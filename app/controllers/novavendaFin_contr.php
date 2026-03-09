<?php

declare(strict_types= 1);
require_once __DIR__ . "/../models/novavenda_model.php";
require_once __DIR__ . "/../services/orderValidator.php";
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);


$cliente = $data["cliente"];
$pagamento = $data["pagamento"];
$produtos = $data["produtos"];

$db = new novavenda_model();

$validate = orderValidator::validate($cliente, $pagamento, $produtos);

if($validate['errors']) {
    $_SESSION['error_order_sale'] = $validate['errors'];
    exit;
} else {
    $orderId = $db->realizar_venda($validate['data']['produto'], $validate['data']['cliente'], $validate['data']['pagamento']);
    echo json_encode([
        "order_id" => $orderId
    ]);
    exit;
}
