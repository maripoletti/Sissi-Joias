<?php

declare(strict_types= 1);
require_once __DIR__ . "/../../config/dbh.config.php";
class cadastroimpressora_model extends Dbh {
    public function create_printer(array $data) {
        $pdo = $this->connect();
        
        try {
            $query = "INSERT INTO Sales_Printers (PrinterName, Type, Connection, Ip, Port, Sector, Brand, Model, Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                $data["nome"],
                $data["tipo"],
                $data["conexao"],
                $data["ip"],
                $data["porta"],
                $data["localizacao"],
                $data["marca"],
                $data["modelo"],
                $data["status"]
            ]);
        } catch (PDOException $e) {
            echo "Erro na conexão: ". $e->getMessage();
        }
    }
}