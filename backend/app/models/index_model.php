<?php

declare(strict_types= 1);

function get_email(object $pdo, string $email) {
    $query = 'SELECT Email FROM Auth_Users WHERE Email = :email;';
    $stmt = $pdo->prepare($query);
    $stmt ->bindParam(':email', $email);
    $stmt -> execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}

function get_password(object $pdo, string $hashedPwd) {
    $query = 'SELECT Pwd FROM Auth_Users WHERE Pwd = :pwd;';
    $stmt = $pdo->prepare($query);
    $stmt ->bindParam(':pwd', $hashedPwd);
    $stmt -> execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}