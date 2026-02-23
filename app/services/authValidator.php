<?php

declare(strict_types= 1);

class authValidator {
    public static function validateLoginInput(string $email, string $pwd) {
        $errors = [];
        if(empty($email) || empty ($pwd)) {
            $errors['fields_empty'] = 'Preencha todos os campos!';
        }
        if(mb_strlen($email) > 70 || mb_strlen($pwd) > 30) {
            $errors['is_bigger'] = 'Limite máximo de caracteres excedido!';
        }

        return $errors;
    }

    public static function validateCredentials(array $user, string $pwd) {
        $errors = [];

        if(!$user) {
            $errors['email_wrong'] = 'Algo está errado!';
        }
        if(!password_verify($pwd, $user['pwd'])) {
            $errors['password_wrong'] = 'Algo está errado!';
        }
        
        return $errors;
    }
}