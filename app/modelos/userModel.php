<?php
require_once __DIR__ . '/../core/conexion.php';


class UserModel {
  public function obtenerPorUsuario($usuario) {
    global $conexion;

    $sql = "SELECT * FROM cuenta WHERE usuario = ? LIMIT 1";
    $stmt = mysqli_prepare($conexion, $sql);
    if (!$stmt) return false;

    mysqli_stmt_bind_param($stmt, "s", $usuario);
    mysqli_stmt_execute($stmt);

    $res = mysqli_stmt_get_result($stmt);
    $fila = $res ? mysqli_fetch_assoc($res) : false;

    mysqli_stmt_close($stmt);
    return $fila ?: false;
  }
}
