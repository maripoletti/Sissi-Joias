<?php

declare(strict_types=1);
require_once __DIR__ . "/../../config/dbh.config.php";

class produtosRevendedores_model extends Dbh {
    public function pegar_produtos(string|null $produto, string|null $revendedor, int $page, int $limit) {
        $pdo = $this->connect();
        $pdo->beginTransaction();
        try {
            $offset = $limit * ($page - 1);

            $params=[];
            $whereParts=[];

            $whereParts[]= "sp.Status = 1";

            if(!empty($produto)) {
                $whereParts[] = "sp.ProductName LIKE ?";
                $params[] = $produto . "%";
            }

            if(!empty($revendedor)) {
                $whereParts[] = "se.FullName LIKE ?";
                $params[] = $revendedor . "%";
            }

            $where = !empty($whereParts) ? "WHERE " . implode(" AND ", $whereParts) : "";
 
            $from = "FROM Sales_EmployeeProducts sep
                JOIN Sales_Employees se ON sep.UserID = se.UserID
                JOIN Sales_Products sp ON sep.ProductID = sp.ProductID
                LEFT JOIN Sales_Cases sc ON sep.CaseID = sc.CaseID";

            // Pagina os pares maleta/revendedora; os produtos de uma maleta vêm juntos.
            $stmt = $pdo->prepare("SELECT DISTINCT sep.UserID, sep.CaseID
                $from $where ORDER BY sep.CaseID DESC, sep.UserID ASC LIMIT ? OFFSET ?");
            $groupParams = array_merge($params, [$limit + 1, $offset]);
            foreach ($groupParams as $index => $param) {
                $stmt->bindValue($index + 1, $param, is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmt->execute();
            $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $hasMore = count($groups) > $limit;
            if ($hasMore) array_pop($groups);

            $products = [];
            if ($groups) {
                $groupWhere = [];
                foreach ($groups as $group) {
                    $groupWhere[] = '(sep.UserID = ? AND sep.CaseID = ?)';
                    $params[] = (int)$group['UserID'];
                    $params[] = (int)$group['CaseID'];
                }
                $where .= ' AND (' . implode(' OR ', $groupWhere) . ')';

                $query = "SELECT
                sp.ProductID ProdId,
                sp.ProductName produto,
                se.FullName revendedor,
                se.UserID RevId,
                sep.UsableStock quantidade, 
                COALESCE(sep.UnitPrice, sp.Price) preco_revenda,
                COALESCE(sep.OriginalPrice, sp.Price) preco_original,
                sep.CouponPercent desconto,
                sep.SendAt data_envio,
                sep.CaseID AS CaseID,
                sc.Name AS CaseName
            $from
            $where
            ORDER BY sep.CaseID DESC, sep.UserID ASC, sp.ProductName ASC, sp.ProductID ASC";

                $stmt=$pdo->prepare($query);

                $index = 1;
                foreach($params as $param) {
                    $stmt->bindValue($index++, $param, is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR);
                }

                $stmt->execute();

                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            $pdo->commit();
            return ['produtos' => $products, 'pagination' => ['page' => $page, 'limit' => $limit, 'has_more' => $hasMore]];
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }

}
