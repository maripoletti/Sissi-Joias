<?php

declare(strict_types= 1);
require_once __DIR__ . "/../../config/dbh.config.php";

class Signup_model extends Dbh {
    public function send_request(string $email, string $pwd, string $name, string $phone) {
        $pdo = $this->connect();

        try {
            $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT, [ 'cost' => 12 ]);
            $query = "INSERT INTO Temp_PendingUsers (FullName, Email, Pwd, Phone) VALUES (:fullname, :email, :pwd, :phone);";
            $stmt = $pdo->prepare($query);
            $stmt-> bindParam(":fullname", $name);
            $stmt-> bindParam(":email", $email);
            $stmt-> bindParam(":pwd", var: $hashedPwd);
            $stmt-> bindParam(":phone", $phone);
            $stmt-> execute();
        } catch (PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
        }
    }
}
