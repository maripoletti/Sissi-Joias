<?php

declare(strict_types= 1);
require_once __DIR__ . "/../../config/dbh.config.php";

class novavenda_model extends Dbh {
    public function realizar_venda(array $produto, array $cliente, string $pagamento) {
        $pdo = $this->connect();

        try {
            $role   = $_SESSION["role"] ?? null;
            $userId = $_SESSION["user_id"] ?? null;

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

            if ($role != 2 && $userId) {
                $query = "
                    UPDATE Sales_EmployeeProducts
                    SET UsableStock = UsableStock - :qty
                    WHERE ProductID = :pid 
                    AND UserID = :uid
                    AND UsableStock >= :qty
                ";

                $stmt = $pdo->prepare($query);
                $stmt->bindParam(":qty", $produto["quantidade"], PDO::PARAM_INT);
                $stmt->bindParam(":pid", $produto["id"], PDO::PARAM_INT);
                $stmt->bindParam(":uid", $userId, PDO::PARAM_INT);
                $stmt->execute();

                if ($stmt->rowCount() === 0) {
                    $pdo->rollBack();
                    echo "Estoque insuficiente para a funcionária.";
                    return false;
                }
            }

            if ($stmt->rowCount() === 0) {
                $pdo->rollBack();
                echo "Estoque insuficiente ou produto não existe.";
                return false;
            }
            
            $pdo->commit();

            return $orderId;
        } catch (PDOException $e) {
            $pdo->rollBack();
        }
    }
    public function buscar_produto(string $nome) {
        $pdo = $this->connect();

        try {
            $role = $_SESSION["role"] ?? null;
            $userId = $_SESSION["user_id"] ?? null;

            $joinEmployee = "";
            $stockField = "p.StockQuantity";

            $params = [$nome . "*"];

            if ($role != 2 && $userId) {
                $joinEmployee = "JOIN Sales_EmployeeProducts sep 
                                ON sep.ProductID = p.ProductID 
                                AND sep.UserID = ?";
                
                $stockField = "IFNULL(sep.UsableStock, 0)";
                $params[] = $userId;
            }

            $query = "
            SELECT 
                p.ProductID AS id,
                p.ProductName AS nome,
                p.Price AS preco,
                $stockField AS estoque
            FROM Sales_Products p
            $joinEmployee
            WHERE MATCH (p.ProductName) AGAINST (? IN BOOLEAN MODE)
            AND p.Status = 1
            LIMIT 7
            ";

            $stmt = $pdo->prepare($query);
            $stmt->execute(array_reverse($params)); // ajusta ordem

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
        }
    }
}

