<?php

declare(strict_types= 1);
require __DIR__ . "/../../config/dbh.config.php";

class Signup_model extends Dbh {
    public function create_user(int $uid, string $email, string $pwd) {
        $pdo = $this->connect();

        $pdo->beginTransaction();

        try {
            $query = "INSERT INTO Auth_Users"
        }
    }
}
