<?php

declare(strict_types= 1);
require_once __DIR__ . "/../models/controledeusuarios_model.php";
header("Content-Type: application/json");
$db = new ControledeusuariosModel();

$input = json_decode(file_get_contents("php://input"), true);

$id = (int)($input["id"] ?? null);

if(!$id) {
    http_response_code(400);
    echo "Dados inválidos";
    exit;
}

$status = $db->get_status_by_id($id);
if($status['Status'] == "pendente" || $status['Status'] == "aprovado") {
    $db->reject_user($id);
    exit;
} else {
    http_response_code(400);
    echo "Dados inválidos";
    exit;
}