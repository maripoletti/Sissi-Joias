<?php

declare(strict_types= 1);
require_once __DIR__ . "/../models/produtos_model.php";
header("Content-Type: application/json");

$db = new produtos_model();

$input = json_decode(file_get_contents('php://input'), true);

$ProductID = (int)($input["id"] ?? "");
$userID = $_SESSION["user_id"];

if(!$id) {
    http_response_code(400);
    echo "Dados inválidos";
    exit;
} else {
    $db->remove_products($ProductID, $userID);
    exit;
}