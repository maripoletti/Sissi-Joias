<?php

declare(strict_types= 1);
require_once __DIR__ . "/../models/novavenda_model.php";
require_once __DIR__ . "/../helpers/utf8ize.php";
header("Content-Type: application/json");

$db = new novavenda_model();

$input = json_decode(file_get_contents("php://input"), true);

$nome = $input["texto"] ?? "";
$userID = $_SESSION["user_id"] ?? "";

if ($nome === "") {
    exit;
}
$produtos = $db->buscar_produto($nome);
$produtosutf8 = utf8ize::utf8($produtos);

echo json_encode($produtos ?? "", JSON_UNESCAPED_UNICODE);

exit;