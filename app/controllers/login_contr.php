<?php

declare(strict_types= 1);

$email = $_POST["email"];
$pwd = $_POST["pwd"];

require_once __DIR__ . '/../models/login_model.php';
require_once __DIR__ . '/../services/authValidator.php';
require_once __DIR__ . '/../middleware/authMiddleware.php';
$login = new Login_model();

$errors = AuthValidator::validateLoginInput($email, $pwd);

if(!$errors) {
    $user = $login->get_user_by_email($email);

    $errors = AuthValidator::validateCredentials($user, $pwd);
}
if($errors) {
    $_SESSION['errors_login'] = $errors;
    header('Location: /login');
    exit;
} else {
    $_SESSION['user_id'] = $user['UserID'];
    $role = AuthMiddleware::user()['RoleID'];
    $_SESSION['role'] = $role;
    header('Location: /dashboard');
    exit;
}