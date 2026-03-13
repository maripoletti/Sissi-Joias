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
$xmlContent = file_get_contents($tmp);
$xmlContent = preg_replace('/^\xEF\xBB\xBF/', '', $xmlContent);

libxml_use_internal_errors(true);
$xml = simplexml_load_string($xmlContent);

if ($xml === false) {
    die("XML inválido");
}

$model = new Produtos_model();

$ns = $xml->getNamespaces(true);
$xml->registerXPathNamespace('nfe', $ns['']);

$itens = $xml->xpath("//nfe:det");

foreach ($itens as $item) {

    $prod = $item->children($ns[''])->prod;

    $codigo = (string)$prod->cProd;
    $nome = (string)$prod->xProd;
    $quantidade = (float)$prod->qCom;
    $preco = (float)$prod->vUnCom;

    $data = [
        "code" => $codigo,
        "name" => $nome,
        "stock" => $quantidade,
        "price" => $preco
    ];

    $model->import_product($data);

    echo "$codigo - $nome - $quantidade - $preco <br>";
}

header("Refresh: 3; url=/produtos");
echo "Importação concluída";
exit;