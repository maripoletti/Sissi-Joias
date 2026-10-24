<?php
declare(strict_types=1);
require_once __DIR__ . '/../helpers/featureApi.php';
require_once __DIR__ . '/../models/cupons_model.php';

feature_run(function () {
    $method = $_SERVER['REQUEST_METHOD'];
    $user = feature_guard($method !== 'GET', $method !== 'GET');
    $model = new Cupons_model();
    if ($method === 'GET') {
        $admin = $_SESSION['role'] === 2;
        if ($admin && isset($_GET['revendedora_id'])) $user = feature_int($_GET['revendedora_id']);
        return $model->listar($user, $admin);
    }
    $input = feature_input();
    if ($method === 'POST') {
        $model->adicionar(feature_int($input['revendedora_id'] ?? null),
            feature_int($input['desconto'] ?? null, 1, 100), feature_int($input['quantidade'] ?? null, 1, 10000));
    } elseif ($method === 'PATCH') {
        $delta = feature_int($input['delta'] ?? null, -1, 1);
        if (!$delta) throw new InvalidArgumentException('Informe um ajuste de +1 ou -1.');
        $model->ajustar(feature_int($input['id'] ?? null), $delta);
    } elseif ($method === 'DELETE') {
        $model->ajustar(feature_int($input['id'] ?? null), null);
    } else {
        feature_json(['success' => false, 'message' => 'Método não permitido.'], 405);
    }
    return ['success' => true];
});
