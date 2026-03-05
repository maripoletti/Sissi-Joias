<?php

declare(strict_types= 1);
require_once __DIR__ . "/../models/controledeusuarios_model.php";
header("Content-Type: application/json");
$db = new controledeusuarios_model();

$input = json_decode(file_get_contents("php://input"));

$id = $input["id"] ?? null;
$nivel = $input["nivel"] ?? null;

if (!$id || !$nivel) {
    http_response_code(400);
    echo "Dados inválidos";
    exit;
} else {
    $db->switch_lvl($id, $status);
    exit;
}