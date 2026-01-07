<?php
require_once __DIR__ . '/../modelos/serverModel.php';

class ServidorApiController
{
    private function json(int $status, array $data): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    private function clientIp(): string
    {
        $xReal = trim($_SERVER['HTTP_X_REAL_IP'] ?? '');
        if ($xReal !== '') return $xReal;

        $xff = trim($_SERVER['HTTP_X_FORWARDED_FOR'] ?? '');
        if ($xff !== '') {
            $parts = array_map('trim', explode(',', $xff));
            if (!empty($parts[0])) return $parts[0];
        }

        return $_SERVER['REMOTE_ADDR'] ?? '';
    }


    public function keepalive(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(405, ['ok' => false, 'error' => 'Method not allowed']);
        }

        $token = trim($_POST['token'] ?? '');
        if ($token === '') {
            $this->json(400, ['ok' => false, 'error' => 'token es obligatorio']);
        }

        $ip = $this->clientIp();
        if ($ip === '') {
            $this->json(400, ['ok' => false, 'error' => 'IP no detectada']);
        }

        if (!ServidorModel::validarTokenPorIp($ip, $token)) {
            $this->json(401, ['ok' => false, 'error' => 'No autorizado']);
        }

        if (!ServidorModel::marcarActivoPorIp($ip)) {
            $this->json(500, ['ok' => false, 'error' => 'No se pudo actualizar estado']);
        }

        $this->json(200, ['ok' => true, 'estado' => 'ENCENDIDO']);
    }

    public function shutdown(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(405, ['ok' => false, 'error' => 'Method not allowed']);
        }

        $token = trim($_POST['token'] ?? '');
        if ($token === '') {
            $this->json(400, ['ok' => false, 'error' => 'token es obligatorio']);
        }

        $ip = $this->clientIp();
        if ($ip === '') {
            $this->json(400, ['ok' => false, 'error' => 'IP no detectada']);
        }

        if (!ServidorModel::validarTokenPorIp($ip, $token)) {
            $this->json(401, ['ok' => false, 'error' => 'No autorizado']);
        }

        if (!ServidorModel::marcarApagadoPorIp($ip)) {
            $this->json(500, ['ok' => false, 'error' => 'No se pudo actualizar estado']);
        }

        $this->json(200, ['ok' => true, 'estado' => 'APAGADO']);
    }
}
