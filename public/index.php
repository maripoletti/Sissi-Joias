<?php
require_once '../config/session.config.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

switch ($uri) {
  case '/':
    require_once '../app/views/login_view.php';
    break;
  case '/login':
    if ($method === 'post') {
      require_once '../app/controllers/login_contr.php';
    }
  case '/dashboard': 
    require_once '../app/views/dashboard_view.php';
    break;
  
  default:
    require_once '../app/views/404.html';
}