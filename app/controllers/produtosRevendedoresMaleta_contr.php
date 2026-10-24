<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/maletas_model.php';

feature_run(function () {
    feature_guard(true);
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') feature_json(['success' => false, 'message' => 'Método não permitido.'], 405);
    $user = isset($_GET['revendedora_id']) ? feature_int($_GET['revendedora_id']) : null;
    return (new Maletas_model())->listar($user);
});
