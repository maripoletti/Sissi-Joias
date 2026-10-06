<?php

declare(strict_types=1);

require_once __DIR__ . "/../../config/dbh.config.php";

class Categorias_model extends Dbh {

    public function listar_categorias(): array {
        $pdo = $this->connect();
        $stmt = $pdo->prepare("
            SELECT
                TagID AS id,
                TagName AS nome
            FROM Prod_Tags
            ORDER BY TagName
        ");

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function adicionar_categoria(string $nome): bool {
        $pdo = $this->connect();
        $stmt = $pdo->prepare("
            INSERT INTO Prod_Tags (TagName)
            VALUES (?)
        ");

        return $stmt->execute([$nome]);
    }

    public function editar_categoria(int $id, string $nome): bool {
        $pdo = $this->connect();
        $stmt = $pdo->prepare("
            UPDATE Prod_Tags
            SET TagName = ?
            WHERE TagID = ?
        ");

        return $stmt->execute([$nome, $id]);
    }

    public function remover_categoria(int $id): bool {
        $pdo = $this->connect();

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                UPDATE Sales_Products
                SET TagID = NULL
                WHERE TagID = ?
            ");
            $stmt->execute([$id]);

            $stmt = $pdo->prepare("
                DELETE FROM Prod_Tags
                WHERE TagID = ?
            ");
            $stmt->execute([$id]);

            $pdo->commit();
            return true;

        } catch (PDOException $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "erro" => $e->getMessage()
            ]);
            exit;
        }
    }
}