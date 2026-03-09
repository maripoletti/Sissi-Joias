<?php

declare(strict_types=1);
require_once __DIR__ . "/../models/fornecedores_model.php";
require_once __DIR__ . "/../helpers/utf8ize.php";

header("Content-Type: application/json; charset=UTF-8");

$db = new fornecedores_model();

$input = json_decode(file_get_contents("php://input"), true);

$text = $input["text"] ?? "";
$page = $input["page"] ?? 0;
$limit = $input["limit"] ?? 10;

$data = [
    "text"=>$text,
    "page"=>$page,
    "limit"=>$limit
];
$result = $db->get_fornecedores($data);

$json = json_encode($result, JSON_UNESCAPED_UNICODE);

echo $json;