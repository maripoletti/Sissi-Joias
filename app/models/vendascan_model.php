<?php
declare(strict_types=1);
require_once __DIR__ . "/../../config/dbh.config.php";

class vendascan_model extends Dbh {

    public function buscarProduto(string $cdb): ?array {
        $pdo = $this->connect();
        $stmt = $pdo->prepare("SELECT * FROM Sales_Products WHERE Barcode = :cdb LIMIT 1");
        $stmt->execute(['cdb' => $cdb]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);
        return $produto ?: null;
    }

    public function registrarVenda(int $produtoID, int $userID, float $price, int $quantity = 1, string $paymentMethod = 'Scanner'): bool {
        $pdo = $this->connect();
        $stmt = $pdo->prepare("
            INSERT INTO Sales_Orders (ProductID, UserID, OrderDate, Quantity, Sales, PaymentMethod)
            VALUES (:product_id, :user_id, NOW(), :quantity, :sales, :payment)
        ");
        return $stmt->execute([
            'product_id' => $produtoID,
            'user_id' => $userID,
            'quantity' => $quantity,
            'sales' => $price,
            'payment' => $paymentMethod
        ]);
    }
}