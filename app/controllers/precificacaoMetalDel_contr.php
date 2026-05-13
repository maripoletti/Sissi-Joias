<?php

declare(strict_types= 1);
require_once __DIR__ . "/../models/precificacao_model.php";
header("Content-Type: application/json");


$db = new precificacao_model();

$input = json_decode(file_get_contents("php://input"), true);

$id = (int)($input["id"] ?? 0);

$response = $db->excluir_metal($id);

echo json_encode($response ?? "", JSON_UNESCAPED_UNICODE);

exit;