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

  public function listarUsuarios() {
    global $conexion;

    $sql = "SELECT * FROM cuenta";
    $stmt = mysqli_prepare($conexion, $sql);
    if (!$stmt) return false;

    mysqli_stmt_bind_param($stmt, "s", $usuario);
    mysqli_stmt_execute($stmt);

    $res = mysqli_stmt_get_result($stmt);
    $fila = $res ? mysqli_fetch_assoc($res) : false;

    mysqli_stmt_close($stmt);
    return $fila ?: false;
  }


  public function registrarUsuario(string $usuario, string $hash, string $rol) {
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

  public function listarUsuariosActivos() {
    global $conexion;
        
      $sql = "SELECT id, usuario, rol FROM cuenta WHERE estaActivo = 1";
        
      $res = mysqli_query($conexion, $sql);
      return mysqli_fetch_all($res, MYSQLI_ASSOC);
  }

  public function desactivarUsuario(int $id) {
    global $conexion;
      $sql = "UPDATE cuenta SET estaActivo = 0 WHERE id = ?";
      $stmt = mysqli_prepare($conexion, $sql);
      mysqli_stmt_bind_param($stmt, "i", $id);
      return mysqli_stmt_execute($stmt);
  }
        
  public function activarUsuario(int $id) {
    global $conexion;
      $sql = "UPDATE cuenta SET estaActivo = 1 WHERE id = ?";
      $stmt = mysqli_prepare($conexion, $sql);
      mysqli_stmt_bind_param($stmt, "i", $id);
      return mysqli_stmt_execute($stmt);
  }



  
  
  
}
