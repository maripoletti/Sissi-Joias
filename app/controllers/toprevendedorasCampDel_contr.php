<?php

declare(strict_types=1);
require_once __DIR__ . "/../models/topRev_model.php";
header("Content-Type: application/json");

$db = new topRev_model();

$input = json_decode(file_get_contents("php://input"), true);
$id = $input["id"] ?? null;

if (!$id) {
    echo json_encode([
        "success" => false,
        "message" => "ID da campanha obrigatório."
    ]);
    exit;
}

try {
    $result = $db->remover_campanha((int)$id);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Erro ao remover campanha: " . $e->getMessage()
    ]);
}
