<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/dbh.config.php';

class Cupons_model extends Dbh {
    public function listar(int $user, bool $admin): array {
        $pdo = $this->connect();
        $stmt = $pdo->prepare('SELECT CouponID AS id, DiscountPercent AS desconto, Quantity AS quantidade
            FROM Sales_EmployeeCoupons WHERE UserID = ?' . ($admin ? '' : ' AND Quantity > 0') . ' ORDER BY DiscountPercent');
        $stmt->execute([$user]);
        $cupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = $pdo->prepare('SELECT ProductName AS produto, DiscountPercent AS desconto, Quantity AS quantidade,
            OriginalPrice AS original, FinalPrice AS final, CreatedAt AS usado_em
            FROM Sales_CouponUses WHERE UserID = ? ORDER BY CouponUseID DESC');
        $stmt->execute([$user]);
        return ['success' => true, 'cupons' => $cupons, 'usados' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
    }

    public function adicionar(int $user, int $percent, int $quantity): void {
        $pdo = $this->connect();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('SELECT UserID FROM Sales_Employees WHERE UserID = ? FOR UPDATE');
            $stmt->execute([$user]);
            if (!$stmt->fetchColumn()) throw new InvalidArgumentException('Revendedora não encontrada.');
            $stmt = $pdo->prepare('SELECT CouponID, Quantity FROM Sales_EmployeeCoupons WHERE UserID = ? AND DiscountPercent = ? FOR UPDATE');
            $stmt->execute([$user, $percent]);
            $coupon = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($quantity + (int)($coupon['Quantity'] ?? 0) > 10000) {
                throw new InvalidArgumentException('O limite é de 10.000 cupons por percentual.');
            }
            $stmt = $pdo->prepare('INSERT INTO Sales_EmployeeCoupons (UserID, DiscountPercent, Quantity) VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE Quantity = Quantity + VALUES(Quantity)');
            $stmt->execute([$user, $percent, $quantity]);
            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function ajustar(int $id, ?int $delta): void {
        $pdo = $this->connect();
        if ($delta === null) {
            // Remoção zera o saldo; o histórico de usos permanece disponível.
            $stmt = $pdo->prepare('UPDATE Sales_EmployeeCoupons SET Quantity = 0 WHERE CouponID = ?');
            $stmt->execute([$id]);
        } else {
            $stmt = $pdo->prepare('UPDATE Sales_EmployeeCoupons SET Quantity = Quantity + ?
                WHERE CouponID = ? AND Quantity + ? BETWEEN 0 AND 10000');
            $stmt->execute([$delta, $id, $delta]);
            if (!$stmt->rowCount()) throw new DomainException('Saldo indisponível. Atualize a lista de cupons.');
        }
    }
}
