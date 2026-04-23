<?php

declare(strict_types=1);
require_once __DIR__ . "/../../config/dbh.config.php";

class produtosRevendedores_model extends Dbh {
    public function pegar_produtos(string|null $produto, string|null $revendedor, int $page, int $limit) {
        $pdo = $this->connect();
        try {
            $offset = $limit * ($page - 1);

            $params=[];
            $whereParts=[];

            $whereParts[]= "sp.Status = 1";

            if(!empty($produto)) {
                $whereParts[] = "MATCH(sp.ProductName) AGAINST (? IN BOOLEAN MODE)";
                $params[] = $produto . "*";
            }

            if(!empty($revendedor)) {
                $whereParts[] = "MATCH(se.FullName) AGAINST (? IN BOOLEAN MODE)";
                $params[] = $revendedor . "*";
            }

            $where = !empty($whereParts) ? "WHERE " . implode(" AND ", $whereParts) : "";
 
            $query = "SELECT 
                sp.ProductName produto,
                se.FullName revendedor,
                sep.UsableStock quantidade, 
                sp.Price preco_revenda,
                sep.SendAt data_envio
            FROM Sales_EmployeeProducts sep
            LEFT JOIN Sales_Employees se
            ON sep.UserID = se.UserID
            LEFT JOIN Sales_Products sp
            ON sep.ProductID = sp.ProductID
            $where
            LIMIT ? OFFSET ?";

            $params[] = (int)$limit;
            $params[] = (int)$offset;

            $stmt=$pdo->prepare($query);

            $index = 1;
            foreach($params as $param) {
                $stmt->bindValue($index++, $param, is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [
                "erro" => $e->getMessage()
            ];
        }
    }
}
