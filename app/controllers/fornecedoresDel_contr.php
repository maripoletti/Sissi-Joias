<?php

declare(strict_types=1);
require_once __DIR__ . "/../models/fornecedores_model.php";

header("Content-Type: application/json");

$db = new fornecedores_model();

$input = json_decode(file_get_contents("php://input"), true);

$id = (int)($input["id"] ?? 0);

if(!$id){
    http_response_code(400);
    exit;
}

$db->delete_fornecedor($id);