<?php
declare(strict_types=1);
require_once __DIR__ . "/../models/controledegastos_model.php";
header("Content-Type: application/json");
$db = new controledegastos_model();

$input = json_decode(file_get_contents("php://input"), true);

$texto = mb_strtoupper(trim($input["texto"] ?? "Todos"));
$metal = $texto . "%";

$result = $db->pegarMetais($metal);

echo json_encode($result, JSON_UNESCAPED_UNICODE);