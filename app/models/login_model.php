<?php

declare(strict_types= 1);
require_once __DIR__ . "/../../config/dbh.config.php";

class Login_model extends Dbh {
    public function get_user_by_email($email) {
        try {
            $query = 'SELECT * FROM Auth_Users WHERE Email = :email;';
            $stmt = $this->connect()->prepare($query);
            $stmt-> bindParam(':email', $email);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
        }
    }
}