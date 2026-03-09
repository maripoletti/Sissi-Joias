<?php
declare(strict_types=1);
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . "/../models/vendascan_model.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não logado']);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
$cdb = $input['cdb'] ?? '';
$quantity = (int)($input['quantity'] ?? 1);
$paymentMethod = $input['paymentMethod'] ?? 'Scanner';


if (!$cdb) {
    echo json_encode(['success' => false, 'message' => 'Código de barras não informado']);
    exit;
}

$db = new vendascan_model();

$produto = $db->buscarProduto($cdb);

if (!$produto) {
    echo json_encode(['success' => false, 'message' => 'Produto não encontrado']);
    exit;
}

$price = (float)($produto['Price'] ?? 0);


$success = $db->registrarVenda((int)$produto['ProductID'], (int)$_SESSION['user_id'], $price, $quantity, $paymentMethod);

if ($success) {
    echo json_encode([
        'success' => true,
        'produto_nome' => $produto['ProductName'] ?? 'Produto',
        'produto_id' => $produto['ProductID'],
        'price' => $price,
        'quantity' => $quantity
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao registrar venda']);
}