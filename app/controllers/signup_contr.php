<?php

declare(strict_types= 1);

$name = $_POST["name"] ?? "";
$email = $_POST["email"] ?? "";
$pwd = $_POST["pwd"] ?? "";
$pwdRepeat = $_POST["pwdRepeat"] ?? "";
$phone = $_POST["phone"] ?? "";

require_once __DIR__ . '/../models/signup_model.php';
require_once __DIR__ . '/../services/authValidator.php';
$signup = new Signup_model();

$validate = AuthValidator::validateSignup($name, $email, $pwd, $phone, $pwdRepeat);
$errors = $validate['errors'] ?? [];

if ($errors) {
    $_SESSION['errors_signup'] = $errors;
    header('Location: /cadastro');
    exit();
} else {
    $signup->send_request($name, $email, $pwd, $phone);
    $_SESSION['signup_submitted'] = "Seu cadastro foi enviado e está aguardando aprovação";
    header('Location: /cadastro?status=approved');
    exit();
}