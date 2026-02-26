<?php
require_once '../config/session.config.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

switch ($uri) {
  case '/':
    if(isset($_SESSION['user_id'])) {
      header('Location: /dashboard');
      break;
    } else {
      header('Location: /login');
      break;
    }
  case '/login':

    if ($method === 'GET') {
      require_once '../app/views/login_view.php';
      break;
    }

    if ($method === 'POST') {
      require_once '../app/controllers/login_contr.php';
    }
    break;

  case '/dashboard': 
    if (isset($_SESSION['user_id'])) {
      require_once '../app/views/dashboard_view.php';
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

  case '/admin':
    require_once '../app/views/admin_view.php';
    break;

  /* case '/a':
    require_once __DIR__ . '/../app/models/signup_model.php';

          require_once __DIR__ ."/../config/dbh.config.php";
          $dbh = new Dbh();
          $pdo = $dbh->connect();

          $query = "SELECT * FROM Temp_PendingUsers;";
          $stmt = $pdo->prepare($query);
          $stmt->execute();

          $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $signup = new Signup_model();
    $signup->create_user($result[0]["Email"], $result[0]["Pwd"], $result[0]["FullName"], $result[0]["Phone"], $result[0]["PendUserID"]);
    break;
  
  case '/b':
    require_once __DIR__ . '/../models/signup_model.php';
          require_once __DIR__ ."/../../config/dbh.config.php";
          $dbh = new Dbh();
          $pdo = $dbh->connect();

          $query = "SELECT * FROM Temp_PendingUsers;";
          $stmt = $pdo->prepare($query);
          $stmt->execute();

          $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $signup = new Signup_model();
    $signup->reject_user($result[0]['PendUserID']);
    break; */

  default:
    require_once '../app/views/404.html';
}
