<?php

declare(strict_types= 1);

//////////////////////////////////////
//ANTES DE SUBIR MUDAR AUTH USERS 
/////////////////////////////////////

function get_user(object $pdo, string $email) {
    $query = 'SELECT * FROM auth_users WHERE Email = :email';
    $stmt = $pdo->prepare($query);
    $stmt -> bindParam(':email', $email); 
    $stmt -> execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}