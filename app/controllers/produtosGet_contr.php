<?php

declare(strict_types= 1);
require_once __DIR__ . "/../models/produtos_model.php";
require_once __DIR__ . "/../services/prodValidator.php";
header("Content-Type: application/json");

$db = new produtos_model();

$input = json_decode(file_get_contents('php://input'), true);
$q = $input["text"] ?? "";
$tags = $input["tags"] ?? [];
$price = $input["price"] ?? "all";
$sort = $input["sort"] ?? "relevancia";
$page = $input["page"] ?? 0;
$limit = $input["limit"] ?? 10;

$data = prodValidator::validate_get($q, $tags, $price, $sort, $limit, $page);

if(!empty($data)) {
    $all = $db->get_products($data);
} else {
    $all = [];
}

echo json_encode([
    "produtos"=> $all['produtos'],
    "total" => $all["total"]
]);
