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
            $sql = "SELECT id, name AS nome, description AS descricao, 
                           start_date AS inicio, end_date AS fim
                    FROM Campaigns
                    ORDER BY start_date DESC";
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
    public function pegar_top_revendedoras(int $campanhaId = 0): array {
        try {

            $whereCampanha = "";

            if ($campanhaId !== 0) {

                $sqlCampanha = "
                    SELECT start_date, end_date
                    FROM Campaigns
                    WHERE id = :id
                    LIMIT 1
                ";

                $stmtCampanha = $this->connect()->prepare($sqlCampanha);
                $stmtCampanha->bindParam(":id", $campanhaId, PDO::PARAM_INT);
                $stmtCampanha->execute();

                $campanha = $stmtCampanha->fetch(PDO::FETCH_ASSOC);

                if (!$campanha) {
                    return [
                        "success" => false,
                        "message" => "Campanha não encontrada."
                    ];
                }

                $whereCampanha = "
                    AND so.OrderDate BETWEEN :inicio AND :fim
                ";
            }

            $sql = "
                SELECT 
                    se.FullName AS nome,
                    SUM(so.Sales) AS total
                FROM Sales_Orders so

                INNER JOIN Sales_Customers sc 
                    ON so.CustomerID = sc.CustomerID

                INNER JOIN Sales_Employees se 
                    ON sc.EmployeeID = se.EmployeeID

                WHERE 
                    so.Status = 1
                    $whereCampanha

                GROUP BY se.FullName
                ORDER BY total DESC
            ";

            $stmt = $this->connect()->prepare($sql);

            if ($campanhaId !== 0) {
                $stmt->bindParam(":inicio", $campanha["start_date"]);
                $stmt->bindParam(":fim", $campanha["end_date"]);
            }

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Erro na query: " . $e->getMessage()
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
}

