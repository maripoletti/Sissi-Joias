<?php
declare(strict_types=1);

require_once __DIR__ . "/../models/controledegastos_model.php";
header("Content-Type: application/json");
$db = new controledegastos_model();

$input = json_decode(file_get_contents("php://input"), true);

$intervalos = (array)($input["intervalos"] ?? []);
$metal = (string)($input["metalBanho"] ?? "");

$result = $db->pegarFixos($intervalos, $metal);

echo json_encode($result, JSON_UNESCAPED_UNICODE);