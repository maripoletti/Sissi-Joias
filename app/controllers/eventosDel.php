<?php
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . "/../models/eventos_model.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(["error" => "Não autenticado"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
$eventId = (int)($input['id'] ?? 0);
if (!$eventId) {
    http_response_code(400);
    echo json_encode(["error" => "Evento inválido"]);
    exit;
}

$model = new eventos_model();
$success = $model->deleteEvento((int)$_SESSION['user_id'], $eventId);

echo json_encode(["success" => $success]);