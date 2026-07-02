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
                t.TagID AS categoria_id,
                t.TagName AS categoria
                FROM Sales_Products p
            LEFT JOIN Prod_Tags t ON t.TagID = p.TagID
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
                    TagID,
                    Status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
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
                $data["categoria"]
            ]);

            $productId = $pdo->lastInsertId();

            $barcode = "2" . str_pad($productId, 11, "0", STR_PAD_LEFT);

            $stmt = $pdo->prepare("
                UPDATE Sales_Products
                SET Barcode = ?
                WHERE ProductID = ?
            ");

            $stmt->execute([$barcode, $productId]);

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
                    BruteCost = ?,
                    TagID = ?
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
                $data["categoria"],
                $data["id"]
            ]);

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
                WHERE Status = 1
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
                UPDATE Prod_Metals SET
                    Status = 0
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