var App = window.App || (window.App = {});

(function () {
  var LINKS_TOP = [
    { path: '/', icon: 'layout-dashboard', label: 'Inicio' },
  ];
  var LINKS_EMITIR = [
    { path: '/nueva-factura', icon: 'file-text', label: 'Factura' },
    { path: '/nueva-boleta', icon: 'receipt', label: 'Boleta' },
    { path: '/nueva-nc', icon: 'trending-down', label: 'Nota de Cr\u00e9dito' },
    { path: '/nueva-nd', icon: 'trending-up', label: 'Nota de D\u00e9bito' },
    { path: '/nueva-guia', icon: 'truck', label: 'Gu\u00eda de Remisi\u00f3n' },
    { path: '/resumenes', icon: 'file-stack', label: 'Resumen Diario' },
  ];
  var LINKS_CONSULTAR = [
    { path: '/documentos/facturas', icon: 'clipboard-list', label: 'Facturas' },
    { path: '/documentos/boletas', icon: 'clipboard-list', label: 'Boletas' },
    { path: '/documentos/notas-credito', icon: 'clipboard-list', label: 'Notas Cr\u00e9dito' },
    { path: '/documentos/notas-debito', icon: 'clipboard-list', label: 'Notas D\u00e9bito' },
    { path: '/documentos/guias-remision', icon: 'clipboard-list', label: 'Gu\u00edas' },
  ];

  App.Sidebar = class Sidebar {
    constructor(opts) {
      this.currentPath = opts.currentPath || '/';
      this.onNavigate = opts.onNavigate;
      this.onClose = opts.onClose;
      this.onLogout = opts.onLogout;
      this.session = opts.session || { nombre: 'Usuario', usuario: 'demo' };
      this.root = null;
    }

    _linkHTML(link) {
      var active = this.currentPath === link.path;
      var cls = active ? 'nav-link active' : 'nav-link';
      return '<a class="' + cls + '" data-path="' + link.path + '">' +
        '<i data-lucide="' + link.icon + '" class="w-[18px] h-[18px] flex-shrink-0"></i>' +
        '<span>' + link.label + '</span>' +
      '</a>';
    }

    _html() {
      var self = this;
      var s = this.session;
      return ''
        + '<div class="px-5 py-5 flex items-center justify-between">'
          + '<div>'
            + '<img src="public/img/logo.png" alt="SUNAT" style="height: 2rem;" />'
          + '</div>'
          + '<button id="sidebar-close" class="lg:hidden p-1" style="color: rgb(148 163 184);">'
            + '<i data-lucide="x" class="w-5 h-5"></i>'
          + '</button>'
        + '</div>'
        + '<nav class="flex-1 flex flex-col gap-1 p-4 overflow-y-auto">'
          + LINKS_TOP.map(function (l) { return self._linkHTML(l); }).join('')
          + '<div class="nav-section-label">Emitir</div>'
          + LINKS_EMITIR.map(function (l) { return self._linkHTML(l); }).join('')
          + '<div class="nav-section-label">Consultar</div>'
          + LINKS_CONSULTAR.map(function (l) { return self._linkHTML(l); }).join('')
        + '</nav>'
        + '<div class="p-4" style="display: flex; flex-direction: column; gap: 0.5rem;">'
          + this._linkHTML({ path: '/configuracion', icon: 'settings', label: 'Configuraci\u00f3n' })
          + this._userBoxHTML()
        + '</div>';
    }

    _userBoxHTML() {
      var s = this.session;
      if (!s) return '';
      var initial = (s.usuario || 'de').substring(0, 2).toUpperCase();
      return ''
        + '<div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; background: rgb(248 250 252); border-radius: 0.75rem;">'
          + '<div style="width: 2rem; height: 2rem; background: rgb(37 99 235); color: white; border-radius: 9999px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800; flex-shrink: 0;">'
            + App.escapeHtml(initial)
          + '</div>'
          + '<div style="flex: 1; min-width: 0;">'
            + '<div style="font-size: 0.75rem; font-weight: 700; color: rgb(15 23 42); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">' + App.escapeHtml(s.nombre) + '</div>'
            + '<div style="font-size: 0.625rem; color: rgb(100 116 139); text-transform: uppercase; letter-spacing: 0.05em;">@' + App.escapeHtml(s.usuario) + '</div>'
          + '</div>'
          + '<button id="sidebar-logout" title="Cerrar sesi\u00f3n" style="padding: 0.375rem; color: rgb(148 163 184); background: transparent; border: none; border-radius: 0.375rem; cursor: pointer; flex-shrink: 0;">'
            + '<i data-lucide="log-out" class="w-4 h-4"></i>'
          + '</button>'
        + '</div>';
    }

    render(root) {
      this.root = root;
      root.innerHTML = this._html();
      this._bind();
      App.refreshIcons();
    }

    update(currentPath) {
      this.currentPath = currentPath;
      if (this.root) this.render(this.root);
    }

    _bind() {
      var self = this;
      this.root.querySelectorAll('[data-path]').forEach(function (el) {
        el.addEventListener('click', function (e) {
          e.preventDefault();
          self.onNavigate(el.dataset.path);
        });
      });
      var closeBtn = this.root.querySelector('#sidebar-close');
      if (closeBtn && this.onClose) closeBtn.addEventListener('click', function () { self.onClose(); });
      var logoutBtn = this.root.querySelector('#sidebar-logout');
      if (logoutBtn && this.onLogout) logoutBtn.addEventListener('click', function () { self.onLogout(); });
    }
  };
})();
