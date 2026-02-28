<?php
require_once '../config/session.config.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

switch ($uri) {
  case '/':
    require_once '../app/views/login_view.php';
    break;
  case '/login':
    if ($method === 'GET') {
      require_once '../app/views/login_view.php';
      break;
    }

    if ($method === 'POST') {
      require_once '../app/controllers/login_contr.php';
    }
    break;
  case '/paineldecontrole': 
    if (!isset($_SESSION['user_id'])) {
      require_once '../app/views/paineldecontrole_view.php';
      break;
    } else {
      require_once '../app/views/login_view.php';
      break;
    }
  case '/cadastro':
    if ($method === 'GET') {
      require_once '../app/views/cadastro_view.php';
      break;
    }
  
  default:
    require_once '../app/views/404.html';
}

