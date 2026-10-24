<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/maletas_model.php';

feature_run(function () {
    feature_guard(true, true);
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') feature_json(['success' => false, 'message' => 'Método não permitido.'], 405);
    $input = feature_input();
    (new Maletas_model())->editarQuantidade(feature_int($input['revId'] ?? null), feature_int($input['prodId'] ?? null),
        feature_int($input['caseId'] ?? 0, 0), feature_int($input['quantidade'] ?? null, 0, 10000));
    return ['success' => true];
});
