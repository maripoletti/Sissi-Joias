<?php
declare(strict_types=1);
require_once __DIR__ . "/../models/produtosRevendedores_model.php";
header("Content-Type: application/json");

$input = json_decode(file_get_contents('php://input'), true);

$model = new produtosRevendedores_model();

$produto = $input["filters"]["produto"];
$revendedor = $input["filters"]["revendedor"];
$page = $input["pagination"]["page"] ?? 0;
$limit = $input["pagination"]["limit"] ?? 50;

$response = $model->pegar_produtos($produto, $revendedor, $page, $limit);

$json = json_encode($response, JSON_UNESCAPED_UNICODE);

if($json === false){
    error_log("Erro no json_encode: " . json_last_error_msg());
    echo json_encode(["produtos"=>[]]);
} else {
    echo $json;
}