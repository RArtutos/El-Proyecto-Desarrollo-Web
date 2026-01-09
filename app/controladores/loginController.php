<?php
declare(strict_types=1);

class LoginController
{
  public function mostrarLogin(): void
  {
    $error = $_SESSION['login_error'] ?? null;
    unset($_SESSION['login_error']);

    require __DIR__ . '/../vistas/auth/login.php';
  }

  public function login(): void
  {
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
      header("Location: /public/login");
      exit;
    }

    $usuario    = trim($_POST['usuario'] ?? '');
    $contrasenia = $_POST['contrasenia'] ?? '';

    $model = new UserModel();
    $user  = $model->obtenerPorUsuario($usuario);

    if ($user && !empty($user['contrasenia']) && password_verify($contrasenia, $user['contrasenia'])) {
      session_regenerate_id(true);

      $_SESSION['usuario'] = $user['usuario'] ?? $usuario;
      $_SESSION['nombre']  = $user['nombre'] ?? $usuario;
      $_SESSION['id'] = $user['id'] ?? null;
      header("Location: /public/dashboard");
      exit;
    }

    $_SESSION['login_error'] = 'Usuario o contraseña incorrectos.';
    header("Location: /public/login");
    exit;
  }

  public function logout(): void
  {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
      $p = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'] ?? '', (bool)($p['secure'] ?? false), (bool)($p['httponly'] ?? true));
    }
    session_destroy();

    header('Location: /public/login');
    exit;
  }
}
