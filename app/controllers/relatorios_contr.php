<?php

declare(strict_types=1);
require_once __DIR__ . "/../models/relatorios_model.php";
header("Content-Type: application/json; charset=UTF-8");

$db = new relatorios_model();

$data = $db->buscar_relatorios();
if($data) {
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
} else {
    http_response_code(400);
    exit;
}