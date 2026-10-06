<?php
declare(strict_types=1);

require_once __DIR__ . "/../models/produtos_model.php";

header("Content-Type: application/json; charset=UTF-8");

$model = new Produtos_model();

$input = json_decode(file_get_contents('php://input'), true);

foreach ($input["itens"] as $i) {

    $revID     = (int)($i["revId"] ?? 0);
    $ProductID = (int)($i["prodId"] ?? 0);
    $caseId    = $i["caseId"] ?? "sem";

    if ($caseId !== "sem") {
        $caseId = (int)$caseId;
    }

    if ($revID <= 0 || $ProductID <= 0) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Dados inválidos"
        ]);
        exit;
    }

    $model->remove_products($ProductID, $revID, $caseId);
}

echo json_encode(["success" => true]);