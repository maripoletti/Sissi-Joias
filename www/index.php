<?php
require_once '../config/session.config.php';
require_once '../app/middleware/authMiddleware.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

switch ($uri) {
  case '/':
    if(!isset($_SESSION['user_id'])) {
      header('Location: /paineldecontrole');
      break;
    } else {
      header('Location: /login');
      break;
    }
  case '/login':
    if (!isset($_SESSION['user_id'])) {
        header('Location: /paineldecontrole');
        exit;
    }

    if ($method === 'GET') {
        require_once '../app/views/login_view.php';
    } 
    elseif ($method === 'POST') {
        require_once '../app/controllers/login_contr.php';
    }

    break;
  case '/paineldecontrole': 
    if (!isset($_SESSION['user_id'])) {
      require_once '../app/views/paineldecontrole_view.php';
      break;
    } else {
      header('Location: /login');
      break;
    }

  case '/cadastro':
    if ($method === 'GET') {
      require_once '../app/views/signup_view.php';
      break;
    }
    if ($method === 'POST') {
      require_once '../app/controllers/signup_contr.php';
      break;
    }
    
    
  case '/novavenda':
    if (!isset($_SESSION['user_id'])) {
      if ($method === 'GET') {
        require_once '../app/views/novavenda_view.php';
        break;
      }
      if ($method === 'POST') {
        require_once '../app/controllers/novavenda_contr.php';
        break;
      }
    } else {
      header('Location: /login');
      break;
    }
  case'/vendas':
    if (!isset($_SESSION['user_id'])) {
      if ($method === 'GET') {
        require_once '../app/views/vendas_view.php';
        break;
      }
    } else {
      header('Location: /login');
      break;
    }
  case '/controledeusuarios':
    if (!isset($_SESSION['user_id']) /* && $_SESSION['role'] == '2' */) {
      require_once '../app/views/controledeusuarios_view.php';
      break;
    } else {
      header('Location: /login');
      break;
    }
    
  case '/api/controledeusuarios':
    if(!isset($_SESSION['user_id'])) {
      require_once '../app/controllers/controledeusuariosGet_contr.php';
      break;
    }

  case '/api/controledeusuarios/alterarnivel':
    if(!isset($_SESSION['user_id'])) {
      require_once '../app/controllers/controledeusuariosAltNiv_contr.php';
      break;
    }
    
  case '/api/controledeusuarios/aprovar':
    if(!isset($_SESSION['user_id'])) {
      require_once '../app/controllers/controledeusuariosAprovar_contr.php';
      break;
    }

  case '/api/controledeusuarios/rejeitar':
    if(!isset($_SESSION['user_id'])) {
      require_once '../app/controllers/controledeusuariosRejeitar_contr.php';
      break;
    }

    case '/produtos':
      if(!isset($_SESSION['user_id'])) {
      if ($method === 'GET') {
        require_once '../app/views/produtos_view.php';
        break;
      }
    } else {
      header('Location: /login');
      break;
    }
    break;

  case '/api/produtos':
    if(!isset($_SESSION['user_id'])) {
      require_once '../app/controllers/produtosGet_contr.php';
      break;
    }

  case '/api/produtos/update':
    if ($_SESSION['role'] == '2') {
      require_once '../app/controllers/produtosUpdate_contr.php';
      break;
    } else {
      AuthMiddleware::user();
    }
  case '/api/produtos/add':
    if ($_SESSION['role'] == '2') {
      require_once '../app/controllers/produtosAdd_contr.php';
      break;
    } else {
      AuthMiddleware::user();
    }

  case '/api/produtos/delete':
    if ($_SESSION['role'] == '2') {
      require_once '../app/controllers/produtosDel_contr.php';
      break;
    } else {
      AuthMiddleware::user();
    }
  default:
    require_once '../app/views/404.html';
}

