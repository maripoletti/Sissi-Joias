<?php

declare(strict_types= 1);
require_once __DIR__ . "/../models/precificacao_model.php";
header("Content-Type: application/json");


$db = new precificacao_model();

$metais = $db->buscar_metal();

echo json_encode($metais ?? "", JSON_UNESCAPED_UNICODE);

exit;