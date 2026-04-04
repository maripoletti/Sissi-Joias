<?php

declare(strict_types= 1);
require_once __DIR__ . "/../models/user_model.php";
require_once __DIR__ . "/../helpers/imageUpload.php";
header("Content-Type: application/json");

$upload = new ImageUpload();
$model = new user_model();

$fotoPath = $upload->image($_FILES['imagem'] ?? []);
$userId = (int) $_SESSION["user_id"];

$model->set_image($userId, $fotoPath);

echo json_encode([
    "status" => "ok",
    "foto" => $fotoPath
]);
exit;