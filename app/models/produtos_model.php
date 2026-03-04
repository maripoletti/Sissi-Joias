<?php

declare(strict_types=1);
require_once __DIR__ . "/../../config/dbh.config.php";

class Produtos_model extends Dbh {
    public function delete_products($id) {
        $pdo = $this->connect();

        try {
            $query = "DELETE FROM Sales_Products WHERE ProductID = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo "Erro na conexão: " . $e->getMessage();
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
            $placeholders = implode(', ', array_fill(0, count($data['tags']),'?'));

            $stmt = $pdo->prepare(
                "SELECT TagID
                FROM Prod_Tags
                WHERE TagName IN ($placeholders)"
            );
            $stmt->execute($data['tags']);
            $tagsIds = $stmt->fetchAll(PDO::FETCH_COLUMN);


            //inserindo produtos
            $campos = ['ProductName', 'StockQuantity', 'Price'];
            $params = [$data['name'], $data['stock'], $data['price']];
            
            if(!empty($data['photo'])) {
                $campos[] = 'ImagePath';
                $params[] = $data['photo'];
            }

            $placeholders = implode(', ', array_fill(0, count($campos), '?'));

            $query = "INSERT INTO Sales_Products (" . implode(',', $campos) . ") VALUES ($placeholders)";

            $stmt = $pdo->prepare($query);
            $stmt->execute($params);



            $productId = $pdo->lastInsertId();



            //linkando tags com produtos
            $stmt = $pdo->prepare(
                "INSERT INTO Prod_ProductsTags (ProductID, TagID) VALUES (?, ?)"
            );

            foreach ($tagsIds as $tag) {
                $stmt->execute([$productId, $tag]);
            }
            
            $pdo->commit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo "Erro na conexão: " . $e->getMessage();
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
                "Price = :price"
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
            $stmt->bindParam(":id", $data["id"]);

            if (!empty($data["photo"])) {
                $stmt->bindParam(":photo", $data["photo"]);
            }

            $stmt->execute();

            $pdo->commit();
        } catch (PDOException $e) {
            $pdo->rollback();
            echo "Erro na conexão: " . $e->getMessage();
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
            $offset = $page * $limit;

            $params = [];
            $whereParts = [];

            if (!empty($text)) {
                $whereParts[] = "MATCH(p.ProductName, p.Description) AGAINST (? IN BOOLEAN MODE)";
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
                    p.Description AS descricao,
                    p.ImagePath AS img
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
            echo "Erro na conexão: " . $e->getMessage();
        }
    }
}