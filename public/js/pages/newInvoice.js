var App = window.App || (window.App = {});

App.NewInvoice = class NewInvoice {
  constructor() {
    this.form = { serie: 'F001', fecha_emision: App.todayISO(), tipo_moneda: 'PEN', forma_pago: 'Contado', observacion: '' };
    this.cliente = null;
    this.items = [];
    this.cuotas = [];
    this.sending = false;
    this.pdfFormat = 'ticket-80';
    this.container = null;
  }

  render(container) {
    this.container = container;
    this._renderHTML();
    this._bind();
  }

  _renderHTML() {
    var f = this.form;
    this.container.querySelector('#f-serie').value = f.serie;
    this.container.querySelector('#f-fecha').value = f.fecha_emision;
    this.container.querySelector('#f-moneda').value = f.tipo_moneda;
    this.container.querySelector('#f-pago').value = f.forma_pago;
    this.container.querySelector('#f-obs').value = f.observacion;
    if (f.forma_pago === 'Credito') {
      this.container.querySelector('#f-cuotas-section').style.display = 'block';
    } else {
      this.container.querySelector('#f-cuotas-section').style.display = 'none';
    }
    var clientRoot = this.container.querySelector('#f-client-selector');
    clientRoot.innerHTML = App.clientSelectorHTML(this.cliente, 'Seleccionar cliente (RUC)...');
    var itemsRoot = this.container.querySelector('#f-items-table');
    itemsRoot.innerHTML = App.itemsTableHTML(this.items, this.form.tipo_moneda);
    var self = this;
    App.bindItemsTable(itemsRoot, function () { return self.items; }, function (n) { self.items = n; }, this.form.tipo_moneda);
    var pdfRoot = this.container.querySelector('#f-pdf-format');
    pdfRoot.innerHTML = App.pdfFormatPickerHTML(this.pdfFormat);
    App.refreshIcons();
  }

  _bind() {
    var self = this;
    this.container.querySelector('#f-serie').addEventListener('input', function (e) { self.form.serie = e.target.value.toUpperCase(); e.target.value = self.form.serie; });
    this.container.querySelector('#f-fecha').addEventListener('input', function (e) { self.form.fecha_emision = e.target.value; });
    this.container.querySelector('#f-moneda').addEventListener('change', function (e) { self.form.tipo_moneda = e.target.value; self._refreshItemsTable(); });
    this.container.querySelector('#f-pago').addEventListener('change', function (e) {
      self.form.forma_pago = e.target.value;
      var section = self.container.querySelector('#f-cuotas-section');
      if (section) section.style.display = e.target.value === 'Credito' ? 'block' : 'none';
    });
    var addCuotaBtn = this.container.querySelector('#f-add-cuota');
    if (addCuotaBtn) addCuotaBtn.addEventListener('click', function () { self._addCuota(); });
    self._bindCuotasRows();
    this.container.querySelector('#f-obs').addEventListener('input', function (e) { self.form.observacion = e.target.value; });
    this.container.querySelector('#f-add-prod').addEventListener('click', function () { self._openProductPicker(); });
    App.bindClientSelector(this.container.querySelector('#f-client-selector'), {
      onOpenPicker: function () { self._openClientPicker(); },
      onClear: function () { self.cliente = null; self._renderHTML(); },
    });
    this.container.querySelector('#f-form').addEventListener('submit', function (e) { e.preventDefault(); self._submit(); });
    App.bindPdfFormatPicker(this.container.querySelector('#f-pdf-format'), function () { return self.pdfFormat; }, function (v) { self.pdfFormat = v; });
  }

  _cuotasRowsHTML() {
    if (this.cuotas.length === 0) return '<p style="font-size: 0.875rem; color: rgb(100 116 139);">Agrega al menos una cuota.</p>';
    return this.cuotas.map(function (c, i) {
      return '<div style="display: flex; gap: 0.5rem; align-items: flex-end; margin-bottom: 0.5rem;">'
        + '<div style="flex: 1;"><label class="label">Fecha pago</label>'
          + '<input type="date" data-ci="' + i + '" data-cf="fecha_pago" class="input" value="' + App.escapeHtml(c.fecha_pago) + '" /></div>'
        + '<div style="flex: 1;"><label class="label">Monto</label>'
          + '<input type="number" data-ci="' + i + '" data-cf="monto" class="input" value="' + App.escapeHtml(String(c.monto)) + '" placeholder="0.00" min="0.01" step="0.01" /></div>'
        + '<button type="button" data-cd="' + i + '" style="padding: 0.375rem; color: rgb(239 68 68); background: transparent; border: none; cursor: pointer; flex-shrink: 0; margin-bottom: 2px;">'
          + '<i data-lucide="x" class="w-4 h-4"></i></button>'
      + '</div>';
    }).join('');
  }

  _refreshCuotasRows() {
    var rows = this.container.querySelector('#f-cuotas-rows');
    if (rows) { rows.innerHTML = this._cuotasRowsHTML(); this._bindCuotasRows(); App.refreshIcons(); }
  }

  _bindCuotasRows() {
    var self = this;
    var rows = this.container.querySelector('#f-cuotas-rows');
    if (!rows) return;
    rows.querySelectorAll('[data-ci]').forEach(function (el) {
      el.addEventListener('input', function () {
        var idx = parseInt(el.dataset.ci);
        var field = el.dataset.cf;
        self.cuotas[idx][field] = el.value;
      });
    });
    rows.querySelectorAll('[data-cd]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var remaining = self.cuotas.length - 1;
        self.cuotas = remaining > 0 ? self._calcCuotas(remaining) : [];
        self._refreshCuotasRows();
      });
    });
  }

  _calcCuotas(count) {
    var total = this.items.reduce(function (s, it) { return s + parseFloat(it.cantidad || 0) * parseFloat(it.precio_unitario || 0); }, 0);
    var base = total > 0 ? parseFloat((total / count).toFixed(2)) : 0;
    var today = new Date();
    var result = [];
    for (var i = 0; i < count; i++) {
      var d = new Date(today);
      d.setMonth(d.getMonth() + i + 1);
      var monto = total > 0 ? (i === count - 1 ? parseFloat((total - base * (count - 1)).toFixed(2)) : base) : '';
      result.push({ fecha_pago: d.toISOString().split('T')[0], monto: monto === '' ? '' : String(monto) });
    }
    return result;
  }

  _addCuota() { this.cuotas = this._calcCuotas(this.cuotas.length + 1); this._refreshCuotasRows(); }

  _refreshItemsTable() {
    var self = this;
    var itemsRoot = this.container.querySelector('#f-items-table');
    itemsRoot.innerHTML = App.itemsTableHTML(this.items, this.form.tipo_moneda);
    App.bindItemsTable(itemsRoot, function () { return self.items; }, function (n) { self.items = n; }, this.form.tipo_moneda);
    App.refreshIcons();
  }

  _openProductPicker() {
    var self = this;
    new App.ProductPicker({
      onSelect: function (p) {
        var item = { codigo: p.codigo, cod_producto_sunat: p.cod_producto_sunat, descripcion: p.descripcion, unidad: p.unidad, cantidad: 1, precio_unitario: p.precio_unitario, tip_afe_igv: p.tip_afe_igv || '10' };
        if (p.icbper) { item.icbper = p.icbper; item.factor_icbper = p.factor_icbper; }
        self.items.push(item);
        self._refreshItemsTable();
      },
    }).render(document.body);
  }

  _openClientPicker() {
    var self = this;
    new App.ClientPicker({ onSelect: function (c) { self.cliente = c; self._renderHTML(); } }).render(document.body);
  }

  async _submit() {
    if (!this.cliente) { alert('Selecciona un cliente'); return; }
    if (this.items.length === 0) { alert('Agrega al menos un producto'); return; }
    if (this.form.forma_pago === 'Credito' && this.cuotas.length === 0) { alert('Agrega al menos una cuota'); return; }
    var payload = Object.assign({}, this.form,
      this.form.forma_pago === 'Credito' ? { cuotas: this.cuotas.map(function (c) { return { monto: parseFloat(c.monto), fecha_pago: c.fecha_pago }; }) } : {},
      { cliente: { tipo_doc: this.cliente.tipo_doc, num_doc: this.cliente.num_doc, razon_social: this.cliente.razon_social, direccion: this.cliente.direccion || '', email: this.cliente.email }, items: this.items.map(function (it) { return Object.assign({}, it, { cantidad: parseFloat(it.cantidad), precio_unitario: parseFloat(it.precio_unitario) }); }) });
    this.sending = true;
    var btn = this.container.querySelector('#f-submit');
    btn.disabled = true; btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 icon-spin"></i> Emitiendo...'; App.refreshIcons();
    try {
      var res = await App.api.crearFactura(payload);
      new App.ResponseModal({ response: res, error: null, tipo: 'facturas', pdfFormat: this.pdfFormat }).render(document.body);
      this.cliente = null; this.items = []; this.form.observacion = '';
    } catch (e) {
      new App.ResponseModal({ response: null, error: e, tipo: 'facturas', pdfFormat: this.pdfFormat }).render(document.body);
    } finally {
      this.sending = false;
      btn.disabled = false; btn.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i> Emitir Factura';
    }
  }
};
