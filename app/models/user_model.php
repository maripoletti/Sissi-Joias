<?php

declare(strict_types= 1);
require_once __DIR__ ."/../../config/dbh.config.php";

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
                    se.Photo AS foto 
                FROM Sales_Employees se
                LEFT JOIN Sales_Customers sc
                    ON sc.EmployeeID = se.EmployeeID
                LEFT JOIN Sales_Orders so
                    ON so.CustomerID = sc.CustomerID
                    AND so.Status = 1
                WHERE se.UserID = ?
                GROUP BY se.EmployeeID, se.FullName, se.Photo";

            $stmt=$pdo->prepare($query);
            $stmt->execute([$userId]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
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