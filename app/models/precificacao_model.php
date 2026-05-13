<?php

declare(strict_types= 1);
require_once __DIR__ . "/../../config/dbh.config.php";

class precificacao_model extends Dbh {
    public function buscar_produto(string $nome) {
        $pdo = $this->connect();

        try {
            $params = [$nome];

            $query = "
            SELECT 
                p.ProductID AS ref,
                p.ProductName AS nome,
                p.Price AS preco,
                p.StockQuantity AS estoque,
                p.SupID AS codigoExterno,
                p.Weight AS peso,
                p.Thickness AS milesimo,
                p.Metal AS metal,
                p.BathThickness AS milesimoBanho,
                p.BathMetal AS metalBanho,
                p.InputCosts AS custoInsumo,
                p.BruteCost AS custoBruto,
                GROUP_CONCAT(DISTINCT t.TagName) AS categoria
            FROM Sales_Products p
            LEFT JOIN Prod_ProductsTags pt ON pt.ProductID = p.ProductID
            LEFT JOIN Prod_Tags t ON t.TagID = pt.TagID
            WHERE p.ProductName LIKE CONCAT(?, '%')
            AND p.Status = 1
            GROUP BY p.ProductID
            LIMIT 7
            ";

            $stmt = $pdo->prepare($query);
            $stmt->execute(array_reverse($params)); // ajusta ordem

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
        }
    }
    public function criar_produto(array $data) {
        $pdo = $this->connect();

        try {
            $pdo->beginTransaction();

            $query = "
                INSERT INTO Sales_Products (
                    ProductName,
                    SupID,
                    StockQuantity,
                    Price,
                    Weight,
                    Thickness,
                    BathThickness,
                    Metal,
                    BathMetal,
                    InputCosts,
                    BruteCost,
                    Status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
            ";

            $stmt = $pdo->prepare($query);
            $stmt->execute([
                $data["nome"],
                $data["codigoExterno"],
                $data["unidadeEstoque"],
                $data["preco"],
                $data["peso"],
                $data["milesimos"],
                $data["milesimosBanho"],
                $data["metal"],
                $data["metalBanho"],
                $data["custoInsumo"],
                $data["custoBruto"]
            ]);

            $productId = $pdo->lastInsertId();

            $barcode = "2" . str_pad($productId, 11, "0", STR_PAD_LEFT);

            $stmt = $pdo->prepare("
                UPDATE Sales_Products
                SET Barcode = ?
                WHERE ProductID = ?
            ");

            $stmt->execute([$barcode, $productId]);

            if (!empty($data["categoria"])) {
                $tags = is_array($data["categoria"]) ? $data["categoria"] : [$data["categoria"]];

             
                $stmt = $pdo->prepare("INSERT IGNORE INTO Prod_Tags (TagName) VALUES (?)");
                foreach ($tags as $tag) {
                    $stmt->execute([$tag]);
                }

                $placeholders = implode(', ', array_fill(0, count($tags), '?'));
                $stmt = $pdo->prepare("SELECT TagID FROM Prod_Tags WHERE TagName IN ($placeholders)");
                $stmt->execute($tags);
                $tagsIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

                $stmt = $pdo->prepare("INSERT INTO Prod_ProductsTags (ProductID, TagID) VALUES (?, ?)");
                foreach ($tagsIds as $tagId) {
                    $stmt->execute([$productId, $tagId]);
                }
            }

            $pdo->commit();

            return ["success" => true, "id" => $productId];

        } catch (PDOException $e) {
            $pdo->rollBack();
            return ["success" => false, "message" => $e->getMessage()];
        }
    }
    public function atualizar_produto(array $data) {
        $pdo = $this->connect();

        try {
            $pdo->beginTransaction();

            $query = "
                UPDATE Sales_Products SET
                    ProductName = ?,
                    SupID = ?,
                    StockQuantity = ?,
                    Price = ?,
                    Weight = ?,
                    Thickness = ?,
                    BathThickness = ?,
                    Metal = ?,
                    BathMetal = ?,
                    InputCosts = ?,
                    BruteCost = ?
                WHERE ProductID = ?
            ";

            $stmt = $pdo->prepare($query);
            $stmt->execute([
                $data["nome"],
                $data["codigoExterno"],
                $data["unidadeEstoque"],
                $data["preco"],
                $data["peso"],
                $data["milesimos"],
                $data["milesimosBanho"],
                $data["metal"],
                $data["metalBanho"],
                $data["custoInsumo"],
                $data["custoBruto"],
                $data["id"]
            ]);

            if (!empty($data["categoria"])) {
                $tags = is_array($data["categoria"]) ? $data["categoria"] : [$data["categoria"]];

                $stmt = $pdo->prepare("DELETE FROM Prod_ProductsTags WHERE ProductID = ?");
                $stmt->execute([$data["id"]]);

                $stmt = $pdo->prepare("INSERT IGNORE INTO Prod_Tags (TagName) VALUES (?)");
                foreach ($tags as $tag) {
                    $stmt->execute([$tag]);
                }

                $placeholders = implode(', ', array_fill(0, count($tags), '?'));
                $stmt = $pdo->prepare("SELECT TagID FROM Prod_Tags WHERE TagName IN ($placeholders)");
                $stmt->execute($tags);
                $tagsIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

                $stmt = $pdo->prepare("INSERT INTO Prod_ProductsTags (ProductID, TagID) VALUES (?, ?)");
                foreach ($tagsIds as $tagId) {
                    $stmt->execute([$data["id"], $tagId]);
                }
            }

            $pdo->commit();

            return ["success" => true];

        } catch (PDOException $e) {
            $pdo->rollBack();
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }
    public function buscar_metal() {
        $pdo = $this->connect();

        try {
            $query = "
                SELECT
                    MetalID AS id,
                    Name AS nome,
                    ValuePerGram AS valorGrama
                FROM Prod_Metals
                ORDER BY Name ASC
            ";

            $stmt = $pdo->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
        }
    }
    public function criar_metal(string $name, float $value) {
        $pdo = $this->connect();

        try {
            $query = "
                INSERT INTO Prod_Metals (Name, ValuePerGram)
                VALUES (?, ?)
            ";

            $stmt = $pdo->prepare($query);
            $stmt->execute([$name, $value]);

            return [
                "success" => true,
                "id" => $pdo->lastInsertId()
            ];

        } catch (PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
            return [
                "success" => false,
                "message" => "Metal já existe."
            ];
        }
    }
    public function excluir_metal(int $id) {
        $pdo = $this->connect();

        try {
            $query = "
                DELETE FROM Prod_Metals
                WHERE MetalID = ?
            ";

            $stmt = $pdo->prepare($query);
            $stmt->execute([$id]);

            return [
                "success" => true
            ];

        } catch (PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
        }
    }
}

