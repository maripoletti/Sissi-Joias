<?php

require_once __DIR__ . "/../models/produtos_model.php";


if (!isset($_FILES["xmlfile"])) {
    die("Nenhum arquivo enviado");
}

if ($_FILES["xmlfile"]["error"] !== UPLOAD_ERR_OK) {
    die("Erro no upload: " . $_FILES["xmlfile"]["error"]);
}

$tmp = $_FILES["xmlfile"]["tmp_name"];

if (!file_exists($tmp) || filesize($tmp) === 0) {
    die("Arquivo XML vazio");
}

libxml_use_internal_errors(true);
$xml = simplexml_load_file($tmp);

if ($xml === false) {
    foreach (libxml_get_errors() as $err) {
        echo $err->message . "<br>";
    }
    die("XML inválido");
}

$model = new Produtos_model();

foreach ($xml->produto as $produto) {

    $data = [
        "name" => (string)$produto->name,
        "stock" => (int)$produto->stock,
        "price" => (float)$produto->price,
        "tamanho" => (string)$produto->size,
        "cor" => (string)$produto->color,
        "peso_banho" => (int)$produto->peso_banho,
        "milesimos_banho" => (int)$produto->milesimos_banho,
        "tags" => [],
        "photo" => null
    ];

    $model->set_products($data);
}

header("Refresh: 1; url=/produtos");
echo "Importação concluída";
exit;