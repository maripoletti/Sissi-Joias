<?php

declare(strict_types= 1);
require_once __DIR__ . "/../models/produtos_model.php";
require_once __DIR__ . "/../services/prodValidator.php";
require_once __DIR__ . "/../helpers/imageUpload.php";
header("Content-Type: application/json");

$upload = new ImageUpload();
$db = new produtos_model();

$fotoPath = $upload->image($_FILES['foto'] ?? []);

$name = $_POST["nome"] ?? "";
$tags = $_POST["categoria"] ?? [];
$price = (float)($_POST["preco"] ?? "");
$stock = (int)($_POST["estoque"] ?? 0);
$tamanho = $_POST["tamanho"] ?? "";
$cor = $_POST["cor"] ?? "";
$peso_banho = ($_POST["peso_banho"] === "") ? null : (int)$_POST["peso_banho"];
$milesimos_banho = ($_POST["milesimos_banho"] === "") ? null : (int)$_POST["milesimos_banho"];

$validate = prodValidator::validate_add(
    $name,
    $tags,
    $price,
    $stock,
    $fotoPath,
    $tamanho,
    $cor,
    $peso_banho,
    $milesimos_banho
);

if($validate['errors']){
    $_SESSION['error_add_prod'] = $validate['errors'];
    exit;
}

$db->set_products($validate['data']);
exit;