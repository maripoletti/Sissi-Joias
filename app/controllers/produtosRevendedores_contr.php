<?php
declare(strict_types=1);
require_once __DIR__ . '/../helpers/featureApi.php';
require_once __DIR__ . '/../models/produtosRevendedores_model.php';

feature_run(function () {
    feature_guard(true);
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') feature_json(['success' => false, 'message' => 'Método não permitido.'], 405);
    $input = feature_input();
    $filters = $input['filters'] ?? [];
    $pagination = $input['pagination'] ?? [];
    if (!is_array($filters) || !is_array($pagination)) throw new InvalidArgumentException('Filtros ou paginação inválidos.');
    $product = $filters['produto'] ?? null;
    $employee = $filters['revendedor'] ?? null;
    if (($product !== null && !is_string($product)) || ($employee !== null && !is_string($employee))) {
        throw new InvalidArgumentException('Filtros inválidos.');
    }
    $page = feature_int($pagination['page'] ?? 1, 1, 1000000);
    $limit = feature_int($pagination['limit'] ?? 50, 1, 10000);
    return (new produtosRevendedores_model())->pegar_produtos($product, $employee, $page, $limit);
});
