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
                    SUM(so.Sales) AS total,
                    se.FullName AS nome,
                    se.Photo AS foto 
                FROM Sales_Orders so
                RIGHT JOIN Sales_Customers sc
                ON so.CustomerID = sc.CustomerID
                LEFT JOIN Sales_Employees se
                ON sc.EmployeeID = se.EmployeeID
                WHERE so.Status = 1 AND se.UserID = ?;";

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