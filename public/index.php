<?php
declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

ini_set('session.use_only_cookies', '1');
ini_set('session.use_trans_sid', '0');

session_name('APPSESSID');
session_set_cookie_params([
  'lifetime' => 0,
  'path' => '/',
  'secure' => false,
  'httponly' => true,
  'samesite' => 'Lax',
]);

session_start();

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');

require_once APP_PATH . '/config/config.php';
require_once APP_PATH . '/core/conexion.php';

require_once APP_PATH . '/modelos/cuentaModel.php';
require_once APP_PATH . '/controladores/loginController.php';


require_once APP_PATH . '/modelos/serverModel.php';
require_once APP_PATH . '/controladores/serverController.php';
require_once APP_PATH . '/controladores/serverApiController.php';


require_once APP_PATH . '/modelos/cuentaModel.php';
require_once APP_PATH . '/controladores/registerController.php';

require_once APP_PATH . '/controladores/userController.php';



$BASE_URL = '/public';

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($uri !== '/' && substr($uri, -1) === '/') $uri = rtrim($uri, '/');

if ($BASE_URL !== '/' && str_starts_with($uri, $BASE_URL)) {
  $uri = substr($uri, strlen($BASE_URL));
  if ($uri === '') $uri = '/';
}

$auth = new LoginController();

switch (true) {
  case (($uri === '/' || $uri === '/index.php') && $method === 'GET'):
    header('Location: ' . $BASE_URL . (!empty($_SESSION['usuario']) ? '/dashboard' : '/login'));
    exit;

  case ($uri === '/login' && $method === 'GET'):
    $auth->mostrarLogin();
    exit;

  case ($uri === '/login' && $method === 'POST'):
    $auth->login();
    exit;

  case ($uri === '/logout' && $method === 'GET'):
    $auth->logout();
    exit;

  case (($uri === '/dashboard' || $uri === '/dashboard.php') && $method === 'GET'):
    if (empty($_SESSION['usuario'])) {
      header("Location: {$BASE_URL}/login");
      exit;
    }
    $srv = new ServidorController();
    $srv->dashboard(); 
    exit;

  
  case ($uri === '/add_servidor' && $method === 'GET'):
    if (empty($_SESSION['usuario'])) {
      header("Location: {$BASE_URL}/login"); 
      exit;
    }
    require APP_PATH . '/vistas/dashboard/agregar_servidor.php';
    exit;

  
  case ($uri === '/add_servidor' && $method === 'POST'):
    if (empty($_SESSION['usuario'])) {
      header("Location: {$BASE_URL}/login");
      exit;
    }
    $srv = new ServidorController();
    $srv->guardar();
    exit;

  case ($uri === '/add_usuario' && $method === 'GET'):
    if (empty($_SESSION['usuario'])) {
      header("Location: {$BASE_URL}/login");
      exit;
    }
    require APP_PATH . '/vistas/dashboard/agregar_usuario.php';
    exit;

  case ($uri === '/add_usuario' && $method === 'POST'):
    if (empty($_SESSION['usuario'])) {
      header("Location: {$BASE_URL}/login");
      exit;
    }

  case ($uri === '/list_usuarios' && $method === 'GET'):
    if (empty($_SESSION['usuario'])) {
      http_response_code(401);
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode(['ok' => false, 'error' => 'No autorizado']);
      exit;
    }
    $usr = new userController();
    $usr->listarUsuarios();
    exit; 

  case ($uri === '/edit_usuario' && $method === 'POST'):
    $usr = new userController();
    $usr->editarUsuario();
    exit;

  case ($uri === '/delete_usuario' && $method === 'POST'):
    $usr = new userController();
    $usr->eliminarUsuario();
    exit;

  case ($uri === '/list_usuarios_activos' && $method === 'GET'):
    (new userController())->usuariosActivos();
    break;
  
  case ($uri === '/toggle_usuario' && $method === 'POST'):
    (new userController())->toggleUsuario();
    break;

  case ($uri === '/logout' && $method === 'GET'):
    session_start();
    $_SESSION = [];
    session_destroy();
    header("Location: {$BASE_URL}/login");
    exit;

    $reg = new RegisterController();
    $reg->registrar();
    exit;
    


    case ($uri === '/api/servidores' && $method === 'GET'):
      if (empty($_SESSION['usuario'])) {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'error' => 'No autorizado']);
        exit;
      }
      $srv = new ServidorController();
      $srv->apiServidores();
      exit;
    


    case ($uri === '/api/keepalive' && $method === 'POST'):
      (new ServidorApiController())->keepalive();
      exit;
  
    case ($uri === '/api/shutdown' && $method === 'POST'):
      (new ServidorApiController())->shutdown();
      exit;
  
    
      case ($uri === '/api/whoami' && $method === 'GET'):
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
          'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? null,
          'x_forwarded_for' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null,
          'x_real_ip' => $_SERVER['HTTP_X_REAL_IP'] ?? null,
        ]);
        exit;

        
  default:
    http_response_code(404);
    echo "404";
    exit;
}
