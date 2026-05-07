<?php
declare(strict_types=1);
require_once __DIR__ . "/../models/produtos_model.php";
header("Content-Type: application/json");

$input = json_decode(file_get_contents('php://input'), true);

$model = new produtos_model();

$productId = (int)($input["prodId"] ?? "");
$userId = (int)($input["revId"] ?? "");
$newStock = (int)($input["quantidade" ?? ""]);

if(empty($productId) || empty($userId) || empty($newStock)) {
    http_response_code(400);

    echo json_encode(["success" => false, "error" => "Dados inválidos"]);
    exit;
} else {
    $model->update_employee_products($productId, $userId, $newStock);

    echo json_encode(["success" => true]);
    exit;
}