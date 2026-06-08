<?php
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . "/../models/eventos_model.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(["error" => "Não autenticado"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

$title = trim($input['titulo'] ?? "");
$date = trim($input['date'] ?? "");
$hour = trim($input['hora'] ?? null);
$type = trim($input['type'] ?? "outro");
$text = trim($input['text'] ?? "Evento");

if (!$title || !$date) {
    http_response_code(400);
    echo json_encode(["error" => "Título e Data obrigatórios"]);
    exit;
}

$model = new eventos_model();
$success = $model->addEvento((int)$_SESSION['user_id'], $title, $date, $hour, $type, $text);

echo json_encode(["success" => $success]);