<?php
declare(strict_types=1);
require_once __DIR__ . '/../helpers/featureApi.php';

feature_run(function () {
    $user = feature_guard();
    $info = (new user_model())->get_employee_info($user);
    if (!$info) feature_json(['success' => false, 'message' => 'Perfil não encontrado.'], 404);
    return $info;
});
