<?php

declare(strict_types= 1);
require_once __DIR__ . "/../../config/dbh.config.php";

class novavenda_model extends Dbh {
    public function realizar_venda(array $produtos, array $cliente, string $pagamento) {
        $pdo = $this->connect();

        try {
            $role   = $_SESSION["role"] ?? null;
            $userId = $_SESSION["user_id"] ?? null;

            $pdo->beginTransaction();

            $query = "UPDATE Sales_Products 
                    SET 
                        StockQuantity = StockQuantity - :qty,
                        Relevancy = Relevancy + :qty
                    WHERE ProductID = :pid AND StockQuantity >= :qty";
            $stmt = $pdo->prepare($query);

            foreach ($produtos as $p) {
                $stmt->execute([
                    ":qty" => $p["quantidade"],
                    ":pid" => $p["id"]
                ]);

                if ($stmt->rowCount() === 0) {
                    $pdo->rollBack();
                    echo "Estoque insuficiente (produto ID {$p["id"]})";
                    return false;
                }
            }

            if ($role != 2 && $userId) {
                $query = "
                    UPDATE Sales_EmployeeProducts
                    SET UsableStock = UsableStock - :qty
                    WHERE ProductID = :pid 
                    AND UserID = :uid
                    AND UsableStock >= :qty
                ";

                $stmt = $pdo->prepare($query);

                foreach ($produtos as $p) {
                    $stmt->execute([
                        ":qty" => $p["quantidade"],
                        ":pid" => $p["id"],
                        ":uid" => $userId
                    ]);

                    if ($stmt->rowCount() === 0) {
                        $pdo->rollBack();
                        echo "Estoque insuficiente para a funcionária (produto ID {$p["id"]})";
                        return false;
                    }
                }

            }

            $query = "SELECT EmployeeID FROM Sales_Employees WHERE UserID = :userid";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":userid", $userId);
            $stmt->execute();
            $employeeId = $stmt->fetchColumn();

            if (!$employeeId) {
                $pdo->rollBack();
                echo "Funcionário não encontrado.";
                return false;
            }

            $query = "INSERT INTO Sales_Customers (EmployeeID, FullName, CPF) VALUES (:employeeid, :name, :cpf)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":employeeid", $employeeId);
            $stmt->bindParam(":name", $cliente["nome"]);
            $stmt->bindParam(":cpf", $cliente["cpf"]);
            $stmt->execute();

            $customerID = $pdo->lastInsertId();

            $stmt = $pdo->prepare("
                SELECT Price FROM Sales_Products WHERE ProductID = ?
            ");

            $total = 0;
            $precos = [];
            foreach ($produtos as $p) {
                $stmt->execute([$p["id"]]);
                $precoReal = $stmt->fetchColumn();

                if ($precoReal === false) {
                    $pdo->rollBack();
                    echo "Produto não encontrado (ID {$p["id"]})";
                    return false;
                }

                $precos[$p["id"]] = $precoReal;
                $total += $p["quantidade"] * $precoReal;
            }

            $query = "INSERT INTO Sales_Orders (CustomerID, OrderDate, Sales, PaymentMethod) VALUES (?, NOW(),?,?)";
            $stmt = $pdo->prepare($query);

            $stmt->execute([$customerID, $total, $pagamento]);

            $orderId = $pdo->lastInsertId();

            $query = "INSERT INTO Sales_OrderItems (OrderID, ProductID, Quantity, Price) VALUES (?,?,?,?)";
            $stmt = $pdo->prepare($query);

            foreach ($produtos as $p) {
                $stmt->execute([
                    $orderId,
                    $p["id"],
                    $p["quantidade"],
                    $precos[$p["id"]]
                ]);
            }
            
            $pdo->commit();

            return $orderId;
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo $e->getMessage();
            return false;
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

