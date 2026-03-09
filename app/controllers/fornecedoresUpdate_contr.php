<?php

declare(strict_types=1);
require_once __DIR__ . "/../models/fornecedores_model.php";

header("Content-Type: application/json");

$db = new fornecedores_model();

$input = json_decode(file_get_contents("php://input"), true);

$data = [
    "id" => (int)($input["id"] ?? 0),
    "nome"=>$input["nome"] ?? "",
    "cnpj"=>$input["cnpj"] ?? "",
    "email"=>$input["email"] ?? "",
    "telefone"=>$input["telefone"] ?? "",
    "endereco"=>$input["endereco"] ?? "",
];

$db->update_fornecedor($data);