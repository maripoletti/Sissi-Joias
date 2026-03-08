<?php

declare(strict_types= 1);
require_once __DIR__ . "/../models/impressoras_model.php";
header("Content-Type: application/json");

$db = new impressoras_model();

$impressoras = $db->get_printers();

foreach ($impressoras as &$row) {
    $row["caps"] = $row["caps"] ? explode(",", $row['caps']) : []; 
}

echo json_encode($impressoras);
exit;