<?php
require_once __DIR__ . '/../modelos/cuentaModel.php';

class userController {

  public function listarUsuarios(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $idUsuario = (int)($_SESSION['id'] ?? 0);

        if ($idUsuario <= 0) {
            header('Location: /public/login');
            exit;
        }

        $modelCuenta = new cuentaModel();
        
        if (!$modelCuenta->esAdminPorId((int)$idUsuario)) {
            header('Location: /public/');
            exit;
        }

        
        $usuarios = $modelCuenta->listarUsuarios();
        require APP_PATH . '/vistas/dashboard/listar_usuarios.php';

    }

    public function editarUsuario(): void {
        $id         = (int)($_POST['id'] ?? 0);
        $usuario    = trim($_POST['usuario'] ?? '');
        $rol        = $_POST['rol'] ?? 'usuario';
        $estaActivo = (int)($_POST['estaActivo'] ?? 1);
        $contrasenia= $_POST['contrasenia'] ?? '';

        if ($id <= 0 || $usuario === '') {
            $_SESSION['error_servidor'] = 'Datos inválidos';
            header('Location: /public/list_usuarios');
            exit;
        }

        $hash = password_hash($contrasenia, PASSWORD_ARGON2ID);

        $model = new cuentaModel();
        $ok = $model->editarUsuario($id, $usuario, $rol, $estaActivo, $hash);

        $_SESSION[$ok ? 'ok_servidor' : 'error_servidor']
            = $ok ? 'Usuario actualizado correctamente' : 'Error al actualizar';

        header('Location: /public/list_usuarios');
        exit;
    }

    public function eliminarUsuario(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 🔐 CSRF
        if (
            empty($_SESSION['csrf_token']) ||
            empty($_POST['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
        ) {
            $_SESSION['error_servidor'] = 'Token CSRF inválido';
            header('Location: /public/list_usuarios');
            exit;
        }

        // 🔒 Autenticación
        if (empty($_SESSION['id']) || $_SESSION['rol'] !== 'admin') {
            $_SESSION['error_servidor'] = 'No autorizado';
            header('Location: /public/list_usuarios');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['error_servidor'] = 'ID inválido';
            header('Location: /public/list_usuarios');
            exit;
        }

        // 🚫 No permitir auto-eliminación
        if ($id === (int)$_SESSION['id']) {
            $_SESSION['error_servidor'] = 'No puedes eliminar tu propio usuario';
            header('Location: /public/list_usuarios');
            exit;
        }

        $model = new cuentaModel();
        $ok = $model->eliminarUsuario($id);

        $_SESSION[$ok ? 'ok_servidor' : 'error_servidor'] =
            $ok ? 'Usuario eliminado correctamente' : 'Error al eliminar usuario';

        header('Location: /public/list_usuarios');
        exit;
    }

    public function usuariosActivos() {
        $model = new cuentaModel();
        echo json_encode($model->listarUsuariosActivos());
        exit;
    }
    
    public function toggleUsuario() {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = (int)$data['id'];
        $accion = $data['accion'];
    
        $model = new cuentaModel();
    
        if ($accion === 'add') {
            $model->activarUsuario($id);
        } else {
            $model->desactivarUsuario($id);
        }
    
        echo json_encode(['ok' => true]);
        exit;
    }



    public function buscarUsuario(string $usuario): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $idUsuario = (int)($_SESSION['id'] ?? 0);

        if ($idUsuario <= 0) {
            header('Location: /public/login');
            exit;
        }

        $modelCuenta = new cuentaModel();
        
        if (!$modelCuenta->esAdminPorId((int)$idUsuario)) {
            header('Location: /public/');
            exit;
        }

        
        $usuarios = $modelCuenta->listarUsuarios();
        require APP_PATH . '/vistas/dashboard/listar_usuarios.php';

    }
}
