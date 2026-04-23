<?php
declare(strict_types=1);
require_once __DIR__ . "/../models/produtos_model.php";
header("Content-Type: application/json");

$input = json_decode(file_get_contents('php://input'), true);

$model = new produtos_model();

$productId = (int)($input["id"] ?? "");
$userId = (int)($_SESSION["user_id"]);

if(!$productId) {
    http_response_code(400);
    echo "Dados inválidos";
    exit;
} else {
    $model->remove_products($productId, $userId);
    exit;
}