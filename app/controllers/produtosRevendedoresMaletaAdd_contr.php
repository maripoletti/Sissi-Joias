<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/maletas_model.php';

feature_run(function () {
    feature_guard(true, true);
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') feature_json(['success' => false, 'message' => 'Método não permitido.'], 405);
    (new Maletas_model())->reagrupar(feature_input());
    return ['success' => true];
});
