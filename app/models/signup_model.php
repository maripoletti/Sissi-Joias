<?php

declare(strict_types= 1);
require __DIR__ . "/../../config/dbh.config.php";

class Signup_model extends Dbh {
    public function create_user(int $uid, string $email, string $pwd) {
        $pdo = $this->connect();

        $pdo->beginTransaction();

        try {
            $query = "INSERT INTO Auth_Users"
            $query = "INSERT INTO Auth_Users (Pwd, Email) VALUES (:pwd, :email);";
            $stmt-> bindParam(":pwd", $pwd);
            $stmt-> bindParam(":email", $email);
            $stmt-> execute();

            $UserID = $pdo->lastInsertId();

            $query = "INSERT INTO Sales_Employees (UserID, FullName, Phone) VALUES (:userid, :fullname, :phone);";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":userid", $UserID);
            $stmt->bindParam(":fullname", $name);
            $stmt->bindParam(":phone", $phone);
            $stmt-> execute();

            $query = "INSERT INTO Auth_UserRoles (UserID, RoleID) VALUES (:userid, 2)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":userid", $UserID);
            $stmt->execute();

            $pdo->commit();
        } catch (PDOException $e) {
            $pdo->rollback();
            echo "Erro na conexão: " . $e->getMessage();
        }
    }
}
