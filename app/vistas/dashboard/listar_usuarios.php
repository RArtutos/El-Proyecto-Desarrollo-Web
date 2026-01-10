<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="/../vistas/styles/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <title>Lista de usuarios</title>
</head>

<body class="bg-dark text-white">

<header class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-secondary p-3">
  <div class="container-fluid">
    <h1 class="h3 mb-0 text-white">EuroTech</h1>
    <ul class="navbar-nav flex-row">

    <div>
      <a class="btn btn-success" href="/public/add_usuario">Agregar Usuario</a>      
      <a href="/public/dashboard" class="btn btn-outline-secondary">Regresar</a>
    </div>
  </div>
</header>

<main class="container-fluid py-4">

  <section class="mb-4">
    <h1 class="text-white">Lista de usuarios</h1>

    <?php if (!empty($_SESSION['error_servidor'])): ?>
      <div class="alert alert-danger mt-3">
        <?= htmlspecialchars($_SESSION['error_servidor']) ?>
      </div>
      <?php unset($_SESSION['error_servidor']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['ok_servidor'])): ?>
      <div class="alert alert-success mt-3">
        <?= htmlspecialchars($_SESSION['ok_servidor']) ?>
      </div>
      <?php unset($_SESSION['ok_servidor']); ?>
    <?php endif; ?>
  </section>

  <section>
    <table class="table table-dark table-hover align-middle">
      <thead class="table-secondary">
        <tr>
          <th>Usuario</th>
          <th>Rol</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>

      <tbody>
      <?php if (empty($usuarios)): ?>
        <tr>
          <td colspan="4" class="text-center text-white-50">
            No hay usuarios registrados
          </td>
        </tr>
      <?php else: ?>
        <?php foreach ($usuarios as $u): ?>
          <?php $activo = (int)$u['estaActivo'] === 1; ?>
          <tr>
            <td>
              <strong><?= htmlspecialchars($u['usuario']) ?></strong>
              <div class="small text-white-50">ID: <?= (int)$u['id'] ?></div>
            </td>

            <td>
              <span class="badge bg-primary text-dark">
                <?= htmlspecialchars($u['rol']) ?>
              </span>
            </td>

            <td>
              <span class="badge <?= $activo ? 'bg-success' : 'bg-danger' ?>">
                <?= $activo ? 'ACTIVO' : 'INACTIVO' ?>
              </span>
            </td>

            <td class="text-nowrap">
              <button
                class="btn btn-sm btn-success"
                data-bs-toggle="modal"
                data-bs-target="#modalEditarUsuario"
                data-id="<?= (int)$u['id'] ?>"
                data-usuario="<?= htmlspecialchars($u['usuario'], ENT_QUOTES) ?>"
                data-rol="<?= htmlspecialchars($u['rol'], ENT_QUOTES) ?>"
                data-activo="<?= (int)$u['estaActivo'] ?>"
              >
                Editar
              </button>

              <form
                method="POST"
                action="/public/delete_usuario"
                class="d-inline"
                onsubmit="return confirm('¿Eliminar usuario?');"
              >
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">

                <button type="submit" class="btn btn-sm btn-danger">
                  Eliminar
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
  </section>
</main>

<!-- MODAL EDITAR -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="/public/edit_usuario" class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Editar usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <!-- <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>"> -->
        <input type="hidden" name="id" id="edit-id">

        <div class="mb-3">
          <label class="form-label">Usuario</label>
          <input type="text" class="form-control" name="usuario" id="edit-usuario" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Rol</label>
          <select class="form-select" name="rol" id="edit-rol">
            <option value="admin">Admin</option>
            <option value="user">Usuario</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Nueva Contraseña</label>
          <input type="password" class="form-control" name="contrasenia" id="contrasenia" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Confirmar Contraseña</label>
          <input type="password" class="form-control" name="confirmar" id="confirmar" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Estado</label>
          <select class="form-select" name="estaActivo" id="edit-activo">
            <option value="1">Activo</option>
            <option value="0">Inactivo</option>
          </select>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success">Guardar cambios</button>
      </div>

    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('modalEditarUsuario');

  modal.addEventListener('show.bs.modal', event => {
    const btn = event.relatedTarget;

    document.getElementById('edit-id').value = btn.dataset.id;
    document.getElementById('edit-usuario').value = btn.dataset.usuario;
    document.getElementById('edit-rol').value = btn.dataset.rol;
    document.getElementById('edit-activo').value = btn.dataset.activo;
  });
});
</script>

<script src="/public/JS/script.js"></script>

</body>
</html>