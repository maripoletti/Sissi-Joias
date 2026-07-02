<?php

declare(strict_types=1);
require_once __DIR__ . "/../../config/dbh.config.php";

class Produtos_model extends Dbh {
    public function import_product(array $data) {
        $pdo = $this->connect();

        try {
            $stmt = $pdo->prepare("
                SELECT ProductID, StockQuantity, Status
                FROM Sales_Products
                WHERE SupID = ?
                LIMIT 1
            ");

            $stmt->execute([$data["code"]]);
            $prod = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($prod) {
                if ($prod["Status"] == "1") {
                    $stmt = $pdo->prepare("
                        UPDATE Sales_Products
                        SET StockQuantity = StockQuantity + ?
                        WHERE ProductID = ?
                    ");

                    $stmt->execute([$data["stock"], $prod["ProductID"]]);

                } else {
                    $stmt = $pdo->prepare("
                        UPDATE Sales_Products
                        SET
                            Status = 1,
                            StockQuantity = ?,
                            Price = ?
                        WHERE ProductID = ?
                    ");

                    $stmt->execute([
                        $data["stock"],
                        $data["price"],
                        $prod["ProductID"]
                    ]);
                }

            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO Sales_Products
                    (ProductName, StockQuantity, Price, SupID)
                    VALUES (?, ?, ?, ?)
                ");

                $stmt->execute([
                    $data["name"],
                    $data["stock"],
                    $data["price"],
                    $data["code"]
                ]);

                $productId = $pdo->lastInsertId();

                $barcode = "2" . str_pad($productId, 11, "0", STR_PAD_LEFT);

                $stmt = $pdo->prepare("
                    UPDATE Sales_Products
                    SET Barcode = ?
                    WHERE ProductID = ?
                ");

                $stmt->execute([$barcode, $productId]);
            }

        } catch (PDOException $e) {

            http_response_code(500);
            echo $e->getMessage();
            exit;

        }
    }
    public function delete_products(int $id) {
        $pdo = $this->connect();

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("UPDATE Sales_Products SET Status = '0' WHERE ProductID = :id");
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $stmt = $pdo->prepare("DELETE FROM Sales_EmployeeProducts WHERE ProductID = :id");
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();

            $pdo->commit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(["produtos"=>[], "total"=>0, "erro"=>$e->getMessage()]);
            exit;
        }
    }
    public function set_products(array $data) {
        $pdo = $this->connect();

        try {
            $pdo->beginTransaction();

            //inserindo produtos
            $campos = [
                'TagID',
                'ProductName',
                'StockQuantity',
                'Price',
                'Size',
                'Color',
                'BathWeight',
                'BathThickness'
            ];

            $params = [
                $data['tag'],
                $data['name'],
                $data['stock'],
                $data['price'],
                $data['tamanho'],
                $data['cor'],
                $data['peso_banho'],
                $data['milesimos_banho']
            ];
            
            if(!empty($data['photo'])) {
                $campos[] = 'ImagePath';
                $params[] = $data['photo'];
            }

            $placeholders = implode(', ', array_fill(0, count($campos), '?'));

            $query = "INSERT INTO Sales_Products (" . implode(',', $campos) . ") VALUES ($placeholders)";

            $stmt = $pdo->prepare($query);
            $stmt->execute($params);



            $productId = $pdo->lastInsertId();

            //adicionando código de barras

            $barcode = "2" . str_pad($productId, 11, "0", STR_PAD_LEFT);

            $stmt = $pdo->prepare(
                "UPDATE Sales_Products SET Barcode = ? WHERE ProductID = ?"
            );

            $stmt->execute([$barcode, $productId]);
            
            $pdo->commit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(["produtos"=>[], "total"=>0, "erro"=>$e->getMessage()]);
            exit;
        }
    }
    public function update_products(array $data) {
        $pdo = $this->connect();

        try {
            $pdo->beginTransaction();

            $query = 
            "DELETE FROM Prod_ProductsTags
            WHERE ProductID = :id;";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":id", $data["id"]);
            $stmt->execute();
            
            $campos = [
                "ProductName = :name",
                "Price = :price",
                "StockQuantity = :stock",
                "TagID = :tagID",
                "Size = :tamanho",
                "Color = :cor",
                "BathWeight = :peso_banho",
                "BathThickness = :milesimos_banho"
            ];

            if (!empty($data["photo"])) {
                $campos[] = "ImagePath = :photo";
            }

            $query = "UPDATE Sales_Products SET "
                    . implode(", ", $campos) .
                    " WHERE ProductID = :id";

            $stmt = $pdo->prepare($query);

            $stmt->bindParam(":name", $data["name"]);
            $stmt->bindParam(":price", $data["price"]);
            $stmt->bindParam(":stock", $data["stock"]);
            $stmt->bindParam(":tagID", $data["tag"], $data["tag"] === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(":id", $data["id"]);
            $stmt->bindParam(":tamanho", $data["tamanho"]);
            $stmt->bindParam(":cor", $data["cor"]);
            $stmt->bindValue(":peso_banho", $data["peso_banho"], $data["peso_banho"] === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindValue(":milesimos_banho", $data["milesimos_banho"], $data["milesimos_banho"] === null ? PDO::PARAM_NULL : PDO::PARAM_INT);

            if (!empty($data["photo"])) {
                $stmt->bindParam(":photo", $data["photo"]);
            }

            $stmt->execute();

            $pdo->commit();
        } catch (PDOException $e) {
            $pdo->rollback();
            http_response_code(500);
            echo json_encode(["produtos"=>[], "total"=>0, "erro"=>$e->getMessage()]);
            exit;
        }
    }

    public function get_products(array $data) {
        $pdo = $this->connect();

        try {
            $min   = $data["min"]   ?? null;
            $max   = $data["max"]   ?? null;
            $text  = $data["text"]  ?? "";
            $tag  = $data["tag"]  ?? null;
            $sort  = $data["sort"]  ?? null;
            $limit = $data["limit"] ?? 12;
            $page  = $data["page"]  ?? 0;
            $tamanho = $data["tamanho"] ?? "";
            $cor = $data["cor"] ?? "";
            $peso_banho = $data["peso_banho"] ?? null;
            $milesimos_banho = $data["milesimos_banho"] ?? null;
            $UserID = $_SESSION["user_id"] ?? null;
            $role   = $_SESSION["role"] ?? null;
            $offset = $page * $limit;

            $params = [];
            $whereParts = [];

            $joinEmployee = "";

            if ($role != 2 && $UserID) {
                $joinEmployee = "JOIN Sales_EmployeeProducts sep 
                                ON sep.ProductID = p.ProductID 
                                AND sep.UserID = ?";
                
                $params[] = $UserID;
            }

            $whereParts[] = "p.Status = 1";

            if (!empty($tamanho)) {
                $whereParts[] = "p.Size = ?";
                $params[] = $tamanho;
            }

            if (!empty($cor)) {
                $whereParts[] = "p.Color = ?";
                $params[] = $cor;
            }

            if (!empty($peso_banho)) {
                $whereParts[] = "p.BathWeight = ?";
                $params[] = $peso_banho;
            }

            if (!empty($milesimos_banho)) {
                $whereParts[] = "p.BathThickness = ?";
                $params[] = $milesimos_banho;
            }

            if ($tag !== null) {
                $whereParts[] = "p.TagID = ?";
                $params[] = $tag;
            }

            if (!empty($text)) {
                $whereParts[] = "p.ProductName LIKE ?";
                $params[] = $text . "%";
            }

            if ($min !== null && $max !== null) {
                $whereParts[] = "p.Price BETWEEN ? AND ?";
                $params[] = $min;
                $params[] = $max;
            } elseif ($min !== null) {
                $whereParts[] = "p.Price >= ?";
                $params[] = $min;
            } elseif ($max !== null) {
                $whereParts[] = "p.Price <= ?";
                $params[] = $max;
            }

            $havingCount = "";
            

            $where = !empty($whereParts) ? "WHERE " . implode(" AND ", $whereParts) : "";

            $countQuery = "
                SELECT COUNT(*) as total
                FROM (
                    SELECT p.ProductID
                    FROM Sales_Products p
                    $joinEmployee
                    $where
                    GROUP BY p.ProductID
                    $havingCount
                ) as sub
            ";

            $countStmt = $pdo->prepare($countQuery);

            $index = 1;
            foreach ($params as $param) {
                $countStmt->bindValue($index++, $param, is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }

            $countStmt->execute();
            $total = $countStmt->fetch(PDO::FETCH_ASSOC)["total"];

            $orderBy = "";

            switch ($sort) {
                case "relevancia":
                    $orderBy = "ORDER BY p.Relevancy DESC";
                    break;
                case "menor":
                    $orderBy = "ORDER BY p.Price ASC";
                    break;
                case "maior":
                    $orderBy = "ORDER BY p.Price DESC";
                    break;
                case "az":
                    $orderBy = "ORDER BY p.ProductName ASC";
                    break;
                case "za":
                    $orderBy = "ORDER BY p.ProductName DESC";
                    break;
            }

            $stockField = ($role == 2)
                ? "p.StockQuantity"
                : "sep.UsableStock";


            $query = "
                SELECT
                    p.ProductID AS id,
                    p.ProductName AS nome,
                    $stockField AS estoque,
                    p.Price AS preco,
                    p.ImagePath AS img,
                    p.Barcode AS cdb,
                    p.Size AS tamanho,
                    p.Color AS cor,
                    p.BathWeight AS peso_banho,
                    p.BathThickness AS milesimos_banho,
                    t.TagID AS categoria_id,
                    t.TagName AS categoria
                FROM Sales_Products p
                LEFT JOIN Prod_Tags t
                    ON t.TagID = p.TagID
                $joinEmployee
                $where
                GROUP BY p.ProductID
                $havingCount
                $orderBy
                LIMIT ? OFFSET ?
            ";

            $params[] = (int)$limit;
            $params[] = (int)$offset;

            $stmt = $pdo->prepare($query);

            $index = 1;
            foreach ($params as $param) {
                $stmt->bindValue($index++, $param, is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }

            $stmt->execute();
            $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                "total" => (int)$total,
                "produtos" => $produtos
            ];

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["produtos"=>[], "total"=>0, "erro"=>$e->getMessage()]);
            exit;
        }
    }

    public function get_employees() {
        $pdo = $this->connect();
        try {
            $stmt = $pdo->prepare("SELECT UserID AS id, FullName AS nome FROM Sales_Employees");
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            http_response_code(500);
            echo $e->getMessage();
            exit;
        }
    }

    public function send_to_employee($UserID, $ProductID, $StockSent, $CaseID) {
        $pdo = $this->connect();

        try {
            $query = "
                INSERT INTO Sales_EmployeeProducts 
                (UserID, ProductID, UsableStock, CaseID)
                VALUES (?, ?, ?, ?)
            ";

            $stmt = $pdo->prepare($query);
            $stmt->execute([$UserID, $ProductID, $StockSent, $CaseID]);

            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            http_response_code(500);
            echo $e->getMessage();
            exit;
        }
    }

    public function create_case(string $name): int {
        $pdo = $this->connect();

        try {
            $stmt = $pdo->prepare("
                INSERT INTO Sales_Cases (Name)
                VALUES (?)
            ");

            $stmt->execute([$name]);

            return (int)$pdo->lastInsertId();
        } catch (PDOException $e) {
            http_response_code(500);
            echo $e->getMessage();
            exit;
        }
    }

    public function add_product_to_case(int $CaseID, int $ProductID, int $Quantity): bool {
        $pdo = $this->connect();

        try {
            $stmt = $pdo->prepare("
                INSERT INTO Sales_CasesProducts
                (CaseID, ProductID, Quantity)
                VALUES (?, ?, ?)
            ");

            return $stmt->execute([$CaseID, $ProductID, $Quantity]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo $e->getMessage();
            exit;
        }    
    }

    public function remove_products($ProductID, $UserID) {
        $pdo = $this->connect();
        try {
            $query = "
            DELETE FROM Sales_EmployeeProducts
            WHERE ProductID = ? AND UserID = ?";

            $stmt=$pdo->prepare($query);
            $stmt->execute([$ProductID, $UserID]);

            return $stmt->rowCount();
        } catch (PDOException $e) {
            http_response_code(500);
            echo $e->getMessage();
            exit;
        }
    }

    public function update_employee_products($ProductID, $UserID, $NewStock) {
        $pdo = $this->connect();
        try{
            $query = "UPDATE Sales_EmployeeProducts SET UsableStock = ? 
            WHERE ProductID = ? AND UserID = ?";

            $stmt=$pdo->prepare($query);
            $stmt->execute([$NewStock, $ProductID, $UserID]);

            return $stmt->rowCount();
        } catch (PDOException $e) {
            http_response_code(500);
            echo $e->getMessage();
            exit;
        }
    }

    public function listar_maletas() {
        $pdo = $this->connect();

        try {
            $query = "SELECT
                        CaseID,
                        Name AS CaseName
                    FROM Sales_Cases            
            ";
            $stmt = $pdo->prepare($query);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Erro ao listar campanhas: " . $e->getMessage()
            ];
        }
    }
}