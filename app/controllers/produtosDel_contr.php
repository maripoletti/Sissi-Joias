<?php

declare(strict_types= 1);
require_once __DIR__ . "/../models/produtos_model.php";
header("Content-Type: application/json");

$db = new produtos_model();

$id = $_POST["id"];

$db->delete_products($id);
exit;