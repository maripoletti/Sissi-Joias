<?php
declare(strict_types=1);
require_once __DIR__ . '/../helpers/featureApi.php';
require_once __DIR__ . '/../models/produtos_model.php';
feature_run(function () {
    feature_guard(true);
    return (new Produtos_model())->get_employees();
});
