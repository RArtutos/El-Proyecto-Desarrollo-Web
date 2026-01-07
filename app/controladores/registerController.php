<?php
require_once __DIR__ . '/../modelos/registerModel.php';

class RegisterController {
  public function registrar(): void {
    $usuario = trim($_POST['usuario'] ?? '');
    $contrasenia = trim($_POST['contrasenia'] ?? '');
    $rol = trim($_POST['rol'] ?? '');

    if ($usuario === '' || $contrasenia === '' || $rol === '') {
      $_SESSION['error'] = 'Todos los campos son obligatorios.';
      header('Location: /public/add_usuario');
      exit;
    }

    $hash = password_hash($contrasenia, PASSWORD_ARGON2ID);

    $model = new RegistroModel();
    $id = $model->registrarUsuario($usuario, $hash, $rol);

    if ($id) {
      header('Location: /public/dashboard/');
      exit;
    }

    $_SESSION['error'] = 'Error al insertar el registro. Revisa logs.';
    header('Location: /public/add_usuario');
    exit;
  }
}
