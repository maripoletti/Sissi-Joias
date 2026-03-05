<?php

declare(strict_types= 1);
require_once __DIR__ . "/../../config/dbh.config.php";

class novavenda_model extends Dbh {
    public function buscar_produto(string $nome) {
        $pdo = $this->connect();

        try {
            $query = "SELECT 
            ProductID AS id,
            ProductName AS nome,
            Price AS preco,
            StockQuantity AS estoque
            FROM Sales_Products WHERE MATCH (ProductName) AGAINST (? IN BOOLEAN MODE)
            LIMIT 7";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$nome . "*"]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
        }
    }
}