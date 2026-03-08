<?php

require_once __DIR__ . "../models/impressoras_model.php";
require_once __DIR__ . "../models/produtos_model.php";
require_once __DIR__ . "../services/printerServices.php";
$data = json_decode(file_get_contents("php://input"), true);

$productId = $data["product_id"];
$printerId = $data["printer_id"];

$productModel = new Produtos_model();
$product = $productModel->get_products($productId);

$printerModel = new impressoras_model();
$printer = $printerModel->get_printer($printerId);

printerServices::imprimir(
    $printer["ip"],
    $printer["port"],
    $product
);

echo json_encode(["status"=>"ok"]);