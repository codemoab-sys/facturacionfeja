<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#2563eb" />
  <title>SUNAT — Iniciar sesión</title>
  <link rel="icon" type="image/x-icon" href="public/img/isotipo.ico" />
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
  <base href="<?= BASE_PATH ?>/" />
  <script>var BASE_PATH = '<?= BASE_PATH ?>';</script>
  <link rel="stylesheet" href="<?= asset('css/styles.css') ?>" />
</head>
<body>
  <div id="root"></div>
  <script src="<?= asset('js/api.js') ?>"></script>
  <script src="<?= asset('js/utils.js') ?>"></script>
  <script src="<?= asset('js/pages/login.js') ?>"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      new App.Login().render(document.getElementById('root'));
    });
  </script>
</body>
</html>
