<?php

declare(strict_types= 1);

$email = $_POST["email"];
$pwd = $_POST["pwd"];

require_once __DIR__ . '/../models/login_model.php';
require_once __DIR__ . '/../services/authValidator.php';
$dbh = new Login_model();

$errors = AuthValidator::validateLoginInput($email, $pwd);

if(!$errors) {
    $user = $dbh->get_user_by_email($email);

    $errors = AuthValidator::validateCredentials($user, $pwd);
}
if($errors) {
    $_SESSION['errors_login'] = $errors;
    header('Location: /');
    exit;
} else {
    $_SESSION['user_id'] = $user['UserID'];
    header('Location: /dashboard');
    exit;
}