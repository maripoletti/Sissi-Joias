<?php
declare(strict_types=1);
require_once __DIR__ . "/../models/user_model.php";
header("Content-Type: application/json; charset=UTF-8");


$userId = (int) $_SESSION["user_id"];

if (!isset($userId)) {
    http_response_code(403);
    echo json_encode(["error" => "Não autenticado"]);
    exit;
}
    
$model = new user_model();
$info = $model->get_employee_info($userId);

$json = json_encode($info, JSON_UNESCAPED_UNICODE);

if($json === false){
    error_log("Erro no json_encode: " . json_last_error_msg());
    echo json_encode(["nome"=>"Erro ao carregar", "total"=>0]);
} else {
    echo $json;
}
