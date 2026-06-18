<?php

declare(strict_types= 1);
require_once __DIR__ . "/../models/topRev_model.php";
header("Content-Type: application/json");


$db = new topRev_model();

$input = json_decode(file_get_contents("php://input"), true);

if (!is_array($input)) {
    echo json_encode([
        "success" => false,
        "message" => "JSON inválido."
    ]);
    exit;
}

$response = [];

foreach ($input as $campanha) {

    $id = $campanha["id"] ?? null;

    $nome = trim($campanha["nome"] ?? "");
    $descricao = trim($campanha["descricao"] ?? "");
    $inicio = $campanha["inicio"] ?? null;
    $fim = $campanha["fim"] ?? null;

    if (is_numeric($id)) {

        $result = $db->atualizar_campanha(
            (int)$id,
            $nome,
            $descricao,
            $inicio,
            $fim
        );

    } else {

        $result = $db->criar_campanha(
            $nome,
            $descricao,
            $inicio,
            $fim
        );
    }

    $response[] = $result;
}

echo json_encode($response ?? "", JSON_UNESCAPED_UNICODE);

exit;