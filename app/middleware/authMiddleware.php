<?php

declare(strict_types= 1);
require_once __DIR__ . "/../models/user_model.php";

class AuthMiddleware {
    public static function user() {
        if(!isset($_SESSION["user_id"])) {
            header("Location: /login");
            exit;
        }

        $userModel = new User_model();
        return $userModel->find_role_by_id($_SESSION["user_id"]);
    }
}