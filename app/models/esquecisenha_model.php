<?php

declare(strict_types= 1);
require_once __DIR__ . "/../../config/dbh.config.php";
class esquecisenha_model extends Dbh {
    public function check_email(string $email) {
        $pdo = $this->connect();
        try {
            $query = "SELECT UserID FROM Auth_Users WHERE Email = :email";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro na conexão: ". $e->getMessage();
        }
    }
    public function save_token ($userID, $token, $expires) {
        $pdo = $this->connect();

        $stmt=$pdo->prepare("INSERT INTO Auth_PasswordReset (UserID, Token, ExpiresAt) VALUES (?, ?, ?)");
        $stmt->execute([$userID, $token, $expires]);
    }

    public function delete_tokens($userID) {
        $pdo = $this->connect();

        $stmt = $pdo->prepare("DELETE FROM Auth_PasswordReset WHERE UserID = ?");
        $stmt->execute([$userID]);
    }

    public function get_token($token) {
        $pdo = $this->connect();

        $stmt = $pdo->prepare("
            SELECT * FROM Auth_PasswordReset 
            WHERE Token = ?
        ");
        $stmt->execute([$token]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update_password($userID, $hash) {
        $pdo = $this->connect();

        $stmt = $pdo->prepare("
            UPDATE Auth_Users 
            SET Pwd = ?
            WHERE UserID = ?
        ");
        $stmt->execute([$hash, $userID]);
    }
}