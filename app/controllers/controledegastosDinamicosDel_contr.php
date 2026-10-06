<?php
declare(strict_types=1);

require_once __DIR__ . "/../models/controledegastos_model.php";
header("Content-Type: application/json");
$db = new controledegastos_model();

$input = json_decode(file_get_contents("php://input"), true);

$id = (int)($input["id"]);

if(!$id) {
    return [
        "success" => false,
    ];
}

$result = $db->excluirGasto($id);

echo json_encode($result, JSON_UNESCAPED_UNICODE);