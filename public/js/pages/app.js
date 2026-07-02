var App = window.App || (window.App = {});

(function () {
  var page = null;
  var sidebar = null;
  var sidebarOpen = false;

  function getCurrentPath() {
    var base = typeof BASE_PATH !== 'undefined' ? BASE_PATH : '';
    var path = window.location.pathname.replace(/\/+$/, '') || '/';
    if (base && path.indexOf(base) === 0) {
      path = path.substring(base.length) || '/';
    }
    return path;
  }

  function toggleSidebar(open) {
    sidebarOpen = open;
    var aside = document.getElementById('app-sidebar');
    var overlay = document.getElementById('app-overlay');
    if (aside) {
      aside.classList.toggle('translate-x-0', open);
      aside.classList.toggle('-translate-x-full', !open);
    }
    if (overlay) overlay.classList.toggle('hidden', !open);
  }

  function doLogout() {
    window.location.href = (typeof BASE_PATH !== 'undefined' ? BASE_PATH : '') + '/logout';
  }

  function getSession() {
    try {
      var el = document.getElementById('app-session-data');
      return el ? JSON.parse(el.textContent || '{}') : { nombre: 'Usuario', usuario: 'demo' };
    } catch (e) { return { nombre: 'Usuario', usuario: 'demo' }; }
  }

  function getPageClass(path) {
    var routes = {
      '/':                     'Dashboard',
      '/nueva-factura':        'NewInvoice',
      '/nueva-boleta':         'NewBoleta',
      '/nueva-nc':             'NewCreditNote',
      '/nueva-nd':             'NewDebitNote',
      '/nueva-guia':           'NewDispatchGuide',
      '/configuracion':        'Settings',
      '/resumenes':            'Summaries',
      '/productos':            'Productos',
      '/clientes':             'Clientes',
      '/inventario':           'Inventario',
      '/compras':              'Compras',
    };
    if (routes[path]) return routes[path];
    if (path.startsWith('/documentos/')) return 'DocumentList';
    return null;
  }

  function getPageParam(path) {
    if (path.startsWith('/documentos/')) {
      return path.replace('/documentos/', '');
    }
    return null;
  }

  function initPage() {
    var path = getCurrentPath();
    var className = getPageClass(path);

    if (!className) {
      window.location.href = (typeof BASE_PATH !== 'undefined' ? BASE_PATH : '') + '/';
      return;
    }

    var container = document.getElementById('page-container');
    if (!container) return;

    var session = getSession();

    // Init sidebar
    if (!document.getElementById('sidebar-content')) return;
    sidebar = new App.Sidebar({
      currentPath: path,
      session: session,
      onNavigate: function (p) { window.location.href = (typeof BASE_PATH !== 'undefined' ? BASE_PATH : '') + p; },
      onClose: function () { toggleSidebar(false); },
      onLogout: doLogout,
    });
    sidebar.render(document.getElementById('sidebar-content'));

    var openBtn = document.getElementById('btn-open-sidebar');
    var overlay = document.getElementById('app-overlay');
    if (openBtn) openBtn.addEventListener('click', function () { toggleSidebar(true); });
    if (overlay) overlay.addEventListener('click', function () { toggleSidebar(false); });

    // Init page
    if (className === 'DocumentList') {
      var tipo = getPageParam(path) || 'facturas';
      page = new App.DocumentList(tipo);
    } else {
      page = new App[className]();
    }

    if (page && page.render) {
      page.render(container);
    }
  }

  // Dark mode
  App.toggleDarkMode = function () {
    var html = document.documentElement;
    html.classList.toggle('dark');
    try { localStorage.setItem('darkMode', html.classList.contains('dark') ? '1' : '0'); } catch (e) {}
  };

  function initDarkMode() {
    var isDark = localStorage.getItem('darkMode');
    if (isDark === null && window.matchMedia) {
      isDark = window.matchMedia('(prefers-color-scheme: dark)').matches ? '1' : '0';
    }
    if (isDark === '1') {
      document.documentElement.classList.add('dark');
    }
  }

  initDarkMode();
  document.addEventListener('DOMContentLoaded', initPage);
})();
