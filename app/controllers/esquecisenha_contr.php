<?php
declare(strict_types = 1);

require_once __DIR__ . "/../models/esquecisenha_model.php";
$model = new esquecisenha_model();

$email = $_POST["email"] ?? "";

if (!$email) {
    header("Location: /login");
    exit;
}

$user = $model->check_email($email);

if($user) {
    $userID = $user['UserID'];
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    $token_hash = hash('sha256', $token);
    $model->delete_tokens($userID);
    $model->save_token($userID, $token_hash, $expires);

    $link = "https://sissisemijoiaseacessorios.com.br/trocarsenha?token=" . $token;

    require_once __DIR__ . "/../../lib/PHPMailer/src/PHPMailer.php";
    require_once __DIR__ . "/../../lib/PHPMailer/src/SMTP.php";
    require_once __DIR__ . "/../../lib/PHPMailer/src/Exception.php";

    $config = require_once __DIR__ . "/../../config/mail.php";

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.sissisemijoiaseacessorios.com.br';
        $mail->SMTPAuth = true;
        $mail->Username = 'sissisemijoias=sissisemijoiaseacessorios.com.br';
        $mail->Password = $config['password'];

        $mail->SMTPAutoTLS = false;
        $mail->SMTPSecure = false;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom('sissisemijoias@sissisemijoiaseacessorios.com.br', 'Sissi Semi Joias');
        $mail->addAddress($email);

        $mail->Subject = 'Recuperação de senha';
        $mail->Body = "Acesse o link para redefinir sua senha:\n$link";

        $mail->send();
    } catch (Exception $e) {
        error_log($mail->ErrorInfo);
    }
};

header("Location: /login");
exit;