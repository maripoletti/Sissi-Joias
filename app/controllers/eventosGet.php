<?php
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . "/../models/eventos_model.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(["error" => "Não autenticado"]);
    exit;
}

$model = new eventos_model();
$eventos = $model->getEventos((int)$_SESSION['user_id']);

echo json_encode($eventos);