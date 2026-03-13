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
            $stmt = $pdo->prepare("UPDATE Sales_Products SET Status = '0' WHERE ProductID = :id");
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["produtos"=>[], "total"=>0, "erro"=>$e->getMessage()]);
            exit;
        }
    }
    public function set_products(array $data) {
        $pdo = $this->connect();

        try {
            $pdo->beginTransaction();


            //inserindo tags
            $stmt = $pdo->prepare(
                "INSERT IGNORE INTO Prod_Tags (TagName) VALUES (?)"
            );

            foreach ($data['tags'] as $tag) {
                $stmt->execute([$tag]);
            }

            //pegando os Ids de tags inseridas
            $tagsIds = [];

            if (!empty($data['tags'])) {

                $placeholders = implode(', ', array_fill(0, count($data['tags']), '?'));

                $stmt = $pdo->prepare(
                    "SELECT TagID
                    FROM Prod_Tags
                    WHERE TagName IN ($placeholders)"
                );

                $stmt->execute($data['tags']);
                $tagsIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
            }


            //inserindo produtos
            $campos = [
                'ProductName',
                'StockQuantity',
                'Price',
                'Size',
                'Color',
                'BathWeight',
                'BathThickness'
            ];

            $params = [
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


            //linkando tags com produtos
            $stmt = $pdo->prepare(
                "INSERT INTO Prod_ProductsTags (ProductID, TagID) VALUES (?, ?)"
            );

            if (!empty($tagsIds)) {
                foreach ($tagsIds as $tag) {
                    $stmt->execute([$productId, $tag]);
                }
            }
            
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
            $stmt->bindParam(":id", $data["id"]);
            $stmt->bindParam(":tamanho", $data["tamanho"]);
            $stmt->bindParam(":cor", $data["cor"]);
            $stmt->bindParam(":peso_banho", $data["peso_banho"]);
            $stmt->bindParam(":milesimos_banho", $data["milesimos_banho"]);

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
            $tags  = $data["tags"]  ?? [];
            $sort  = $data["sort"]  ?? null;
            $limit = $data["limit"] ?? 12;
            $page  = $data["page"]  ?? 0;
            $tamanho = $data["tamanho"] ?? "";
            $cor = $data["cor"] ?? "";
            $peso_banho = $data["peso_banho"] ?? "";
            $milesimos_banho = $data["milesimos_banho"] ?? "";
            $offset = $page * $limit;

            $params = [];
            $whereParts = [];

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

            if (!empty($text)) {
                $whereParts[] = "MATCH(p.ProductName) AGAINST (? IN BOOLEAN MODE)";
                $params[] = $text . "*";
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

            $joinTags = "";
            $havingCount = "";

            if (!empty($tags)) {
                $joinTags = "JOIN Prod_ProductsTags pt ON pt.ProductID = p.ProductID
                            JOIN Prod_Tags t ON t.TagID = pt.TagID";

                $placeholders = implode(", ", array_fill(0, count($tags), "?"));
                $whereParts[] = "t.TagName IN ($placeholders)";
                $params = array_merge($params, $tags);

                $havingCount = "HAVING COUNT(DISTINCT t.TagName) = " . count($tags);
            }

            $where = !empty($whereParts) ? "WHERE " . implode(" AND ", $whereParts) : "";

            $countQuery = "
                SELECT COUNT(*) as total
                FROM (
                    SELECT p.ProductID
                    FROM Sales_Products p
                    $joinTags
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

            $query = "
                SELECT
                    p.ProductID AS id,
                    p.ProductName AS nome,
                    p.StockQuantity AS estoque,
                    p.Price AS preco,
                    p.ImagePath AS img,
                    p.Barcode AS cdb,
                    p.Size AS tamanho,
                    p.Color AS cor,
                    p.BathWeight AS peso_banho,
                    p.BathThickness AS milesimos_banho
                FROM Sales_Products p
                $joinTags
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
}