<?php
declare(strict_types=1);
require_once __DIR__ . "/../models/produtos_model.php";
header("Content-Type: application/json");

$input = json_decode(file_get_contents('php://input'), true);

$model = new produtos_model();

$productId = (int)($input["prodId"] ?? 0);
$userId    = (int)($input["revId"] ?? 0);

$caseId = ($input["caseId"] ?? "sem");
if ($caseId !== "sem") {
    $caseId = (int)$caseId;
}

if ($productId <= 0 || $userId <= 0) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "error" => "Dados inválidos"
    ]);
    exit;
}

$model->remove_products(
    $productId,
    $userId,
    $caseId
);

echo json_encode(["success" => true]);