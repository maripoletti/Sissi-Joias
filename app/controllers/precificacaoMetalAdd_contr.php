<?php

declare(strict_types= 1);
require_once __DIR__ . "/../models/precificacao_model.php";
header("Content-Type: application/json");


$db = new precificacao_model();

$input = json_decode(file_get_contents("php://input"), true);

$name = mb_strtoupper(trim($input["nome"] ?? ""));
$value = (float)($input["valorGrama"] ?? 0);

if ($name === "") {
    echo json_encode([
        "success" => false,
        "message" => "Nome obrigatório."
    ]);
    exit;
}

$response = $db->criar_metal($name, $value);

echo json_encode($response ?? "", JSON_UNESCAPED_UNICODE);

exit;