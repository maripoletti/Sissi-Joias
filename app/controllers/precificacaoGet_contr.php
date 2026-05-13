<?php

declare(strict_types= 1);
require_once __DIR__ . "/../models/precificacao_model.php";
header("Content-Type: application/json");


$db = new precificacao_model();

$nome = $_GET["nome"] ?? "";

if ($nome === "") {
    exit;
}
$produtos = $db->buscar_produto($nome);

echo json_encode($produtos ?? "", JSON_UNESCAPED_UNICODE);

exit;