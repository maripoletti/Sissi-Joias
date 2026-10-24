<?php

declare(strict_types= 1);
require_once __DIR__ . "/../../config/dbh.config.php";

class topRev_model extends Dbh {
    public function criar_campanha(string $nome, string $descricao, string $inicio, string $fim): array {
        try {
            $query = "
                INSERT IGNORE INTO Campaigns 
                (name, description, start_date, end_date) 
                VALUES (:nome, :descricao, :inicio, :fim)
            ";
            $stmt = $this->connect()->prepare($query);
            $stmt->bindParam(":nome", $nome);
            $stmt->bindParam(":descricao", $descricao);
            $stmt->bindParam(":inicio", $inicio);
            $stmt->bindParam(":fim", $fim);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                return [
                    "success" => false,
                    "message" => "Essa campanha já existe."
                ];
            }

            return [
                "success" => true,
                "message" => "Campanha criada com sucesso!",
                "id" => $this->connect()->lastInsertId()
            ];
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Erro ao criar campanha: " . $e->getMessage()
            ];
        }
    }
    public function listar_campanhas(): array {
        try {
            $sql = "
                SELECT
                    id,
                    name AS nome,
                    description AS descricao,
                    start_date AS inicio,
                    end_date AS fim,
                    selected
                FROM Campaigns
                ORDER BY start_date DESC
            ";
            $stmt = $this->connect()->query($sql);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $rows;
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Erro ao listar campanhas: " . $e->getMessage()
            ];
        }
    }
    public function remover_campanha(int $id): array {
        try {
            $sql = "DELETE FROM Campaigns WHERE id = :id";
            $stmt = $this->connect()->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();

            return [
                "success" => true,
                "message" => "Campanha removida com sucesso!"
            ];
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Erro ao remover campanha: " . $e->getMessage()
            ];
        }
    }
    public function atualizar_campanha(
        int $id,
        string $nome,
        string $descricao,
        string $inicio,
        string $fim
    ): array {

        try {
            $sql = "
                UPDATE Campaigns
                SET
                    name = :nome,
                    description = :descricao,
                    start_date = :inicio,
                    end_date = :fim
                WHERE id = :id
            ";

            $stmt = $this->connect()->prepare($sql);

            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->bindParam(":nome", $nome);
            $stmt->bindParam(":descricao", $descricao);
            $stmt->bindParam(":inicio", $inicio);
            $stmt->bindParam(":fim", $fim);

            $stmt->execute();

            return [
                "success" => true
            ];

        } catch (PDOException $e) {

            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    public function selecionar_campanha(int $id): array {
        $pdo = $this->connect();

        try {
            $pdo->beginTransaction();

            $pdo->exec("
                UPDATE Campaigns
                SET selected = 0
            ");

            $stmt = $pdo->prepare("
                UPDATE Campaigns
                SET selected = 1
                WHERE id = ?
            ");

            $stmt->execute([$id]);

            $pdo->commit();

            return [
                "success" => true,
                "message" => "Campanha selecionada com sucesso!"
            ];
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Erro ao selecionar campanha: " . $e->getMessage()
            ];
        }
    }
}

