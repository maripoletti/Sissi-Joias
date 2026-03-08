<?php

declare(strict_types= 1);
require_once __DIR__ . "/../models/controledeusuarios_model.php";
header("Content-Type: application/json");

$db = new ControledeusuariosModel();


$status = $_GET["status"] ?? "";

if (empty($status)) {
    $vendedoras = [];
    echo json_encode([
        "usuarios" => $vendedoras
    ]);
    exit;
} else {
    $vendedoras = $db->get_employees_by_status($status);
    echo json_encode([
        "usuarios" => $vendedoras
    ]);
    exit;
}