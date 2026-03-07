<?php

declare(strict_types= 1);
require_once __DIR__ . "/../models/produtos_model.php";
header("Content-Type: application/json");

$db = new produtos_model();

$input = json_decode(file_get_contents('php://input'), true);

$id = (int)$input["id"];

$db->delete_products($id);
exit;