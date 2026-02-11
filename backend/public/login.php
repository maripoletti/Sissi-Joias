<?php

if($_SERVER["REQUEST_METHOD"] === 'POST') {
    $email = $_POST['email'];
    $pwd = $_POST['pwd'];

    try {
        require_once '../config/dbh.config.php';
        require_once '../app/models/index_model.php';
        require_once '../app/controllers/index_contr.php';
    

        $errors = [];

        // error handlers
        if(is_empty($email, $pwd)) {
            $errors['fields_empty'] = 'Preencha todos os campos!';
        }
        if(is_bigger_than_expected($email, $pwd)) {
            $errors['is_bigger'] = 'Limite máximo de caracteres excedido';
        }
        if(is_email_wrong($pdo, $email)) {
            $errors['email_wrong'] = 'Algo está errado';
        }
        if(is_password_wrong($pdo, $pwd)) {
            $errors['password_wrong'] = 'Algo está errado';
        }

        if($errors) {
            header('Location: index.php');
            die();
        } else {
            
        }
    } catch (PDOException $e) {
        header('Location: index.php');
        die("Query failed: " . $e->getMessage());
    }
} else {
    header('Location: index.php');
    die();
}