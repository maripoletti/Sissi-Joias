<?php

declare(strict_types=1);
require_once __DIR__ . "/../models/precificacao_model.php";
header("Content-Type: application/json");

$db = new precificacao_model();

$input = json_decode(file_get_contents("php://input"), true);

$result = $db->criar_produto([
    "nome" => $input["produto"]["nome"] ?? "",
    "codigoExterno" => $input["produto"]["codigoExterno"] ?? null,
    "unidadeEstoque" => $input["produto"]["unidadeEstoque"] ?? null,
    "preco" => $input["produto"]["preco"] ?? 0,
    "peso" => $input["produto"]["peso"] ?? 0,
    "milesimos" => $input["produto"]["milesimos"] ?? 0,
    "milesimosBanho" => $input["produto"]["milesimosBanho"] ?? 0,
    "metal" => $input["produto"]["metal"] ?? null,
    "metalBanho" => $input["produto"]["metalBanho"] ?? null,
    "custoInsumo" => $input["produto"]["custoInsumo"] ?? 0,
    "custoBruto" => $input["produto"]["custoCompraBruto"] ?? 0,
    "categoria" => $input["produto"]["categoria"] ?? []
]);

echo json_encode($result);