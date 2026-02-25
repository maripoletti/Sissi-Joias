<?php

declare(strict_types= 1);
require __DIR__ . "/../../config/dbh.config.php";
//////////////////////////////////////
//ANTES DE SUBIR MUDAR AUTH USERS 
/////////////////////////////////////

class Login_model extends Dbh {
    public function get_user_by_email($email) {
        try {
            $query = 'SELECT * FROM auth_users WHERE Email = :email;';
            $stmt = $this->connect()->prepare($query);
            $stmt-> bindParam(':email', $email);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
        }
    }
}