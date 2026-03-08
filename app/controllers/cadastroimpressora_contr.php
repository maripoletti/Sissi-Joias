<?php

declare(strict_types= 1);
require_once __DIR__ . "/../models/cadastroimpressora_model.php";
require_once __DIR__ . "/../services/printValidator.php";
header("Content-Type: application/json");
$db = new cadastroimpressora_model();

$input = json_decode(file_get_contents("php://input"), true);

$nome = $input["nome"] ?? null;
$conexao = $input["conexao"] ?? null;
$ip = $input["ip"] ?? null;
$porta = (int)($input["porta"] ?? 9100);
$marca = $input["marca"] ?? null;
$modelo = $input["modelo"] ?? null;
$localizacao = $input["localizacao"] ?? null;
$status = $input["status"] ?? null;
$tipo = $input["tipo"] ?? null;

$validate = printValidator::validate($nome, $conexao, $ip, $porta, $marca, $modelo, $localizacao, $status, $tipo);


if ($validate['errors']) {
    $_SESSION['add_print_errors'] = $validate['errors'];
    http_response_code(400);
    echo "Dados inválidos";
    exit;
} else {
    $db->create_printer($validate['data']);
    exit;
}

