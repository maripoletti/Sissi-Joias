<?php

declare(strict_types= 1);
require_once __DIR__ ."/../../config/dbh.config.php";
require_once __DIR__ . '/../services/rankService.php';

class user_model extends Dbh {
    public function find_role_by_id(string|int $id) {
        $pdo = $this->connect();

        $query = "SELECT RoleID FROM Auth_UserRoles WHERE UserID = :userid"; 
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":userid", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_employee_info(int $userId) {
        $pdo = $this->connect();

        try {
            $query = "SELECT
                    COALESCE(SUM(so.Sales), 0) AS total,
                    se.FullName AS nome,
                    se.Photo AS foto,
                    COALESCE(pending.Level, 'ametista') AS nivel
                FROM Sales_Employees se
                JOIN Auth_Users auth ON auth.UserID = se.UserID
                LEFT JOIN Temp_PendingUsers pending ON pending.Email = auth.Email AND pending.Status = 'aprovado'
                LEFT JOIN Sales_Customers sc
                    ON sc.EmployeeID = se.EmployeeID
                LEFT JOIN Sales_Orders so
                    ON so.CustomerID = sc.CustomerID
                    AND so.Status = 1
                WHERE se.UserID = ?
                GROUP BY se.EmployeeID, se.FullName, se.Photo, pending.Level";

            $stmt=$pdo->prepare($query);
            $stmt->execute([$userId]);

            $info = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$info) return null;
            return array_merge($info, RankService::perfil($info['nivel'], RankService::centavos((string)$info['total'])));
        } catch (PDOException $e) {
            http_response_code(500);
            echo $e->getMessage();
            exit;
        }
    }

    public function set_image(int $userId, string $fotoPath) {
        $pdo = $this->connect();

        try {
            $stmt = $pdo->prepare("UPDATE Sales_Employees SET Photo=? WHERE UserID = ?");
            $stmt->execute([$fotoPath, $userId]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            http_response_code(500);
            echo $e->getMessage();
            exit;
        }
    }
}
