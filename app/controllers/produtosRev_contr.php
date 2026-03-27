<?php
declare(strict_types=1);
require_once __DIR__ . "/../models/produtos_model.php";
header("Content-Type: application/json; charset=UTF-8");

$model = new produtos_model();

if($_SESSION['role'] = 2) {
    $employees = $model->get_employees();
    echo json_encode($employees, JSON_UNESCAPED_UNICODE);
    exit();
} else {
    die();
}