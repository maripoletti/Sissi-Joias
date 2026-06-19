<?php
declare(strict_types=1);

require_once __DIR__ . "/../models/produtos_model.php";

header("Content-Type: application/json; charset=UTF-8");

$model = new Produtos_model();

$input = json_decode(file_get_contents('php://input'), true);

$caseID = (int)($input["case_id"] ?? 0);
$nomeMaleta = (string)trim($input["nome_maleta"] ?? "");

if (empty($input["produtos"])) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Produtos não informados"
    ]);
    exit;
}

if ($caseID === 0 && $nomeMaleta !== "") {
    $caseID = $model->create_case($nomeMaleta);
} elseif ($caseID === 0) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Informe uma maleta"
    ]);

    exit;
}

$erros = [];

foreach ($input["produtos"] as $p) {
    $ProductID = (int)($p["produto_id"] ?? 0);
    $revID = (int)($p["revendedor_id"] ?? 0);
    $qtd = (int)($p["quantidade"] ?? 0);

    if ($ProductID <= 0) continue;


    $model->add_product_to_case($caseID, $ProductID, $qtd);
    $model->remove_products($ProductID, $revID);
    $model->send_to_employee($revID, $ProductID, $qtd, $caseID);
}

echo json_encode(["success" => true]);