<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="/../vistas/styles/style.css" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    />
    <title>Lista de servidores</title>
  </head>

  <body class="bg-dark text-white">
    <header class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-secondary p-3">
      <div class="container-fluid">
        <div id="head-logo" class="d-flex align-items-center">
          <h1 class="h3 mb-0 text-white">Nombre</h1>
        </div>

        <div class="d-flex align-items-center">
          <h2 class="h6 mb-0 me-4 text-white-50">
            Bienvenido, <?= htmlspecialchars($_SESSION['usuario'] ?? '', ENT_QUOTES, 'UTF-8') ?>
          </h2>

          <ul class="navbar-nav flex-row">
            <li class="nav-item me-2">
              <a href="#" class="btn btn-outline-light">modo claro/oscuro</a>
            </li>
            <li class="nav-item me-2">
              <a href="/public/add_usuario" class="btn btn-outline-light">Añadir usuarios</a>
            </li>
            <li class="nav-item">
              <a class="btn btn-primary" href="/public/add_servidor">Añadir server</a>
            </li>
          </ul>
        </div>
      </div>
    </header>

    <main class="container-fluid py-4">
      <section class="mb-4">
        <h1 class="text-white">Lista de servidores</h1>
        <p class="text-white-50">Servidores asociados a tu cuenta</p>

        <?php if (!empty($_SESSION['error_servidor'])): ?>
          <div class="alert alert-danger mt-3">
            <?= htmlspecialchars($_SESSION['error_servidor'], ENT_QUOTES, 'UTF-8') ?>
          </div>
          <?php unset($_SESSION['error_servidor']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['ok_servidor'])): ?>
          <div class="alert alert-success mt-3">
            <?= htmlspecialchars($_SESSION['ok_servidor'], ENT_QUOTES, 'UTF-8') ?>
          </div>
          <?php unset($_SESSION['ok_servidor']); ?>
        <?php endif; ?>
      </section>

      <section id="server-list">
        <table class="table table-dark table-hover align-middle">
          <thead class="table-secondary">
            <tr>
              <th class="text-secondary" style="width: 30%">Servidor</th>
              <th class="text-secondary">IP</th>
              <th class="text-secondary">Dominio</th>
              <th class="text-secondary" style="width: 18%">Estado</th>
              <th class="text-secondary" style="width: 14%">Rol</th>
            </tr>
          </thead>

          <tbody id="servers-tbody">
            <?php if (empty($servidores)): ?>
              <tr>
                <td colspan="5" class="text-center text-white-50 py-4">
                  No tienes servidores asociados en usuarios_servidor.
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($servidores as $s): ?>
                <?php
                  $estado = strtoupper((string)($s['estado'] ?? 'INACTIVO'));

                  switch ($estado) {
                    case 'ENCENDIDO':
                    case 'ACTIVO':
                      $dot = 'bg-success';
                      $badge = 'bg-success';
                      break;
                    case 'APAGADO':
                      $dot = 'bg-danger';
                      $badge = 'bg-danger';
                      break;
                    case 'INDETERMINADO':
                      $dot = 'bg-warning';
                      $badge = 'bg-warning';
                      break;
                    default:
                      $dot = 'bg-secondary';
                      $badge = 'bg-secondary';
                      break;
                  }
                ?>

                <tr>
                  <td class="align-middle">
                    <span class="badge rounded-circle <?= $dot ?> me-2 p-2"></span>
                    <span class="fw-semibold"><?= htmlspecialchars($s['alias'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                    <div class="small text-white-50">ID: <?= (int)($s['id'] ?? 0) ?></div>
                  </td>

                  <td>
                    <code class="text-white"><?= htmlspecialchars($s['ip'] ?? '', ENT_QUOTES, 'UTF-8') ?></code>
                  </td>

                  <td>
                    <?= !empty($s['dominio'])
                      ? htmlspecialchars($s['dominio'], ENT_QUOTES, 'UTF-8')
                      : '<span class="text-white-50">—</span>' ?>
                  </td>

                  <td>
                    <span class="badge <?= $badge ?> text-dark">
                      <?= htmlspecialchars($estado, ENT_QUOTES, 'UTF-8') ?>
                    </span>
                  </td>

                  <td>
                    <span class="badge bg-info text-dark">
                      <?= htmlspecialchars($s['rol_usuario'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </section>
    </main>

    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eOzr1wS5Nn+EnIQJ"
      crossorigin="anonymous"
    ></script>

    <script>
      const tbody = document.getElementById('servers-tbody');

      function estadoUI(estadoRaw) {
        const estado = String(estadoRaw || 'INACTIVO').toUpperCase();
        if (estado === 'ENCENDIDO' || estado === 'ACTIVO') return { dot: 'bg-success', badge: 'bg-success', text: estado };
        if (estado === 'APAGADO') return { dot: 'bg-danger', badge: 'bg-danger', text: estado };
        if (estado === 'INDETERMINADO') return { dot: 'bg-warning', badge: 'bg-warning', text: estado };
        return { dot: 'bg-secondary', badge: 'bg-secondary', text: estado };
      }

      function escapeHtml(s) {
        return String(s ?? '')
          .replaceAll('&','&amp;')
          .replaceAll('<','&lt;')
          .replaceAll('>','&gt;')
          .replaceAll('"','&quot;')
          .replaceAll("'",'&#039;');
      }

      function renderRows(servidores) {
        if (!Array.isArray(servidores) || servidores.length === 0) {
          tbody.innerHTML = `
            <tr>
              <td colspan="5" class="text-center text-white-50 py-4">
                No tienes servidores asociados en usuarios_servidor.
              </td>
            </tr>
          `;
          return;
        }

        tbody.innerHTML = servidores.map(s => {
          const ui = estadoUI(s.estado);
          const dominio = s.dominio ? escapeHtml(s.dominio) : '<span class="text-white-50">—</span>';
          const alias = escapeHtml(s.alias);
          const ip = escapeHtml(s.ip);
          const rol = escapeHtml(s.rol_usuario);
          const id = Number(s.id) || 0;

          return `
            <tr>
              <td class="align-middle">
                <span class="badge rounded-circle ${ui.dot} me-2 p-2"></span>
                <span class="fw-semibold">${alias}</span>
                <div class="small text-white-50">ID: ${id}</div>
              </td>

              <td>
                <code class="text-white">${ip}</code>
              </td>

              <td>
                ${dominio}
              </td>

              <td>
                <span class="badge ${ui.badge} text-dark">
                  ${escapeHtml(ui.text)}
                </span>
              </td>

              <td>
                <span class="badge bg-info text-dark">
                  ${rol}
                </span>
              </td>
            </tr>
          `;
        }).join('');
      }

      let inFlight = false;

      async function refreshServers() {
        if (inFlight) return;
        inFlight = true;
        try {
          const res = await fetch('/public/api/servidores', { credentials: 'same-origin' });
          const data = await res.json().catch(() => null);
          if (!res.ok || !data || data.ok !== true) return;
          renderRows(data.servidores);
        } catch (e) {
        } finally {
          inFlight = false;
        }
      }

      refreshServers();
      setInterval(refreshServers, 5000);
    </script>
  </body>
</html>



