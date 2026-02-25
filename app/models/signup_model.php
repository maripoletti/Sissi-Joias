<?php

declare(strict_types= 1);
require __DIR__ . "/../../config/dbh.config.php";

class Signup_model extends Dbh {
    public function send_request(string $email, string $pwd, string $name, string $phone) {
        $pdo = $this->connect();

        try {
            $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT, [ 'cost' => 12 ]);
            $query = "INSERT INTO Temp_PendingUsers (FullName, Email, Pwd, Phone) VALUES (:fullname, :email, :pwd, :phone);";
            $stmt = $pdo->prepare($query);
            $stmt-> bindParam(":fullname", $name);
            $stmt-> bindParam(":email", $email);
            $stmt-> bindParam(":pwd", $hashedPwd);
            $stmt-> bindParam(":phone", $phone);
            $stmt-> execute();
        } catch (PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
        }
    }
    public function reject_user(int $tempid) {
        $pdo = $this->connect();

        try {
            $query = "DELETE FROM Temp_PendingUsers WHERE PendUserID = :userid";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":userid", $tempid);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Erro na conexão: ". $e->getMessage();
        }
    }
    public function create_user(string $email, string $pwd, string $name, string $phone) {
        $pdo = $this->connect();

        $pdo->beginTransaction();

        try {
            $query = "INSERT INTO Auth_Users (Pwd, Email) VALUES (:pwd, :email);";
            $stmt = $pdo->prepare($query);
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

            $query = "INSERT INTO Auth_UserRoles (UserID, RoleID) VALUES (:userid, 1)";
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
