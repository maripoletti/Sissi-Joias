<?php

require_once __DIR__ . "/../models/produtos_model.php";

$model = new Produtos_model();


if (isset($_POST['importar'])) {

    $xmlContent = base64_decode($_POST['xmltemp']);
    $xml = simplexml_load_string($xmlContent);

    $ns = $xml->getNamespaces(true);
    $xml->registerXPathNamespace('nfe', $ns['']);

    $itens = $xml->xpath("//nfe:det");

    $map = $_POST['map'];

    foreach ($itens as $item) {

        $prod = $item->children($ns[''])->prod;

        $data = [
            "code" => (string)$prod->{$map['code']},
            "name" => (string)$prod->{$map['name']},
            "stock" => (float)$prod->{$map['stock']},
            "price" => (float)$prod->{$map['price']}
        ];

        $model->import_product($data);

        $importados[] = $data;
        $_SESSION['importados'] = $importados;
    }

    header("Location: /produtos/importados");
    exit;
}


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

$xmlContent = file_get_contents($tmp);
$xmlContent = preg_replace('/^\xEF\xBB\xBF/', '', $xmlContent);

$xml = simplexml_load_string($xmlContent);

if ($xml === false) {
    die("XML inválido");
}

$ns = $xml->getNamespaces(true);
$xml->registerXPathNamespace('nfe', $ns['']);

$itens = $xml->xpath("//nfe:det");

$prod = $itens[0]->prod;

$colunasXml = [];
foreach ($prod->children() as $tag => $valor) {
    $colunasXml[] = $tag;
}

$xmlEncoded = base64_encode($xmlContent);


require __DIR__ . "/../views/mapearXml_view.php";
exit;