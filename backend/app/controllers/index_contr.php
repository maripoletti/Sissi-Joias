<?php

declare(strict_types= 1);

function is_empty(string $email, string $pwd) {
    if (empty($email) || empty ($pwd)) {
        return true;
    } else {
        return false;
    }
}

function is_bigger_than_expected(string $email, string $pwd) {
    if (mb_strlen($email) > 70 || mb_strlen($pwd) > 30) {
        return true;
        return false;
    }
}

function is_email_wrong(object $pdo, string $email) {
    if(!get_email($pdo, $email)) {
        return true;
    } else {
        return false;
    }
}

function is_password_wrong(object $pdo, string $pwd) {
    $options = [
        'cost' => 12,
    ];
    $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT, $options);

    if(!get_password($pdo, $hashedPwd)) {
        return true;
    } else {
        return false;
    }
}