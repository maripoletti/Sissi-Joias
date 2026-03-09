<?php

declare(strict_types=1);
require_once __DIR__ . "/../models/vendas_model.php";
require_once __DIR__ . "/../helpers/utf8ize.php";
header("Content-Type: application/json; charset=UTF-8");

$db = new vendas_model();

$userID = (int)($_SESSION['user_id']);
$role = $_SESSION['role'];

$input = json_decode(file_get_contents("php://input"), true);

$page = $input["page"] ?? 0;
$limit = $input["limit"] ?? 10;

if(isset($userID)) {

    $sales = $db->get_sales($userID, $role, $page, $limit);
    $total = $db->get_total_sales($userID, $role);

    echo json_encode([
        "sales" => utf8ize::utf8($sales),
        "total" => (int)$total["total"]
    ], JSON_UNESCAPED_UNICODE);

    exit;

} else {
    http_response_code(400);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}