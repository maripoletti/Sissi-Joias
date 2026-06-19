<?php

declare(strict_types=1);
require_once __DIR__ . "/../models/produtos_model.php";
header("Content-Type: application/json");

$db = new Produtos_model();

try {
    $response = $db->listar_maletas();
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Erro ao buscar maletas: " . $e->getMessage()
    ]);
}

exit;
