<?php

declare(strict_types= 1);
require_once __DIR__ . "/../../config/dbh.config.php";

class topRev_model extends Dbh {
    public function criar_campanha(string $nome, string $descricao, string $inicio, string $fim): array {
        try {
            $query = "INSERT INTO campaigns (name, description, start_date, end_date) 
                    VALUES (:nome, :descricao, :inicio, :fim)";
            $stmt = $this->connect()->prepare($query);
            $stmt->bindParam(":nome", $nome);
            $stmt->bindParam(":descricao", $descricao);
            $stmt->bindParam(":inicio", $inicio);
            $stmt->bindParam(":fim", $fim);
            $stmt->execute();

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
                    FROM campaigns
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
    public function pegar_top_revendedoras(string $mesNome, int $ano, string $campanha): array {
        $meses = [
            "Jan"=>1,"Fev"=>2,"Mar"=>3,"Abr"=>4,
            "Mai"=>5,"Jun"=>6,"Jul"=>7,"Ago"=>8,
            "Set"=>9,"Out"=>10,"Nov"=>11,"Dez"=>12
        ];
        $mes = $meses[$mesNome] ?? null;

        if (!$mes) {
            return [
                "success" => false,
                "message" => "Mês inválido."
            ];
        }

        try {
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
                    MONTH(so.OrderDate) = :mes
                    AND YEAR(so.OrderDate) = :ano
                    AND so.Status = 1
                GROUP BY se.FullName
                ORDER BY total DESC;

            ";

            $stmt = $this->connect()->prepare($sql);
            $stmt->bindParam(":mes", $mes, PDO::PARAM_INT);
            $stmt->bindParam(":ano", $ano, PDO::PARAM_INT);
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
        $sql = "DELETE FROM campaigns WHERE id = :id";
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

