<?php

declare(strict_types= 1);

$email = $_POST["email"];
$pwd = $_POST["pwd"];

require_once __DIR__ . '/../models/login_model.php';
require_once __DIR__ . '/../services/authValidator.php';
require_once __DIR__ . '/../middleware/authMiddleware.php';
$login = new login_model();

$errors = authValidator::validateLoginInput($email, $pwd);

if(!$errors) {
    $user = $login->get_user_by_email($email);

    $errors = authValidator::validateCredentials($user, $pwd);
}
if($errors) {
    $_SESSION['errors_login'] = $errors;
    header('Location: /login');
    exit;
} else {
    $_SESSION['user_id'] = $user['UserID'];
    $role = authMiddleware::user()['RoleID'];
    $_SESSION['role'] = $role;
    header('Location: /paineldecontrole');
    exit;
}