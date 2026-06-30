var App = window.App || (window.App = {});

App.NewDebitNote = class NewDebitNote {
  constructor() {
    this.form = { serie: 'FD01', fecha_emision: App.todayISO(), tipo_moneda: 'PEN', doc_afectado_tipo: '01', doc_afectado_serie: 'F001', doc_afectado_correlativo: '', cod_motivo: '01', des_motivo: '' };
    this.cliente = null;
    this.items = [];
    this.sending = false;
    this.pdfFormat = 'ticket-80';
    this.container = null;
  }

  render(container) { this.container = container; this._renderHTML(); this._bind(); }

  _renderHTML() {
    var f = this.form;
    this.container.querySelector('#f-serie').value = f.serie;
    this.container.querySelector('#f-fecha').value = f.fecha_emision;
    this.container.querySelector('#f-moneda').value = f.tipo_moneda;
    this.container.querySelector('#f-doc-tipo').value = f.doc_afectado_tipo;
    this.container.querySelector('#f-doc-serie').value = f.doc_afectado_serie;
    this.container.querySelector('#f-doc-correlativo').value = f.doc_afectado_correlativo;
    this.container.querySelector('#f-motivo').value = f.cod_motivo;
    this.container.querySelector('#f-motivo-desc').value = f.des_motivo;
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
    this.container.querySelector('#f-doc-tipo').addEventListener('change', function (e) { self.form.doc_afectado_tipo = e.target.value; });
    this.container.querySelector('#f-doc-serie').addEventListener('input', function (e) { self.form.doc_afectado_serie = e.target.value.toUpperCase(); });
    this.container.querySelector('#f-doc-correlativo').addEventListener('input', function (e) { self.form.doc_afectado_correlativo = e.target.value; });
    this.container.querySelector('#f-motivo').addEventListener('change', function (e) { self.form.cod_motivo = e.target.value; });
    this.container.querySelector('#f-motivo-desc').addEventListener('input', function (e) { self.form.des_motivo = e.target.value; });
    this.container.querySelector('#f-add-prod').addEventListener('click', function () { self._openProductPicker(); });
    App.bindClientSelector(this.container.querySelector('#f-client-selector'), {
      onOpenPicker: function () { self._openClientPicker(); },
      onClear: function () { self.cliente = null; self._renderHTML(); },
    });
    this.container.querySelector('#f-form').addEventListener('submit', function (e) { e.preventDefault(); self._submit(); });
    App.bindPdfFormatPicker(this.container.querySelector('#f-pdf-format'), function () { return self.pdfFormat; }, function (v) { self.pdfFormat = v; });
  }

  _refreshItemsTable() {
    var self = this;
    var itemsRoot = this.container.querySelector('#f-items-table');
    itemsRoot.innerHTML = App.itemsTableHTML(this.items, this.form.tipo_moneda);
    App.bindItemsTable(itemsRoot, function () { return self.items; }, function (n) { self.items = n; }, this.form.tipo_moneda);
    App.refreshIcons();
  }

  _openProductPicker() { var self = this; new App.ProductPicker({ onSelect: function (p) { var item = { codigo: p.codigo, cod_producto_sunat: p.cod_producto_sunat, descripcion: p.descripcion, unidad: p.unidad, cantidad: 1, precio_unitario: p.precio_unitario, tip_afe_igv: p.tip_afe_igv || '10' }; if (p.icbper) { item.icbper = p.icbper; item.factor_icbper = p.factor_icbper; } self.items.push(item); self._refreshItemsTable(); } }).render(document.body); }

  _openClientPicker() { var self = this; new App.ClientPicker({ onSelect: function (c) { self.cliente = c; self._renderHTML(); } }).render(document.body); }

  async _submit() {
    if (!this.cliente) { alert('Selecciona un cliente'); return; }
    if (this.items.length === 0) { alert('Agrega al menos un producto'); return; }
    var payload = Object.assign({}, this.form, { cliente: { tipo_doc: this.cliente.tipo_doc, num_doc: this.cliente.num_doc, razon_social: this.cliente.razon_social, direccion: this.cliente.direccion || '', email: this.cliente.email }, items: this.items.map(function (it) { return Object.assign({}, it, { cantidad: parseFloat(it.cantidad), precio_unitario: parseFloat(it.precio_unitario) }); }) });
    var btn = this.container.querySelector('#f-submit');
    btn.disabled = true; btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 icon-spin"></i> Emitiendo...'; App.refreshIcons();
    try { var res = await App.api.crearNotaDebito(payload); new App.ResponseModal({ response: res, error: null, tipo: 'notas-debito', pdfFormat: this.pdfFormat }).render(document.body); this.cliente = null; this.items = []; }
    catch (e) { new App.ResponseModal({ response: null, error: e, tipo: 'notas-debito' }).render(document.body); }
    finally { btn.disabled = false; btn.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i> Emitir Nota de D\u00e9bito'; }
  }
};
