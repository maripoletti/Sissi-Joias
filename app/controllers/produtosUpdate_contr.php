<?php

declare(strict_types= 1);
require_once __DIR__ . "/../models/produtos_model.php";
require_once __DIR__ . "/../services/prodValidator.php";
require_once __DIR__ . "/../helpers/imageUpload.php";
header("Content-Type: application/json");

$upload = new imageUpload();
$db = new produtos_model();

$fotoPath = null;

if (!empty($_FILES['foto']['name'])) {
    $fotoPath = $upload->image($_FILES['foto']);
}

$id = (int)($_POST['id'] ?? 0);
$nome = $_POST['nome'] ?? "";
$preco = (float)($_POST['preco'] ?? 0);
$estoque = (int)($_POST['estoque'] ?? 0);

$tamanho = $_POST["tamanho"] ?? "";
$cor = $_POST["cor"] ?? "";
$peso_banho = $_POST["peso_banho"] ?? "";
$milesimos_banho = $_POST["milesimos_banho"] ?? "";

$validate = prodValidator::validate_update(
    $id,
    $nome,
    $preco,
    $estoque,
    $fotoPath,
    $tamanho,
    $cor,
    $peso_banho,
    $milesimos_banho
);

if($validate['errors']){
    $_SESSION['error_upd_prod'] = $validate['errors'];
    exit;
}

$db->update_products($validate['data']);
exit;