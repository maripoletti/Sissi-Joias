<?php

declare(strict_types=1);
require_once __DIR__ . "/../../config/dbh.config.php";

class relatorios_model extends Dbh {

    public function buscar_relatorios(){

        $pdo = $this->connect();

        try {


            $stmt = $pdo->prepare("
                SELECT
                    SUM(Sales) AS total,
                    COUNT(*) AS qtd_vendas
                FROM Sales_Orders
            ");
            $stmt->execute();
            $totais = $stmt->fetch(PDO::FETCH_ASSOC);




            $stmt = $pdo->prepare("
                SELECT 
                    SUM(so.Sales) AS valor,
                    se.FullName AS nome
                FROM Sales_Orders so
                LEFT JOIN Sales_Customers sc
                    ON so.CustomerID = sc.CustomerID
                LEFT JOIN Sales_Employees se
                    ON sc.EmployeeID = se.EmployeeID
                WHERE se.FullName IS NOT NULL
                GROUP BY se.EmployeeID
                ORDER BY valor DESC
                LIMIT 4
            ");
            $stmt->execute();
            $vendedoras = $stmt->fetchAll(PDO::FETCH_ASSOC);



            $stmt = $pdo->prepare("
                SELECT
                    SUM(Sales) AS valor,
                    PaymentMethod AS tipo
                FROM Sales_Orders
                GROUP BY PaymentMethod
            ");
            $stmt->execute();
            $pagamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);



            $stmt = $pdo->prepare("
                SELECT COUNT(*) AS vendedoras_ativas
                FROM Sales_Employees
            ");
            $stmt->execute();
            $ativas = $stmt->fetch(PDO::FETCH_ASSOC);



            $stmt = $pdo->prepare("
                SELECT COUNT(*) AS total_pecas
                FROM Sales_Products
            ");
            $stmt->execute();
            $pecas = $stmt->fetch(PDO::FETCH_ASSOC);




            $stmt = $pdo->prepare("
                SELECT SUM(StockQuantity) AS total_unidades
                FROM Sales_Products
            ");
            $stmt->execute();
            $unidades = $stmt->fetch(PDO::FETCH_ASSOC);




            $stmt = $pdo->prepare("
                SELECT COUNT(*) AS alertas
                FROM Sales_Products
                WHERE StockQuantity < 3
            ");
            $stmt->execute();
            $alertas = $stmt->fetch(PDO::FETCH_ASSOC);




            $stmt = $pdo->prepare("
                SELECT SUM(Price * StockQuantity) AS valor_estoque
                FROM Sales_Products
            ");
            $stmt->execute();
            $valorEstoque = $stmt->fetch(PDO::FETCH_ASSOC);




            $stmt = $pdo->prepare("
                SELECT 
                    p.ProductName AS nome,
                    SUM(so.Quantity) AS vendidos
                FROM Sales_Orders so
                JOIN Sales_Products p ON so.ProductID = p.ProductID
                GROUP BY p.ProductID
                ORDER BY vendidos DESC
                LIMIT 6
            ");
            $stmt->execute();
            $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);



            return [

                "total" => (float)($totais["total"] ?? 0),
                "qtd_vendas" => (int)($totais["qtd_vendas"] ?? 0),

                "vendedoras" => $vendedoras,
                "pagamentos" => $pagamentos,

                "vendedoras_ativas" => (int)($ativas["vendedoras_ativas"] ?? 0),

                "estoque" => [
                    "total_pecas" => (int)($pecas["total_pecas"] ?? 0),
                    "total_unidades" => (int)($unidades["total_unidades"] ?? 0),
                    "alertas" => (int)($alertas["alertas"] ?? 0),
                    "valor" => (float)($valorEstoque["valor_estoque"] ?? 0)
                ],

                "produtos_mais_vendidos" => $produtos

            ];

        } catch (PDOException $e) {

            return [
                "erro" => $e->getMessage()
            ];

        }

    }

}