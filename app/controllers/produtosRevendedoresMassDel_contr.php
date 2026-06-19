<?php
declare(strict_types=1);

require_once __DIR__ . "/../models/produtos_model.php";

header("Content-Type: application/json; charset=UTF-8");

$model = new Produtos_model();

$input = json_decode(file_get_contents('php://input'), true);

foreach ($input["itens"] as $i) {
    $revID = (int)($i["revId"] ?? 0);
    $ProductID = (int)($i["prodId"] ?? 0);

    if ($revID <= 0 || $ProductID <= 0) {
        http_response_code(400);
        return [
            "success" => false,
            "message" => "Dados inválidos"
        ];
    }

    $model->remove_products($ProductID, $revID);
}

echo json_encode(["success" => true]);