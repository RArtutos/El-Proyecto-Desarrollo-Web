<?php
require_once __DIR__ . '/../core/conexion.php';

class RegistroModel
{
    public function registrarUsuario(string $usuario, string $hash, string $rol)
    {
        global $conexion;

        mysqli_begin_transaction($conexion);

        try {
            $sql = "INSERT INTO cuenta (usuario, contrasenia, rol) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conexion, $sql);
            if (!$stmt) {
                throw new Exception("PREPARE: " . mysqli_error($conexion));
            }

            mysqli_stmt_bind_param($stmt, "sss", $usuario, $hash, $rol);

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("EXECUTE: " . mysqli_stmt_error($stmt));
            }

            $id = mysqli_insert_id($conexion);

            mysqli_stmt_close($stmt);
            mysqli_commit($conexion);

            return $id; 
        } catch (Exception $e) {
            mysqli_rollback($conexion);

            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['error_servidor'] = $e->getMessage();

            return false;
        }
    }
}
