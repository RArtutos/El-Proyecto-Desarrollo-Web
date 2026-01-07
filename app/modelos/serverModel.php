<?php
require_once __DIR__ . '/../core/conexion.php';

class ServidorModel
{
    public static function agregarServer(
        string $alias,
        string $ip,
        ?string $dominio,
        int $id,
        string $tokenHash
    ): bool {
        global $conexion;

        mysqli_begin_transaction($conexion);

        try {
            $sql1 = "INSERT INTO servidor (alias, ip, dominio, dueno_id, token)
                     VALUES (?, ?, ?, ?, ?)";
            $stmt1 = mysqli_prepare($conexion, $sql1);
            if (!$stmt1) {
                throw new Exception(mysqli_error($conexion));
            }

            mysqli_stmt_bind_param($stmt1, "sssis", $alias, $ip, $dominio, $id, $tokenHash);

            if (!mysqli_stmt_execute($stmt1)) {
                throw new Exception(mysqli_stmt_error($stmt1));
            }

            $id_servidor = mysqli_insert_id($conexion);
            mysqli_stmt_close($stmt1);

            $rol = 'admin';
            $sql2 = "INSERT INTO usuarios_servidor (id_servidor, id_usuario, rol)
                     VALUES (?, ?, ?)";
            $stmt2 = mysqli_prepare($conexion, $sql2);
            if (!$stmt2) {
                throw new Exception(mysqli_error($conexion));
            }

            mysqli_stmt_bind_param($stmt2, "iis", $id_servidor, $id, $rol);

            if (!mysqli_stmt_execute($stmt2)) {
                throw new Exception(mysqli_stmt_error($stmt2));
            }

            mysqli_stmt_close($stmt2);

            mysqli_commit($conexion);
            return true;

        } catch (Exception $e) {
            mysqli_rollback($conexion);
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['error_servidor'] = $e->getMessage();
            return false;
        }
    }

    public static function listarPorUsuario(int $id_usuario): array
    {
        global $conexion;

        $sql = "
            SELECT
                s.id,
                s.alias,
                s.ip,
                s.dominio,
                s.estado,
                us.rol AS rol_usuario
            FROM usuarios_servidor us
            INNER JOIN servidor s ON s.id = us.id_servidor
            WHERE us.id_usuario = ?
            ORDER BY s.alias ASC
        ";

        $stmt = mysqli_prepare($conexion, $sql);
        if (!$stmt) return [];

        mysqli_stmt_bind_param($stmt, "i", $id_usuario);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_stmt_close($stmt);

        return $rows ?: [];
    }

    public static function validarTokenPorIp(string $ipRequest, string $tokenPlano): bool
    {
        global $conexion;

        $sql = "SELECT token FROM servidor WHERE ip = ? LIMIT 1";
        $stmt = mysqli_prepare($conexion, $sql);
        if (!$stmt) return false;

        mysqli_stmt_bind_param($stmt, "s", $ipRequest);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($res);
        mysqli_stmt_close($stmt);

        if (!$row) return false;

        $hash = $row['token'] ?? '';
        if ($hash === '') return false;

        return password_verify($tokenPlano, $hash);
    }

    public static function marcarActivoPorIp(string $ipRequest): bool
    {
        global $conexion;

        $sql = "UPDATE servidor SET estado = 'ENCENDIDO', ultima_senal = NOW() WHERE ip = ? LIMIT 1";
        $stmt = mysqli_prepare($conexion, $sql);
        if (!$stmt) return false;

        mysqli_stmt_bind_param($stmt, "s", $ipRequest);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return (bool)$ok;
    }

    public static function marcarApagadoPorIp(string $ipRequest): bool
    {
        global $conexion;

        $sql = "UPDATE servidor SET estado = 'APAGADO', ultima_senal = NOW() WHERE ip = ? LIMIT 1";
        $stmt = mysqli_prepare($conexion, $sql);
        if (!$stmt) return false;

        mysqli_stmt_bind_param($stmt, "s", $ipRequest);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return (bool)$ok;
    }
    public static function marcarIndeterminadoPorUsuario(int $id_usuario, int $segundos): void
    {
        global $conexion;

        $sql = "
            UPDATE servidor s
            INNER JOIN usuarios_servidor us ON us.id_servidor = s.id
            SET s.estado = 'INDETERMINADO'
            WHERE us.id_usuario = ?
            AND s.estado = 'ENCENDIDO'
            AND (s.ultima_senal IS NULL OR TIMESTAMPDIFF(SECOND, s.ultima_senal, NOW()) > ?)
        ";

        $stmt = mysqli_prepare($conexion, $sql);
        if (!$stmt) return;

        mysqli_stmt_bind_param($stmt, "ii", $id_usuario, $segundos);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    

}
