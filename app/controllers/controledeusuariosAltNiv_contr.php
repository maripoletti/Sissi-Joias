<?php
declare(strict_types=1);
require_once __DIR__ . '/../helpers/featureApi.php';
require_once __DIR__ . '/../models/controledeusuarios_model.php';

feature_run(function () {
    feature_guard(true, true);
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') feature_json(['success' => false, 'message' => 'Método não permitido.'], 405);
    $input = feature_input();
    $level = $input['nivel'] ?? null;
    if (!is_string($level)) throw new InvalidArgumentException('Nível inválido.');
    (new ControledeusuariosModel())->switch_lvl(feature_int($input['id'] ?? null), $level);
    return ['success' => true, 'nivel' => $level];
});
