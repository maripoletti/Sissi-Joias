<?php
declare(strict_types=1);

require_once __DIR__ . "/../models/controledegastos_model.php";
header("Content-Type: application/json");
$db = new controledegastos_model();

$input = json_decode(file_get_contents("php://input"), true);

$nome = (string)($input["nome"] ?? "");
$preco = (float)($input["preco"] ?? 0);
$data = (string)($input["data"] ?? "");
$id = (int)($input["id"]);

if(!$id || !$nome || !$data || !$preco) {
    return [
        "success" => false,
        "message" => "Dados inválidos"
    ];
}

$result = $db->editarGasto($nome, $preco, $data, $id);

echo json_encode($result, JSON_UNESCAPED_UNICODE);