<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <link rel="icon" type="image/x-icon" href="public/img/isotipo.ico" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#2563eb" />
  <title>SUNAT</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'system-ui', '-apple-system', 'Segoe UI', 'sans-serif'],
            mono: ['JetBrains Mono', 'Cascadia Code', 'ui-monospace', 'Consolas', 'monospace'],
          },
        },
      },
    };
  </script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
  <base href="<?= BASE_PATH ?>/" />
  <script>var BASE_PATH = '<?= BASE_PATH ?>';</script>
  <link rel="stylesheet" href="public/css/styles.css" />
</head>
<body>
  <div id="root">
    <div class="min-h-screen flex" style="background: rgb(248 250 252);">
      <div id="app-overlay" class="fixed inset-0 z-30 hidden lg:hidden" style="background: rgb(15 23 42 / 0.5); backdrop-filter: blur(4px);"></div>
      <aside id="app-sidebar" class="fixed lg:sticky top-0 left-0 z-40 w-72 h-screen bg-white flex flex-col -translate-x-full lg:translate-x-0 transform transition-transform duration-300">
        <div id="sidebar-content" class="flex flex-col h-full"></div>
      </aside>
      <div class="flex-1 flex flex-col min-w-0">
        <header class="lg:hidden sticky top-0 z-20 px-4 py-3 flex items-center justify-between" style="background: rgb(255 255 255 / 0.8); backdrop-filter: blur(6px);">
          <button id="btn-open-sidebar" class="p-2 -ml-2 rounded-lg" style="color: rgb(51 65 85);">
            <i data-lucide="menu" class="w-5 h-5"></i>
          </button>
          <div class="flex items-center gap-2">
            <img src="public/img/logo.png" alt="SUNAT" style="height: 1.5rem;" />
          </div>
          <div class="w-9"></div>
        </header>
        <main class="flex-1 p-4 md:p-6 lg:p-8 overflow-auto">
          <div class="max-w-6xl mx-auto">

<?php
$apiConfig = \App\Core\Session::get('api_config', []);
$hasApiConfig = !empty($apiConfig['base_url']) && !empty($apiConfig['api_key']);
if (!$hasApiConfig):
?>
<div id="config-alert" style="margin-bottom: 1rem; padding: 1rem 1.25rem; border-radius: 1rem; background: rgb(254 243 199); border: 1px solid rgb(251 191 36); display: flex; align-items: center; gap: 0.75rem; font-size: 0.875rem; color: rgb(146 64 14);">
  <i data-lucide="alert-triangle" class="w-5 h-5 flex-shrink-0"></i>
  <div style="flex: 1;">
    <strong style="font-weight: 700;">Configuración requerida</strong>
    &mdash; Antes de emitir documentos, configura la conexión API en
    <a href="<?= BASE_PATH ?>/configuracion" style="text-decoration: underline; font-weight: 600;">Configuración</a>.
  </div>
  <button onclick="this.parentElement.remove()" style="padding: 0.25rem; background: none; border: none; cursor: pointer; color: rgb(146 64 14); flex-shrink: 0;">
    <i data-lucide="x" class="w-4 h-4"></i>
  </button>
</div>
<?php endif; ?>

            <div id="page-container"><?php echo $content ?? ''; ?></div>
          </div>
        </main>
      </div>
    </div>
  </div>

  <script id="app-session-data" type="application/json">{"nombre":"<?php echo htmlspecialchars($sessionUser ?? 'Usuario'); ?>","usuario":"<?php echo htmlspecialchars($sessionLogin ?? ''); ?>"}</script>
  <script src="public/js/api.js"></script>
  <script src="public/js/utils.js"></script>
  <script src="public/js/components/sidebar.js"></script>
  <script src="public/js/components/clientSelector.js"></script>
  <script src="public/js/components/itemsTable.js"></script>
  <script src="public/js/components/productPicker.js"></script>
  <script src="public/js/components/clientPicker.js"></script>
  <script src="public/js/components/responseModal.js"></script>
  <script src="public/js/pages/login.js"></script>
  <script src="public/js/pages/settings.js"></script>
  <script src="public/js/pages/dashboard.js"></script>
  <script src="public/js/pages/newInvoice.js"></script>
  <script src="public/js/pages/newBoleta.js"></script>
  <script src="public/js/pages/newCreditNote.js"></script>
  <script src="public/js/pages/newDebitNote.js"></script>
  <script src="public/js/pages/newDispatchGuide.js"></script>
  <script src="public/js/pages/documentList.js"></script>
  <script src="public/js/pages/summaries.js"></script>
  <script src="public/js/pages/app.js"></script>
</body>
</html>
