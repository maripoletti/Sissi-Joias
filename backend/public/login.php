<?php

if($_SERVER["REQUEST_METHOD"] === 'POST') {
    $email = $_POST['email'];
    $pwd = $_POST['pwd'];

    try {
        $errors = [];

        


    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage())
    }
} else {
    header('Location: index.php');
    die();
}