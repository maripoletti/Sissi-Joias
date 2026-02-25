<?php

declare(strict_types= 1);

class AuthMiddleware {
    public static function user() {
        if(!isset($_SESSION)) {
            header("Location: /login");
            exit;
        }

        $userModel = new UserModel();
        return $userModel->find_role_by_id($_SESSION["user_id"]);
    }
}