<?php

declare(strict_types=1);
require_once __DIR__ . "/../../config/dbh.config.php";

class fornecedores_model extends Dbh {

    public function get_fornecedores(array $data){

        $pdo = $this->connect();

        $text  = $data["text"] ?? "";
        $page  = $data["page"] ?? 0;
        $limit = $data["limit"] ?? 10;

        $offset = $page * $limit;

        $params = [];
        $where = "";

        if(!empty($text)){
            $where = "WHERE SupplierName LIKE ? OR CNPJ LIKE ?";
            $params[] = "%$text%";
            $params[] = "%$text%";
        }

        $countQuery = "
        SELECT COUNT(*) as total
        FROM Sales_Suppliers
        $where
        ";

        $stmt = $pdo->prepare($countQuery);
        $stmt->execute($params);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)["total"];

        $query = "
        SELECT
            SupplierID AS id,
            SupplierName AS nome,
            CNPJ AS cnpj,
            Email AS email,
            Phone AS telefone,
            Address AS endereco
        FROM Sales_Suppliers
        $where
        ORDER BY SupplierID DESC
        LIMIT ? OFFSET ?
        ";

        $params[] = (int)$limit;
        $params[] = (int)$offset;

        $stmt = $pdo->prepare($query);

        $i = 1;
        foreach($params as $p){
            $stmt->bindValue($i++, $p, is_int($p) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }

        $stmt->execute();
        $fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            "total" => (int)$total,
            "fornecedores" => $fornecedores
        ];
    }

    public function add_fornecedor(array $data){

        $pdo = $this->connect();

        $query = "
        INSERT INTO Sales_Suppliers
        (SupplierName, CNPJ, Email, Phone, Address)
        VALUES (?, ?, ?, ?, ?)
        ";

        $stmt = $pdo->prepare($query);

        $stmt->execute([
            $data["nome"],
            $data["cnpj"],
            $data["email"],
            $data["telefone"],
            $data["endereco"]
        ]);
    }

    public function update_fornecedor(array $data){

        $pdo = $this->connect();

        $query = "
        UPDATE Sales_Suppliers
        SET
            SupplierName = :nome,
            CNPJ = :cnpj,
            Email = :email,
            Phone = :telefone,
            Address = :endereco
        WHERE SupplierID = :id
        ";

        $stmt = $pdo->prepare($query);

        $stmt->bindParam(":nome",$data["nome"]);
        $stmt->bindParam(":cnpj",$data["cnpj"]);
        $stmt->bindParam(":email",$data["email"]);
        $stmt->bindParam(":telefone",$data["telefone"]);
        $stmt->bindParam(":endereco",$data["endereco"]);
        $stmt->bindParam(":id",$data["id"]);

        $stmt->execute();
    }

    public function delete_fornecedor(int $id){

        $pdo = $this->connect();

        $query = "DELETE FROM Sales_Suppliers WHERE SupplierID = ?";

        $stmt = $pdo->prepare($query);
        $stmt->execute([$id]);
    }

}