var App = window.App || (window.App = {});

App.Compras = class Compras {
  constructor() {
    this.compras = [];
    this.productos = [];
    this.currentView = 'list';
    this.form = this._emptyForm();
    this.root = null;
  }

  _emptyForm() {
    return {
      proveedor: '', numero_documento: '', tipo_documento: 'FACTURA',
      fecha_emision: new Date().toISOString().substring(0, 10),
      observaciones: '', detalles: [],
      subtotal: 0, igv: 0, total: 0,
    };
  }

  async render(root) {
    this.root = root;
    root.innerHTML = '<div style="text-align:center;padding:2rem;color:#94a3b8;">Cargando...</div>';
    await this._load();
    this._renderList();
    this._bind();
    App.refreshIcons();
  }

  async _load() {
    try {
      var res = await App.api.listarCompras();
      this.compras = res.data || [];
    } catch (e) { this.compras = []; }

    try {
      var res2 = await App.api.inventarioProductos();
      this.productos = res2.data || [];
      App.productosLista = this.productos;
    } catch (e) { this.productos = []; }
  }

  _renderList() {
    var rows = this.compras.map(function (c) {
      return ''
        + '<tr class="hover">'
          + '<td class="td" style="font-size:0.75rem;">' + App.escapeHtml(c.created_at || '') + '</td>'
          + '<td class="td">' + App.escapeHtml(c.proveedor || '-') + '</td>'
          + '<td class="td">' + App.escapeHtml(c.numero_documento || '-') + '</td>'
          + '<td class="td" style="text-align:right;">' + App.fmtMoney(c.total) + '</td>'
          + '<td class="td" style="text-align:right;">'
            + '<button class="btn-compra-delete btn-sm" data-id="' + c.id + '" title="Eliminar"><i data-lucide="trash-2" class="w-4 h-4" style="color:#dc2626;"></i></button>'
          + '</td>'
        + '</tr>';
    }).join('') || '<tr><td colspan="5" style="text-align:center;padding:2rem;color:#94a3b8;">Sin compras registradas</td></tr>';

    this.root.innerHTML = ''
      + '<div style="padding:1.5rem;">'
        + '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">'
          + '<h1 style="font-size:1.25rem;font-weight:800;">Compras</h1>'
          + '<button id="btn-nueva-compra" class="btn btn-primary"><i data-lucide="plus" class="w-4 h-4"></i> Nueva Compra</button>'
        + '</div>'
        + '<div class="card" style="overflow:hidden;">'
          + '<div style="overflow-x:auto;">'
            + '<table class="table">'
              + '<thead><tr>'
                + '<th class="th">Fecha</th>'
                + '<th class="th">Proveedor</th>'
                + '<th class="th">N\u00b0 Documento</th>'
                + '<th class="th" style="text-align:right;">Total</th>'
                + '<th class="th" style="text-align:right;"></th>'
              + '</tr></thead>'
              + '<tbody>' + rows + '</tbody>'
            + '</table>'
          + '</div>'
        + '</div>'
        + '<div id="compra-modal-container"></div>'
      + '</div>';
  }

  _renderForm() {
    var f = this.form;

    var detRows = f.detalles.map(function (d, i) {
      var p = App.productosLista ? App.productosLista.find(function (x) { return x.id === d.producto_id; }) : null;
      return ''
        + '<tr>'
          + '<td class="td">' + App.escapeHtml(p ? p.codigo : 'ID:' + d.producto_id) + '</td>'
          + '<td class="td">' + App.escapeHtml(p ? p.descripcion : '') + '</td>'
          + '<td class="td" style="text-align:right;"><input type="number" class="input det-cant" data-idx="' + i + '" value="' + d.cantidad + '" step="0.01" min="0.01" style="width:80px;text-align:right;" /></td>'
          + '<td class="td" style="text-align:right;"><input type="number" class="input det-precio" data-idx="' + i + '" value="' + d.precio_unitario + '" step="0.01" min="0" style="width:100px;text-align:right;" /></td>'
          + '<td class="td" style="text-align:right;">' + App.fmtMoney(d.subtotal) + '</td>'
          + '<td class="td" style="text-align:right;"><button class="btn-det-remove btn-sm" data-idx="' + i + '" style="color:#dc2626;"><i data-lucide="x" class="w-4 h-4"></i></button></td>'
        + '</tr>';
    }).join('');

    this.root.innerHTML = ''
      + '<div style="padding:1.5rem;">'
        + '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">'
          + '<h1 style="font-size:1.25rem;font-weight:800;">Nueva Compra</h1>'
          + '<button id="btn-volver-compras" class="btn btn-secondary">Volver</button>'
        + '</div>'
        + '<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">'
          // Datos de compra
          + '<div class="card">'
            + '<div class="card-header"><h3>Datos de la Compra</h3></div>'
            + '<div style="padding:1rem;display:grid;gap:0.75rem;">'
              + '<div class="form-group"><label class="form-label">Proveedor</label><input type="text" id="f-proveedor" class="input" value="' + App.escapeHtml(f.proveedor) + '" /></div>'
              + '<div class="form-group"><label class="form-label">N\u00b0 Documento</label><input type="text" id="f-numero-doc" class="input" value="' + App.escapeHtml(f.numero_documento) + '" /></div>'
              + '<div class="form-group"><label class="form-label">Tipo Documento</label>'
                + '<select id="f-tipo-doc" class="input"><option value="FACTURA"' + (f.tipo_documento === 'FACTURA' ? ' selected' : '') + '>Factura</option><option value="BOLETA"' + (f.tipo_documento === 'BOLETA' ? ' selected' : '') + '>Boleta</option><option value="TICKET"' + (f.tipo_documento === 'TICKET' ? ' selected' : '') + '>Ticket</option><option value="OTRO"' + (f.tipo_documento === 'OTRO' ? ' selected' : '') + '>Otro</option></select>'
              + '</div>'
              + '<div class="form-group"><label class="form-label">Fecha Emisi\u00f3n</label><input type="date" id="f-fecha" class="input" value="' + App.escapeHtml(f.fecha_emision) + '" /></div>'
              + '<div class="form-group"><label class="form-label">Observaciones</label><textarea id="f-obs" class="input" rows="2">' + App.escapeHtml(f.observaciones) + '</textarea></div>'
            + '</div>'
          + '</div>'
          // Detalle
          + '<div class="card">'
            + '<div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">'
              + '<h3>Detalle</h3>'
              + '<button id="btn-agregar-producto-compra" class="btn btn-sm btn-primary"><i data-lucide="plus" class="w-4 h-4"></i> Agregar</button>'
            + '</div>'
            + '<div style="overflow-x:auto;">'
              + '<table class="table">'
                + '<thead><tr>'
                  + '<th class="th">C\u00f3digo</th>'
                  + '<th class="th">Producto</th>'
                  + '<th class="th" style="text-align:right;">Cant</th>'
                  + '<th class="th" style="text-align:right;">P.Unit</th>'
                  + '<th class="th" style="text-align:right;">Subtotal</th>'
                  + '<th class="th" style="text-align:right;"></th>'
                + '</tr></thead>'
                + '<tbody>' + (detRows || '<tr><td colspan="6" style="text-align:center;padding:1rem;color:#94a3b8;">Agrega productos a la compra</td></tr>') + '</tbody>'
              + '</table>'
            + '</div>'
            + '<div style="padding:0.75rem 1rem;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;justify-content:flex-end;gap:1.5rem;font-size:0.85rem;">'
              + '<span>Subtotal: <strong id="compra-subtotal">' + App.fmtMoney(f.subtotal) + '</strong></span>'
              + '<span>IGV: <strong id="compra-igv">' + App.fmtMoney(f.igv) + '</strong></span>'
              + '<span style="font-weight:800;">Total: <strong id="compra-total">' + App.fmtMoney(f.total) + '</strong></span>'
            + '</div>'
          + '</div>'
        + '</div>'
        + '<div style="margin-top:1rem;display:flex;justify-content:flex-end;gap:0.5rem;">'
          + '<button id="btn-guardar-compra" class="btn btn-primary"><i data-lucide="save" class="w-4 h-4"></i> Guardar Compra</button>'
        + '</div>'
        + '<div id="compra-picker-container"></div>'
      + '</div>';
  }

  _calcular() {
    var sub = this.form.detalles.reduce(function (s, d) { return s + (parseFloat(d.subtotal) || 0); }, 0);
    this.form.subtotal = Math.round(sub * 100) / 100;
    this.form.igv = Math.round(sub * 0.18 * 100) / 100;
    this.form.total = Math.round((sub + this.form.igv) * 100) / 100;
  }

  _bind() {
    var self = this;

    if (this.currentView === 'list') {
      var newBtn = document.getElementById('btn-nueva-compra');
      if (newBtn) newBtn.addEventListener('click', function () {
        self.form = self._emptyForm();
        self.currentView = 'form';
        self._renderForm();
        self._bind();
        App.refreshIcons();
      });

      document.querySelectorAll('.btn-compra-delete').forEach(function (btn) {
        btn.addEventListener('click', function () {
          if (!confirm('\u00bfEliminar esta compra? Se revertir\u00e1 el stock.')) return;
          var id = parseInt(btn.dataset.id, 10);
          App.api.eliminarCompra(id).then(function (res) {
            if (res.success) {
              App.showToast(res.message, 'success');
              self._load().then(function () { self._renderList(); self._bind(); App.refreshIcons(); });
            } else {
              App.showToast(res.message || 'Error', 'error');
            }
          }).catch(function (e) { App.showToast(e.message, 'error'); });
        });
      });
    }

    if (this.currentView === 'form') {
      var volver = document.getElementById('btn-volver-compras');
      if (volver) volver.addEventListener('click', function () {
        self.currentView = 'list';
        self.render(self.root);
      });

      // Add product button
      var addProd = document.getElementById('btn-agregar-producto-compra');
      if (addProd) addProd.addEventListener('click', function () {
        var container = document.getElementById('compra-picker-container');
        if (!container) return;
        var picker = new App.ProductPicker({
          onSelect: function (producto) {
            var exists = self.form.detalles.find(function (d) { return d.producto_id === producto.id; });
            if (exists) {
              App.showToast('Producto ya agregado', 'error');
              return;
            }
            self.form.detalles.push({
              producto_id: producto.id,
              cantidad: 1,
              precio_unitario: parseFloat(producto.precio_compra) || 0,
              subtotal: parseFloat(producto.precio_compra) || 0,
            });
            self._calcular();
            self._renderForm();
            self._bind();
            App.refreshIcons();
          },
        });
        picker.render(container);
      });

      // Detalle quantity/price changes
      document.querySelectorAll('.det-cant, .det-precio').forEach(function (input) {
        input.addEventListener('change', function () {
          var idx = parseInt(input.dataset.idx, 10);
          var d = self.form.detalles[idx];
          if (!d) return;
          var cant = parseFloat(document.querySelector('.det-cant[data-idx="' + idx + '"]').value) || 0;
          var precio = parseFloat(document.querySelector('.det-precio[data-idx="' + idx + '"]').value) || 0;
          d.cantidad = cant;
          d.precio_unitario = precio;
          d.subtotal = Math.round(cant * precio * 100) / 100;
          self._calcular();
          self._renderForm();
          self._bind();
          App.refreshIcons();
        });
      });

      // Remove detail
      document.querySelectorAll('.btn-det-remove').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var idx = parseInt(btn.dataset.idx, 10);
          self.form.detalles.splice(idx, 1);
          self._calcular();
          self._renderForm();
          self._bind();
          App.refreshIcons();
        });
      });

      // Save
      var guardar = document.getElementById('btn-guardar-compra');
      if (guardar) guardar.addEventListener('click', function () {
        self.form.proveedor = (document.getElementById('f-proveedor') || {}).value || '';
        self.form.numero_documento = (document.getElementById('f-numero-doc') || {}).value || '';
        self.form.tipo_documento = (document.getElementById('f-tipo-doc') || {}).value || 'FACTURA';
        self.form.fecha_emision = (document.getElementById('f-fecha') || {}).value || '';
        self.form.observaciones = (document.getElementById('f-obs') || {}).value || '';

        if (!self.form.proveedor) { App.showToast('Ingresa el proveedor', 'error'); return; }
        if (self.form.detalles.length === 0) { App.showToast('Agrega al menos un producto', 'error'); return; }

        App.api.crearCompra(self.form).then(function (res) {
          if (res.success) {
            App.showToast(res.message, 'success');
            self.currentView = 'list';
            self._load().then(function () { self._renderList(); self._bind(); App.refreshIcons(); });
          } else {
            App.showToast(res.message || 'Error', 'error');
          }
        }).catch(function (e) { App.showToast(e.message, 'error'); });
      });
    }
  }

  async refresh(root) {
    this.root = root;
    await this._load();
    if (this.currentView === 'list') {
      this._renderList();
    } else {
      this._renderForm();
    }
    this._bind();
    App.refreshIcons();
  }
};
