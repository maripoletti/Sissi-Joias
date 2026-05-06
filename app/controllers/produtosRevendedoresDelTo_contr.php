<?php
declare(strict_types=1);
require_once __DIR__ . "/../models/produtos_model.php";
header("Content-Type: application/json");

$input = json_decode(file_get_contents('php://input'), true);

$model = new produtos_model();

$productId = (int)($input["prodId"] ?? "");
$userId = (int)($input["revId"] ?? "");

if(!$productId) {
    http_response_code(400);

    echo json_encode(["success" => false, "error" => "Dados inválidos"]);
    exit;
} else {
    $model->remove_products($productId, $userId);

    echo json_encode(["success" => true]);
    exit;
}