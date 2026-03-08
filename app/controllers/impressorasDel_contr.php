<?php

declare(strict_types= 1);
require_once __DIR__ . "/../models/impressoras_model.php";
header("Content-Type: application/json");

$db = new impressoras_model();
$input = json_decode(file_get_contents('php://input'), true);
$id = (int)($input["id"]);

if(!$id) {
    http_response_code(400);
    echo "Dados inválidos";
    exit;
} else {
    $db->delete_printer($id);
    exit;
}