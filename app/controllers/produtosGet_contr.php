<?php
declare(strict_types= 1);
require_once __DIR__ . "/../models/produtos_model.php";
require_once __DIR__ . "/../services/prodValidator.php";
require_once __DIR__ . "/../helpers/utf8ize.php";
header("Content-Type: application/json; charset=UTF-8");

$db = new produtos_model();

$baseUrl = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'];

$input = json_decode(file_get_contents('php://input'), true);

$q = $input["text"] ?? "";
$tags = $input["tags"] ?? [];
$price = $input["price"] ?? "all";
$sort = $input["sort"] ?? "relevancia";
$page = $input["page"] ?? 0;
$limit = $input["limit"] ?? 10;
$tamanho = $input["tamanho"] ?? "";
$cor = $input["cor"] ?? "";
$peso_banho = $input["peso_banho"] ?? "";
$milesimos_banho = $input["milesimos_banho"] ?? "";

$data = prodValidator::validate_get(
    $q,
    $tags,
    $price,
    $sort,
    $limit,
    $page,
    $tamanho,
    $cor,
    $peso_banho,
    $milesimos_banho
);

if(!empty($data)) {
    $all = $db->get_products($data);
} else {
    $all = [];
}

$allutf8 = utf8ize::utf8($all ?? ['produtos'=>[], 'total'=>0]);

foreach($allutf8['produtos'] as $i => $p) {
    $allutf8['produtos'][$i]['img'] = $baseUrl . $p['img'];
}

$response = [
    "produtos"=> $allutf8['produtos'],
    "total" => $allutf8["total"]
];
$json = json_encode($response, JSON_UNESCAPED_UNICODE);

if($json === false){
    error_log("Erro no json_encode: " . json_last_error_msg());
    echo json_encode(["produtos"=>[], "total"=>0]);
} else {
    echo $json;
}
