<?php
declare(strict_types=1);
require_once __DIR__ . "/../../config/dbh.config.php";
require_once __DIR__ . "/novavenda_model.php";

class vendascan_model extends Dbh {

    public function buscarProduto(string $cdb): ?array {
        $pdo = $this->connect();
        $stmt = $pdo->prepare("SELECT * FROM Sales_Products WHERE Barcode = :cdb LIMIT 1");
        $stmt->execute(['cdb' => $cdb]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);
        return $produto ?: null;
    }

    public function registrarVenda(int $produtoID, int $userID, float $price, int $quantity = 1, string $paymentMethod = 'Scanner'): bool {
        if ($userID !== (int)($_SESSION['user_id'] ?? 0) || $quantity < 1 || !is_finite($price) || $price < 0) return false;
        // Compartilha estoque, itens, transação e promoção de nível com a venda comum.
        $order = (new novavenda_model())->realizar_venda(
            [['id' => $produtoID, 'quantidade' => $quantity, 'preco' => $price]],
            ['nome' => 'Venda por leitor', 'cpf' => ''], $paymentMethod
        );
        return $order !== false;
    }
}
