<?php

declare(strict_types=1);
require_once __DIR__ . "/../models/topRev_model.php";
header("Content-Type: application/json");

$db = new topRev_model();

$mes = $_GET["mes"] ?? null;
$ano = $_GET["ano"] ?? null;
$campanha = $_GET["campanha"] ?? "Todas";

if (!$mes || !$ano) {
    echo json_encode([
        "success" => false,
        "message" => "Mês e ano são obrigatórios."
    ]);
    exit;
}

try {
    $response = $db->pegar_top_revendedoras($mes, (int)$ano, $campanha);
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Erro ao buscar ranking: " . $e->getMessage()
    ]);
}

exit;
