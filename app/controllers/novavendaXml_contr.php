<?php

require_once __DIR__ . "/../models/novavenda_model.php";

if (!isset($_FILES["xmlfile"])) {
    die("Nenhum arquivo enviado");
}

if ($_FILES["xmlfile"]["error"] !== UPLOAD_ERR_OK) {
    die("Erro no upload");
}

$tmp = $_FILES["xmlfile"]["tmp_name"];

if (!file_exists($tmp) || filesize($tmp) === 0) {
    die("Arquivo XML vazio");
}

libxml_use_internal_errors(true);
$xml = simplexml_load_file($tmp);

if ($xml === false) {
    die("XML inválido");
}

$model = new novavenda_model();

foreach ($xml->venda as $venda) {

    $produto = [
        "id" => (int)$venda->produto->id,
        "quantidade" => (int)$venda->produto->quantidade,
        "preco" => (float)$venda->produto->preco
    ];

    $cliente = [
        "nome" => (string)$venda->cliente->nome,
        "cpf" => (string)$venda->cliente->cpf
    ];

    $pagamento = (string)$venda->pagamento;

    $result = $model->realizar_venda($produto, $cliente, $pagamento);

    if ($result === false) {
        header("Refresh: 3; url=/vendas");
        exit;
    }
}

header("Refresh: 1; url=/vendas");
echo "Importação de vendas concluída";
exit;

