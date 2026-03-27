<?php
declare(strict_types=1);

require_once __DIR__ . "/../models/produtos_model.php";

header("Content-Type: application/json; charset=UTF-8");

$model = new Produtos_model();

$input = json_decode(file_get_contents('php://input'), true);

$revID = (int)($input["revendedora_id"] ?? 0);

if ($revID <= 0 || empty($input["produtos"])) {
    http_response_code(400);
    echo json_encode(["erro" => "Dados inválidos"]);
    exit;
}

foreach ($input["produtos"] as $p) {
    $ProductID = (int)($p["produto_id"] ?? 0);
    $qtd = (int)($p["quantidade"] ?? 0);

    if ($ProductID <= 0 || $qtd <= 0) continue;

    $model->send_to_employee($revID, $ProductID, $qtd);
}

echo json_encode(["msg" => "Sucesso"]);