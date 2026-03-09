<?php

require_once __DIR__ . "/../../config/dbh.config.php";

class comprovante_model extends Dbh {

    public function buscar_venda($id){

        $pdo = $this->connect();

        $query = "
        SELECT 
            p.ProductName,
            p.Price,
            o.Quantity,
            o.Sales,
            o.OrderDate
        FROM Sales_Orders o
        JOIN Sales_Products p ON p.ProductID = o.ProductID
        WHERE o.OrderID = ?
        ";

        $stmt = $pdo->prepare($query);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

}