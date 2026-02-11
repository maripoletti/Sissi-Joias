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
            $errors['is_bigger'] = 'Limite máximo de caracteres excedido!';
        }

        $result = get_user($pdo, $email);

        if(is_email_wrong($result)) {
            $errors['email_wrong'] = 'Algo está errado!';
        }
        if(!is_email_wrong($result) && is_password_wrong($pwd, $result['Pwd'])) {
            $errors['password_wrong'] = 'Algo está errado!';
        }

        require_once '../config/session.config.php';

        if($errors) {
            $_SESSION['errors_login'] = $errors;
            print_r($errors);


            header('Location: index.php');
            die();
        }

        /* $newSessionId = session_create_id();
        $sessionId = $newSessionId . "_" . $result["UserID"];
        session_id($sessionId); */

        $_SESSION["user_id"] = $result["UserID"];
        $_SESSION["user_email"] = htmlspecialchars($result["Email"]);

        $_SESSION["last_regeneration"] = time();

        header("Location: index.php?login=success");
        $pdo = null;
        $stmt = null;

        die();
    } catch (PDOException $e) {
        header('Location: index.php');
        die("Query failed: " . $e->getMessage());
    }
} else {
    header('Location: index.php');
    die();
}