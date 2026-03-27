<?php

class trocarsenha_contr {
    public function show() {
        require_once '../app/models/esquecisenha_model.php';
        $model = new esquecisenha_model();

        $token = $_GET['token'] ?? '';
        if (!$token) die("Token inválido");

        $token_hash = hash('sha256', $token);
        $reset = $model->get_token($token_hash);

        if (!$reset || strtotime($reset['ExpiresAt']) < time()) {
            die("Token inválido ou expirado");
        }

        require '../app/views/trocarsenha_view.php';
    }

    public function update() {
        require_once '../app/models/esquecisenha_model.php';
        $model = new esquecisenha_model();

        $token = $_POST['token'] ?? '';
        $pwd = $_POST['pwd'] ?? '';
        $pwdRepeat = $_POST['pwdRepeat'] ?? '';

        if (!$token || !$pwd || !$pwdRepeat) {
            die("Dados inválidos");
        }

        if ($pwd !== $pwdRepeat) {
            die("Senhas não coincidem");
        }

        $token_hash = hash('sha256', $token);
        $reset = $model->get_token($token_hash);

        if (!$reset || strtotime($reset['ExpiresAt']) < time()) {
            die("Token inválido ou expirado");
        }

        $hash = password_hash($pwd, PASSWORD_DEFAULT);

        $model->update_password($reset['UserID'], $hash);
        $model->delete_tokens($reset['UserID']);

        header("Location: /login");
        exit;
    }
}