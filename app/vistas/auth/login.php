<?php $sid = session_id(); ?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Pantalla de Inicio de Sesión</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    />
  </head>

  <body class="bg-dark text-white d-flex flex-column" style="min-height: 100vh">
    <header class="p-3"></header>

    <main class="d-flex align-items-center justify-content-center flex-grow-1">
      <div class="card bg-secondary p-4 shadow-lg" style="width: 350px">
        <div class="card-body text-center">

          <img
            src="/public/img/logo.png"
            alt="logo"
            width="64"
            height="64"
            class="mb-3"
          />

          <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2">
              <?= htmlspecialchars($error) ?>
            </div>
          <?php endif; ?>

          <form method="POST" action="/public/login">
            <div class="mb-3">
              <label for="usuario" class="form-label d-block text-start">Usuario</label>
              <input
                type="text"
                id="usuario"
                name="usuario"
                placeholder="Ej: Mauricio65"
                class="form-control text-bg-dark border-secondary"
                required
              />
            </div>

            <div class="mb-4">
              <label for="contrasena" class="form-label d-block text-start">Contraseña</label>
              <div class="input-group">
                <input
                  type="password"
                  id="contrasenia"
                  name="contrasenia"
                  class="form-control text-bg-dark border-secondary"
                  required
                />
                <button class="btn btn-outline-light" type="button" id="togglePass">
                  OJO
                </button>
              </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">Entrar</button>
          </form>
        </div>
      </div>
    </main>

    <footer class="text-center py-3 border-top border-secondary mt-auto">
      <p class="m-0 text-white-50">© 2025 - Información del servidor</p>
    </footer>
    <script src="/public/JS/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  </body>
</html>
