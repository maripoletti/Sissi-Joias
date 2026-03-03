<?php

declare(strict_types= 1);
require_once __DIR__ . "/../models/produtos_model.php";
require_once __DIR__ . "/../services/prodValidator.php";
header("Content-Type: application/json");

$db = new Produtos_model();

$id = (int)($_POST['id'] ?? 0);
$nome = $_POST['nome'] ?? "";
$preco = (float)($_POST['preco'] ?? 0);
$foto = $_FILES['foto']['tmp_name'] ?? null;

$validate = ProdValidator::validate_update($id, $nome, $preco, $foto);
if($validate['errors']) {
    $_SESSION['error_upd_prod'] = $validate['errors'];
    exit;
} else {
    $db->update_products($validate['data']);
    exit;
}