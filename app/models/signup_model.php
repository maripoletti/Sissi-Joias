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
    public function create_user_from_pending(int $PendUserID) {
        $pdo = $this->connect();

        $pdo->beginTransaction();

        try {
            $query = "SELECT * FROM Temp_PendingUser WHERE PendUserID = :id;";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":id", $PendUserID);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!$user) {
                throw new Exception("Usuário não encontrado");
            };

            $query = "INSERT INTO Auth_Users (Pwd, Email) VALUES (:pwd, :email);";
            $stmt = $pdo->prepare($query);
            $stmt-> bindParam(":pwd", $user['Pwd']);
            $stmt-> bindParam(":email", $user['Email']);
            $stmt-> execute();

            $UserID = $pdo->lastInsertId();

            $query = "INSERT INTO Sales_Employees (UserID, FullName, Phone) VALUES (:userid, :fullname, :phone);";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":userid", $UserID);
            $stmt->bindParam(":fullname", $user['FullName']);
            $stmt->bindParam(":phone", $user['Phone']);
            $stmt-> execute();

            $query = "INSERT INTO Auth_UserRoles (UserID, RoleID) VALUES (:userid, 1)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":userid", $UserID);
            $stmt->execute();

            $query = "DELETE FROM Temp_PendingUsers WHERE PendUserID = :userid";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":userid", $PendUserID);
            $stmt->execute();

            $pdo->commit();
        } catch (PDOException $e) {
            $pdo->rollback();
            echo "Erro na conexão: " . $e->getMessage();
        }
    }
}
