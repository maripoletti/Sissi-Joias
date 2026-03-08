<?php

declare(strict_types= 1);
require_once __DIR__ . "/../../config/dbh.config.php";

class impressoras_model extends Dbh {
    public function get_printer(int $id) {
        $pdo = $this->connect();

        try {
            $query = "SELECT ip, port FROM Sales_Printers WHERE PrinterID = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
        }
    }
    public function new_status_printer(int $id, string $status) {
        $pdo = $this->connect();
        try {
            $query = "UPDATE Sales_Printers SET Status = :status WHERE PrinterID = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->bindParam(":status", $status, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro na conexão: ". $e->getMessage();
        }
    }
    public function delete_printer(int $id) {
        $pdo = $this->connect();
        try {
            $pdo->beginTransaction();

            $query = "DELETE FROM Print_PrinterTags WHERE PrinterID = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            $query = "DELETE FROM Sales_Printers WHERE PrinterID = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            $pdo->commit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo "Erro na conexão: " . $e->getMessage();
        }
    }
    public function get_printers() {
        $pdo = $this->connect();

        try {
            $query = 
            "SELECT
                p.PrinterID AS id,
                p.PrinterName AS name,
                p.Model AS model,
                p.Type AS type,
                p.Connection AS conn,
                p.Ip AS ip,
                p.Sector AS sector,
                p.Status AS status,
                GROUP_CONCAT(t.TagName) AS caps
            FROM Sales_Printers p
            LEFT JOIN Print_PrinterTags pt ON pt.PrinterID = p.PrinterID
            LEFT JOIN Print_Tags t ON t.TagID = pt.TagID
            GROUP BY p.PrinterID";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro na conexão: ". $e->getMessage();
        }
    }
}