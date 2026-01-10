<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
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
  <link
    rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=light_mode"
  />
  <title>Lista de servidores</title>
</head>

<body class="bg-dark text-white">
  <header class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-secondary p-3">
    <div class="container-fluid">
      <div id="head-logo" class="d-flex align-items-center">
        <h1 class="h3 mb-0 text-white">EuroTech</h1>
      </div>

      <div class="d-flex align-items-center">
        <h2 class="h6 mb-0 me-4 text-white-50">
          Bienvenido, <?= htmlspecialchars($_SESSION['usuario'] ?? '', ENT_QUOTES, 'UTF-8') ?>
        </h2>

        <ul class="navbar-nav flex-row">
          <li class="nav-item me-2">
            <a href="#" class="btn"><span class="material-symbols-outlined">light_mode</span></a>
          </li>
          <li class="nav-item me-2">
            <a href="/public/list_usuarios" class="btn btn-success">Editar usuarios</a>
          </li>
          <li class="nav-item me-2">
            <a class="btn btn-success" href="/public/add_servidor">Agregar servidor</a>
          </li>
          <li class="nav-item">
            <a class="btn btn-danger" href="/public/logout">Salir</a>
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
            <th class="text-secondary acciones-col" style="width: 14%">Acciones</th>
          </tr>
        </thead>

        <tbody id="servers-tbody">
          <?php if (empty($servidores)): ?>
            <tr>
              <td colspan="6" class="text-center text-white-50 py-4">
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

                $idServidor = (int)($s['id'] ?? 0);
                $aliasServidor = (string)($s['alias'] ?? '');
                $rolUsuario = strtoupper(trim((string)($s['rol_usuario'] ?? '')));
              ?>

              <tr>
                <td class="align-middle">
                  <span class="badge rounded-circle <?= $dot ?> me-2 p-2"></span>
                  <span class="fw-semibold"><?= htmlspecialchars($aliasServidor, ENT_QUOTES, 'UTF-8') ?></span>
                  <div class="small text-white-50">ID: <?= $idServidor ?></div>
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

                <td class="acciones-col text-nowrap">
                  <?php if ($rolUsuario === 'ADMIN'): ?>
                    <button
                      type="button"
                      class="btn btn-sm btn-success btn-usuarios-servidor"
                      data-bs-toggle="modal"
                      data-bs-target="#modalUsuariosServidor"
                      data-servidor-id="<?= $idServidor ?>"
                      data-servidor-alias="<?= htmlspecialchars($aliasServidor, ENT_QUOTES, 'UTF-8') ?>"
                    >
                      Editar usuarios
                    </button>

                    <a class="btn btn-sm btn-danger ms-2" href="/public/servidor/<?= $idServidor ?>/eliminar">
                      Eliminar
                    </a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </section>
  </main>

  <div class="modal fade" id="modalUsuariosServidor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="mus-title">Usuarios del servidor</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" id="mus-servidor-id">

          <div class="mb-3">
            <label class="form-label">Buscar usuario para agregar</label>
            <div class="input-group">
              <input type="text" class="form-control" id="mus-q" placeholder="Escribe un usuario…">
              <button class="btn btn-success" type="button" id="mus-btn-buscar">Buscar</button>
            </div>
            <div class="mt-2" id="mus-resultados"></div>
          </div>

          <hr>

          <h6 class="mb-2">Usuarios asignados</h6>
          <div id="mus-alert"></div>

          <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0">
              <thead class="table-secondary">
                <tr>
                  <th>Usuario</th>
                  <th>Rol</th>
                  <th style="width: 1%"></th>
                </tr>
              </thead>
              <tbody id="mus-tbody">
                <tr><td colspan="3" class="text-white-50">Cargando…</td></tr>
              </tbody>
            </table>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>

      </div>
    </div>
  </div>

  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
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
            <td colspan="6" class="text-center text-white-50 py-4">
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
        const rol = String(s.rol_usuario || '').trim().toUpperCase();
        const id = Number(s.id) || 0;

        const acciones = rol === 'ADMIN'
          ? `<button
               type="button"
               class="btn btn-sm btn-success btn-usuarios-servidor"
               data-bs-toggle="modal"
               data-bs-target="#modalUsuariosServidor"
               data-servidor-id="${id}"
               data-servidor-alias="${alias}"
             >Editar usuarios</button>
             <a class="btn btn-sm btn-danger ms-2" href="/public/servidor/${id}/eliminar">Eliminar</a>`
          : '';

        return `
          <tr>
            <td class="align-middle">
              <span class="badge rounded-circle ${ui.dot} me-2 p-2"></span>
              <span class="fw-semibold">${alias}</span>
              <div class="small text-white-50">ID: ${id}</div>
            </td>

            <td><code class="text-white">${ip}</code></td>

            <td>${dominio}</td>

            <td>
              <span class="badge ${ui.badge} text-dark">
                ${escapeHtml(ui.text)}
              </span>
            </td>

            <td>
              <span class="badge bg-info text-dark">
                ${escapeHtml(String(s.rol_usuario ?? ''))}
              </span>
            </td>

            <td class="acciones-col text-nowrap">
              ${acciones}
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

    const musModal = document.getElementById('modalUsuariosServidor');
    const musTitle = document.getElementById('mus-title');
    const musServidorId = document.getElementById('mus-servidor-id');
    const musTbody = document.getElementById('mus-tbody');
    const musAlert = document.getElementById('mus-alert');

    const musQ = document.getElementById('mus-q');
    const musBtnBuscar = document.getElementById('mus-btn-buscar');
    const musResultados = document.getElementById('mus-resultados');

    function esc(s) {
      return String(s ?? '')
        .replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;')
        .replaceAll('"','&quot;').replaceAll("'",'&#039;');
    }

    function showAlert(type, msg) {
      musAlert.innerHTML = `<div class="alert alert-${type} py-2">${esc(msg)}</div>`;
      setTimeout(() => { musAlert.innerHTML = ''; }, 2500);
    }

    async function cargarUsuariosServidor(idServidor) {
      musTbody.innerHTML = `<tr><td colspan="3" class="text-white-50">Cargando…</td></tr>`;

      const res = await fetch(`/public/api/servidor/${idServidor}/usuarios`, { credentials: 'same-origin' });
      const data = await res.json().catch(() => null);

      if (!res.ok || !data || data.ok !== true) {
        musTbody.innerHTML = `<tr><td colspan="3" class="text-danger">No se pudo cargar.</td></tr>`;
        return;
      }

      if (!data.usuarios || data.usuarios.length === 0) {
        musTbody.innerHTML = `<tr><td colspan="3" class="text-white-50">Sin usuarios asignados.</td></tr>`;
        return;
      }

      musTbody.innerHTML = data.usuarios.map(u => `
        <tr>
          <td>${esc(u.usuario)}</td>
          <td><span class="badge bg-info text-dark">${esc(u.rol ?? '')}</span></td>
          <td class="text-end">
            <button class="btn btn-sm btn-danger mus-del" data-id="${u.id}">Eliminar</button>
          </td>
        </tr>
      `).join('');
    }

    async function buscarUsuarios(q) {
      musResultados.innerHTML = `<div class="text-white-50">Buscando…</div>`;
      const res = await fetch(`/public/api/usuarios/buscar?q=${encodeURIComponent(q)}`, { credentials: 'same-origin' });
      const data = await res.json().catch(() => null);

      if (!res.ok || !data || data.ok !== true) {
        musResultados.innerHTML = `<div class="text-danger">No se pudo buscar.</div>`;
        return;
      }

      if (!data.usuarios || data.usuarios.length === 0) {
        musResultados.innerHTML = `<div class="text-white-50">Sin resultados.</div>`;
        return;
      }

      musResultados.innerHTML = `
        <div class="list-group">
          ${data.usuarios.map(u => `
            <div class="list-group-item d-flex justify-content-between align-items-center">
              <div>${esc(u.usuario)}</div>
              <button class="btn btn-sm btn-success mus-add" data-id="${u.id}">Agregar</button>
            </div>
          `).join('')}
        </div>
      `;
    }

    async function postJson(url, body) {
      const res = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify(body)
      });
      const data = await res.json().catch(() => null);
      return { res, data };
    }

    musModal.addEventListener('show.bs.modal', async (event) => {
      const btn = event.relatedTarget;
      const idServidor = btn?.dataset?.servidorId;
      const alias = btn?.dataset?.servidorAlias || '';

      musServidorId.value = idServidor || '';
      musTitle.textContent = alias ? `Usuarios del servidor • ${alias}` : 'Usuarios del servidor';

      musQ.value = '';
      musResultados.innerHTML = '';
      musAlert.innerHTML = '';

      if (idServidor) await cargarUsuariosServidor(idServidor);
    });

    musBtnBuscar.addEventListener('click', () => {
      const q = musQ.value.trim();
      if (q.length < 2) {
        musResultados.innerHTML = `<div class="text-white-50">Escribe al menos 2 caracteres.</div>`;
        return;
      }
      buscarUsuarios(q);
    });

    musQ.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        musBtnBuscar.click();
      }
    });

    document.addEventListener('click', async (e) => {
      const idServidor = musServidorId.value;
      if (!idServidor) return;

      const delBtn = e.target.closest('.mus-del');
      if (delBtn) {
        const idUsuario = Number(delBtn.dataset.id || 0);
        const { res, data } = await postJson(`/public/api/servidor/${idServidor}/usuarios/eliminar`, { idUsuario });
        if (!res.ok || !data || data.ok !== true) return showAlert('danger', 'No se pudo eliminar.');
        showAlert('success', 'Usuario eliminado.');
        await cargarUsuariosServidor(idServidor);
        return;
      }

      const addBtn = e.target.closest('.mus-add');
      if (addBtn) {
        const idUsuario = Number(addBtn.dataset.id || 0);
        const { res, data } = await postJson(`/public/api/servidor/${idServidor}/usuarios/agregar`, { idUsuario });
        if (!res.ok || !data || data.ok !== true) return showAlert('danger', 'No se pudo agregar.');
        showAlert('success', 'Usuario agregado.');
        await cargarUsuariosServidor(idServidor);
        return;
      }
    });

    refreshServers();
    setInterval(refreshServers, 5000);
  </script>

  <script src="/public/JS/script.js"></script>
</body>
</html>
