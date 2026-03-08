<?php

declare(strict_types= 1);
require_once __DIR__ . "/../models/impressoras_model.php";
require_once __DIR__ . "/../services/printerServices.php";
header("Content-Type: application/json");
$db = new impressoras_model();

$input = json_decode(file_get_contents("php://input"), true);
$id = $input["id"] ?? null;

if(!$id) {
    http_response_code(400);
    echo "Dados inválidos";
    exit;
}

$printer = $db->get_printer($id);

if(!$printer) {
    echo json_encode("não encontrada");
    exit;
}

$status = printerServices::testar($printer["ip"], $printer["port"]);
var_dump($status);