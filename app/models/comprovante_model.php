<?php

require_once __DIR__ . "/../../config/dbh.config.php";

class comprovante_model extends Dbh {

    public function buscar_venda($id){

        $pdo = $this->connect();

        $query = "
        SELECT 
            p.ProductName,
            p.Price,
            oi.Quantity,
            o.Sales,
            o.OrderDate,
            o.PaymentMethod
        FROM Sales_Orders o
        JOIN Sales_OrderItems oi ON oi.OrderID = o.OrderID
        JOIN Sales_Products p ON p.ProductID = oi.ProductID
        WHERE o.OrderID = ?
        ";

        $stmt = $pdo->prepare($query);
        $stmt->execute([$id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

}