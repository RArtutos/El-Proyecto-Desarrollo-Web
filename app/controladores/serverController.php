<?php
require_once __DIR__ . '/../modelos/serverModel.php';

class ServidorController
{
    public function guardar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /public/dashboard');
            exit;
        }

        $alias   = trim($_POST['alias'] ?? '');
        $ip      = trim($_POST['ip'] ?? '');
        $dominio = trim($_POST['dominio'] ?? '');

        $id = (int)($_SESSION['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['error_servidor'] = 'No hay sesión válida (id de usuario no encontrado).';
            header('Location: /public/add_servidor');
            exit;
        }

        if ($alias === '' || $ip === '') {
            $_SESSION['error_servidor'] = 'Alias e IP son obligatorios.';
            header('Location: /public/add_servidor');
            exit;
        }

        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $_SESSION['error_servidor'] = 'La IP no es válida (solo IPv4).';
            header('Location: /public/add_servidor');
            exit;
        }

        $dominio = ($dominio === '') ? null : $dominio;

        $tokenPlano = bin2hex(random_bytes(32));
        $tokenHash  = password_hash($tokenPlano, PASSWORD_ARGON2ID);

        $ok = ServidorModel::agregarServer($alias, $ip, $dominio, $id, $tokenHash);

        if (!$ok) {
            header('Location: /public/add_servidor');
            exit;
        }

        $_SESSION['ok_servidor'] = 'Servidor agregado. Copia el token y guárdalo en el demonio del servidor.';
        $_SESSION['token_servidor_creado'] = $tokenPlano;

        header('Location: /public/add_servidor');
        exit;
    }

    public function dashboard()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $idUsuario = (int)($_SESSION['id'] ?? 0);

        if ($idUsuario <= 0) {
            header('Location: /public/login');
            exit;
        }

        $servidores = ServidorModel::listarPorUsuario($idUsuario);

        require APP_PATH . '/vistas/dashboard/dashboard.php';
        exit;
    }



    public function apiServidores(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $idUsuario = (int)($_SESSION['id'] ?? 0);
        if ($idUsuario <= 0) {
            http_response_code(401);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => false, 'error' => 'No autorizado']);
            exit;
        }
        ServidorModel::marcarIndeterminadoPorUsuario($idUsuario, 60);
        $servidores = ServidorModel::listarPorUsuario($idUsuario);


        $servidores = ServidorModel::listarPorUsuario($idUsuario);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => true, 'servidores' => $servidores]);
        exit;
    }

}
