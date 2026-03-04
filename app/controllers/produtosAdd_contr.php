<?php

declare(strict_types= 1);
require_once __DIR__ . "/../models/produtos_model.php";
require_once __DIR__ . "/../services/prodValidator.php";
require_once __DIR__ . "/../services/imageUpload.php";
header("Content-Type: application/json");

$upload = new imageUpload();
$db = new produtos_model();

$fotoPath = $upload->image($_FILES['foto'] ?? []);

$name = $_POST["nome"] ?? "";
$tags = $_POST["categoria"] ?? [];
$price = (float)($_POST["preco"] ?? "");
$stock = (int)($_POST["estoque"] ?? 0);

$validate = prodValidator::validate_add($name, $tags, $price, $stock, $photo);
var_dump( $validate );
if($validate['errors']) {
    $_SESSION['error_add_prod'] = $validate['errors'];
    exit;
} else {
    $asd = $db->set_products($validate['data']);
    var_dump( $asd );
    exit;
}

