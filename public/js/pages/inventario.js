var App = window.App || (window.App = {});

App.Inventario = class Inventario {
  constructor() {
    this.productos = [];
    this.movimientos = [];
    this.filtros = { tipo: '', desde: '', hasta: '' };
    this.searchTerm = '';
    this.root = null;
  }

  async render(root) {
    this.root = root;
    root.innerHTML = '<div style="text-align:center;padding:2rem;color:#94a3b8;">Cargando inventario...</div>';
    await this._load();
    this._renderHTML();
    this._bind();
    App.refreshIcons();
  }

  async _load(search) {
    if (search !== undefined) this.searchTerm = search;
    try {
      var res = await App.api.inventarioProductos(this.searchTerm);
      this.productos = res.data || [];
    } catch (e) { this.productos = []; }

    try {
      var res2 = await App.api.inventarioMovimientos(this.filtros);
      this.movimientos = res2.data || [];
    } catch (e) { this.movimientos = []; }
  }

  _renderHTML() {
    var totalValor = 0;
    var prodHtml = this.productos.map(function (p) {
      var stock = parseFloat(p.stock) || 0;
      var min = parseFloat(p.stock_minimo) || 0;
      var valor = stock * (parseFloat(p.precio_compra) || 0);
      totalValor += valor;
      var alerta = min > 0 && stock <= min;
      return ''
        + '<tr class="' + (alerta ? 'bg-red-50' : 'hover') + '">'
          + '<td class="td">' + App.escapeHtml(p.codigo) + '</td>'
          + '<td class="td">' + App.escapeHtml(p.descripcion) + '</td>'
          + '<td class="td">' + App.escapeHtml(p.categoria || '-') + '</td>'
          + '<td class="td" style="text-align:right;font-weight:700;' + (alerta ? 'color:#dc2626;' : '') + '">' + App.fmtNumber(stock) + '</td>'
          + '<td class="td" style="text-align:right;">' + App.fmtNumber(min) + '</td>'
          + '<td class="td" style="text-align:right;">' + App.fmtMoney(valor) + '</td>'
          + '<td class="td" style="text-align:right;white-space:nowrap;">'
            + '<button class="btn-inv-mov btn-sm" data-id="' + p.id + '" data-tipo="entrada" title="Entrada"><i data-lucide="plus-circle" class="w-4 h-4" style="color:#059669;"></i></button>'
            + '<button class="btn-inv-mov btn-sm" data-id="' + p.id + '" data-tipo="salida" title="Salida"><i data-lucide="minus-circle" class="w-4 h-4" style="color:#dc2626;"></i></button>'
            + '<button class="btn-inv-mov btn-sm" data-id="' + p.id + '" data-tipo="ajuste" title="Ajuste"><i data-lucide="refresh-cw" class="w-4 h-4" style="color:#d97706;"></i></button>'
          + '</td>'
        + '</tr>';
    }).join('');

    var movHtml = this.movimientos.map(function (m) {
      var color = m.tipo === 'entrada' ? '#059669' : m.tipo === 'salida' ? '#dc2626' : '#d97706';
      return ''
        + '<tr>'
          + '<td class="td" style="font-size:0.75rem;">' + App.escapeHtml(m.created_at) + '</td>'
          + '<td class="td">' + App.escapeHtml(m.codigo) + '</td>'
          + '<td class="td">' + App.escapeHtml(m.producto) + '</td>'
          + '<td class="td"><span style="display:inline-block;padding:0.125rem 0.5rem;border-radius:999px;font-size:0.7rem;font-weight:600;background:' + color + ';color:white;">' + App.escapeHtml(m.tipo) + '</span></td>'
          + '<td class="td" style="text-align:right;">' + App.fmtNumber(m.cantidad) + '</td>'
          + '<td class="td" style="text-align:right;">' + App.fmtNumber(m.stock_anterior) + ' &rarr; ' + App.fmtNumber(m.stock_nuevo) + '</td>'
          + '<td class="td" style="font-size:0.75rem;color:#64748b;">' + App.escapeHtml(m.motivo || '-') + '</td>'
        + '</tr>';
    }).join('') || '<tr><td colspan="7" style="text-align:center;padding:2rem;color:#94a3b8;">Sin movimientos</td></tr>';

    var stockBajo = this.productos.filter(function (p) {
      var min = parseFloat(p.stock_minimo) || 0;
      return min > 0 && (parseFloat(p.stock) || 0) <= min;
    });

    this.root.innerHTML = ''
      + '<div style="padding:1.5rem;">'
        + '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">'
          + '<h1 style="font-size:1.25rem;font-weight:800;">Inventario</h1>'
          + '<div style="display:flex;gap:0.5rem;align-items:center;">'
            + '<input type="text" id="inv-search" class="input" placeholder="Buscar producto..." style="width:200px;" />'
          + '</div>'
        + '</div>'

        + (stockBajo.length > 0 ? ''
          + '<div style="padding:0.75rem 1rem;background:#fef2f2;border:1px solid #fecaca;border-radius:0.5rem;margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem;">'
            + '<i data-lucide="alert-triangle" class="w-5 h-5" style="color:#dc2626;"></i>'
            + '<span style="font-size:0.85rem;color:#991b1b;"><strong>' + stockBajo.length + '</strong> producto(s) con stock bajo</span>'
          + '</div>' : '')

        + '<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">'

          // Productos con stock
          + '<div class="card" style="overflow:hidden;">'
            + '<div class="card-header"><h3>Productos</h3></div>'
            + '<div style="overflow-x:auto;max-height:400px;overflow-y:auto;">'
              + '<table class="table">'
                + '<thead><tr>'
                  + '<th class="th">C\u00f3digo</th>'
                  + '<th class="th">Producto</th>'
                  + '<th class="th">Categor\u00eda</th>'
                  + '<th class="th" style="text-align:right;">Stock</th>'
                  + '<th class="th" style="text-align:right;">M\u00ednimo</th>'
                  + '<th class="th" style="text-align:right;">Valorizado</th>'
                  + '<th class="th" style="text-align:right;">Acci\u00f3n</th>'
                + '</tr></thead>'
                + '<tbody>' + prodHtml + '</tbody>'
              + '</table>'
            + '</div>'
            + '<div style="display:flex;justify-content:space-between;padding:0.75rem 1rem;background:#f8fafc;border-top:1px solid #e2e8f0;font-size:0.85rem;">'
              + '<span><strong>' + this.productos.length + '</strong> productos</span>'
              + '<span>Valor total: <strong>' + App.fmtMoney(totalValor) + '</strong></span>'
            + '</div>'
          + '</div>'

          // Movimientos recientes
          + '<div class="card" style="overflow:hidden;">'
            + '<div class="card-header"><h3>\u00daltimos movimientos</h3></div>'
            + '<div style="overflow-x:auto;max-height:400px;overflow-y:auto;">'
              + '<table class="table">'
                + '<thead><tr>'
                  + '<th class="th">Fecha</th>'
                  + '<th class="th">C\u00f3digo</th>'
                  + '<th class="th">Producto</th>'
                  + '<th class="th">Tipo</th>'
                  + '<th class="th" style="text-align:right;">Cant</th>'
                  + '<th class="th" style="text-align:right;">Stock</th>'
                  + '<th class="th">Motivo</th>'
                + '</tr></thead>'
                + '<tbody>' + movHtml + '</tbody>'
              + '</table>'
            + '</div>'
          + '</div>'

        + '</div>'

        // Contenedor para modales
        + '<div id="inv-modal-container"></div>'
      + '</div>';
  }

  _bind() {
    var self = this;

    // Search
    var search = document.getElementById('inv-search');
    if (search) {
      search.addEventListener('input', function () {
        clearTimeout(self._searchTimer);
        self._searchTimer = setTimeout(function () {
          self._load(search.value).then(function () { self._renderHTML(); self._bind(); App.refreshIcons(); });
        }, 300);
      });
    }

    // Movement buttons
    document.querySelectorAll('.btn-inv-mov').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var id = parseInt(btn.dataset.id, 10);
        var tipo = btn.dataset.tipo;
        var producto = self.productos.find(function (p) { return p.id === id; });
        if (!producto) return;

        var container = document.getElementById('inv-modal-container');
        if (!container) return;

        var modal = new App.InventoryModal({
          producto: producto,
          tipo: tipo,
          onSave: function (data) {
            App.api.inventarioRegistrarMovimiento(data).then(function (res) {
              if (res.success) {
                App.showToast(res.message, 'success');
                self._load().then(function () { self._renderHTML(); self._bind(); App.refreshIcons(); });
              } else {
                App.showToast(res.message || 'Error', 'error');
              }
            }).catch(function (e) {
              App.showToast(e.message || 'Error al registrar', 'error');
            });
          },
        });
        modal.render(container);
      });
    });
  }
};
