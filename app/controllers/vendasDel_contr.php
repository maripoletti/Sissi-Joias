<?php

declare(strict_types= 1);
require_once __DIR__ . "/../models/vendas_model.php";
header("Content-Type: application/json");

$db = new vendas_model();

$input = json_decode(file_get_contents('php://input'), true);

$id = (int)($input["id"] ?? "");

if(!$id) {
    http_response_code(400);
    echo "Dados inválidos";
    exit;
} else {
    var_dump($db->delete_sales($id));
    exit;
}