<?php

declare(strict_types= 1);

class AuthValidator {
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

    public static function validateCredentials(bool|array $user, string $pwd) {
        $errors = [];

        if(!$user) {
            $errors['email_wrong'] = 'Algo está errado!';
            return $errors;
        }
        if(!password_verify($pwd, $user['Pwd'])) {
            $errors['password_wrong'] = 'Algo está errado!';
        }
        
        return $errors;
    }

    public static function validateSignup(string $name, string $email, string $pwd, string $phone, string $pwdRepeat): array {
        $errors = [];

        $clean = [
            'name'  => trim($name ?? ''),
            'email' => trim(strtolower($email ?? '')),
            'pwd'   => $pwd ?? '',
            'phone' => preg_replace('/\D/', '', $phone ?? '')
        ];

        if (
            empty($clean['name']) ||
            empty($clean['email']) ||
            empty($clean['pwd']) ||
            empty($clean['phone'])
        ) {
            $errors['empty_field'] = 'Preencha todos os campos!';
        }

        if (
            mb_strlen($clean['name']) > 140 ||
            mb_strlen($clean['email']) > 70 ||
            mb_strlen($clean['pwd']) > 35
        ) {
            $errors['is_bigger'] = 'Limite máximo de caracteres excedido!';
        }

        if (mb_strlen($clean['name']) < 2) {
            $errors['name_wrong'] = 'Nome inválido.';
        }

        if (!filter_var($clean['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email_wrong'] = 'Use um email válido!';
        }

        if (mb_strlen($clean['pwd']) < 8) {
            $errors['pwd_wrong'] = 'Use no mínimo 8 caracteres para criar sua senha.';
        }
        
        if ($clean['pwd'] !== $pwdRepeat) {
            $errors['pwd_match'] = 'Senhas diferentes.';
        }

        if (!in_array(strlen($clean['phone']), [10, 11], true)) {
            $errors['phone_wrong'] = 'Telefone inválido. Informe o número com DDD.';
        }

        return [
            'errors' => $errors,
            'data'   => $clean
        ];
    }
}