<?php

declare(strict_types= 1);
require_once __DIR__ . "/../../config/dbh.config.php";

class novavenda_model extends Dbh {
    public function realizar_venda(array $produto, array $cliente, string $pagamento) {
        $pdo = $this->connect();

        try {
            $pdo->beginTransaction();

            $query = "SELECT EmployeeID FROM Sales_Employees WHERE UserID = :userid";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":userid", $_SESSION["user_id"]);
            $stmt->execute();
            $employeeId = $stmt->fetchColumn();

            $query = "INSERT INTO Sales_Customers (EmployeeID, FullName, CPF) VALUES (:employeeid, :name, :cpf)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":employeeid", $employeeId);
            $stmt->bindParam(":name", $cliente["nome"]);
            $stmt->bindParam(":cpf", $cliente["cpf"]);
            $stmt->execute();

            $customerID = $pdo->lastInsertId();

            $query = "INSERT INTO Sales_Orders (ProductID, CustomerID, OrderDate, Quantity, Sales, PaymentMethod) VALUES (?,?, NOW(),?,?,?)";
            $stmt = $pdo->prepare($query);

            $total = ($produto["quantidade"] * $produto["preco"]);
            $stmt->execute([$produto["id"], $customerID, $produto["quantidade"], $total, $pagamento]);

            $orderId = $pdo->lastInsertId();

            $query = "UPDATE Sales_Products 
                    SET 
                        StockQuantity = StockQuantity - :qty,
                        Relevancy = Relevancy + :qty
                    WHERE ProductID = :pid AND StockQuantity >= :qty";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":qty", $produto["quantidade"], PDO::PARAM_INT);
            $stmt->bindParam(":pid", $produto["id"], PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                throw new Exception("Estoque insuficiente.");
            }
            
            $pdo->commit();

            return $orderId;
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo "Erro na conexão: " . $e->getMessage();
        }
    }
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