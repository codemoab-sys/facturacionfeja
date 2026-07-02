var App = window.App || (window.App = {});

App.NewDispatchGuide = class NewDispatchGuide {
  constructor() {
    this.form = { serie: 'T001', fecha_emision: App.todayISO(), observacion: '', cod_traslado: '01', mod_traslado: '02', fecha_traslado: App.tomorrowISO(), peso_total: 10, und_peso_total: 'KGM', num_bultos: 1, partida_ubigeo: '150101', partida_direccion: 'AV. LIMA 123', llegada_ubigeo: '150101', llegada_direccion: '', vehiculo_placa: 'ABC-123', conductor_num_doc: '', conductor_nombre: '' };
    this.destinatario = null;
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
    this.container.querySelector('#f-motivo').value = f.cod_traslado;
    this.container.querySelector('#f-modalidad').value = f.mod_traslado;
    this.container.querySelector('#f-fecha-traslado').value = f.fecha_traslado;
    this.container.querySelector('#f-partida-ubigeo').value = f.partida_ubigeo;
    this.container.querySelector('#f-partida-dir').value = f.partida_direccion;
    this.container.querySelector('#f-llegada-ubigeo').value = f.llegada_ubigeo;
    this.container.querySelector('#f-llegada-dir').value = f.llegada_direccion;
    this.container.querySelector('#f-placa').value = f.vehiculo_placa;
    this.container.querySelector('#f-conductor-doc').value = f.conductor_num_doc;
    this.container.querySelector('#f-conductor-nombre').value = f.conductor_nombre;
    this.container.querySelector('#f-peso').value = f.peso_total;
    this.container.querySelector('#f-peso-und').value = f.und_peso_total;
    this.container.querySelector('#f-bultos').value = f.num_bultos;
    this.container.querySelector('#f-obs').value = f.observacion;
    var clientRoot = this.container.querySelector('#f-client-selector');
    clientRoot.innerHTML = App.clientSelectorHTML(this.destinatario, 'Seleccionar destinatario...');
    var itemsRoot = this.container.querySelector('#f-items-table');
    itemsRoot.innerHTML = App.itemsTableHTML(this.items, 'PEN');
    var self = this;
    App.bindItemsTable(itemsRoot, function () { return self.items; }, function (n) { self.items = n; });
    var pdfRoot = this.container.querySelector('#f-pdf-format');
    pdfRoot.innerHTML = App.pdfFormatPickerHTML(this.pdfFormat);
    App.refreshIcons();
  }

  _bind() {
    var self = this;
    var fieldMap = {
      '#f-serie': 'serie',
      '#f-fecha': 'fecha_emision',
      '#f-obs': 'observacion',
      '#f-partida-ubigeo': 'partida_ubigeo',
      '#f-partida-dir': 'partida_direccion',
      '#f-llegada-ubigeo': 'llegada_ubigeo',
      '#f-llegada-dir': 'llegada_direccion',
      '#f-placa': 'vehiculo_placa',
      '#f-conductor-doc': 'conductor_num_doc',
      '#f-conductor-nombre': 'conductor_nombre',
    };
    Object.keys(fieldMap).forEach(function (sel) {
      var el = self.container.querySelector(sel);
      if (el) el.addEventListener('input', function (e) { self.form[fieldMap[sel]] = e.target.value; });
    });
    this.container.querySelector('#f-motivo').addEventListener('change', function (e) { self.form.cod_traslado = e.target.value; });
    this.container.querySelector('#f-modalidad').addEventListener('change', function (e) { self.form.mod_traslado = e.target.value; });
    this.container.querySelector('#f-fecha-traslado').addEventListener('input', function (e) { self.form.fecha_traslado = e.target.value; });
    this.container.querySelector('#f-peso').addEventListener('input', function (e) { self.form.peso_total = parseFloat(e.target.value) || 0; });
    this.container.querySelector('#f-peso-und').addEventListener('change', function (e) { self.form.und_peso_total = e.target.value; });
    this.container.querySelector('#f-bultos').addEventListener('input', function (e) { self.form.num_bultos = parseInt(e.target.value) || 1; });
    this.container.querySelector('#f-add-prod').addEventListener('click', function () { self._openProductPicker(); });
    App.bindClientSelector(this.container.querySelector('#f-client-selector'), {
      onOpenPicker: function () { self._openClientPicker(); },
      onClear: function () { self.destinatario = null; self._renderHTML(); },
    });
    this.container.querySelector('#f-form').addEventListener('submit', function (e) { e.preventDefault(); self._submit(); });
    App.bindPdfFormatPicker(this.container.querySelector('#f-pdf-format'), function () { return self.pdfFormat; }, function (v) { self.pdfFormat = v; });
  }

  _refreshItemsTable() {
    var self = this;
    var itemsRoot = this.container.querySelector('#f-items-table');
    itemsRoot.innerHTML = App.itemsTableHTML(this.items, 'PEN');
    App.bindItemsTable(itemsRoot, function () { return self.items; }, function (n) { self.items = n; });
    App.refreshIcons();
  }

  _openProductPicker() { var self = this; new App.ProductPicker({ onSelect: function (p) { var item = { codigo: p.codigo, cod_producto_sunat: p.cod_producto_sunat, descripcion: p.descripcion, unidad: p.unidad, cantidad: 1, precio_unitario: p.precio_unitario, tip_afe_igv: p.tip_afe_igv || '10' }; if (p.icbper) { item.icbper = p.icbper; item.factor_icbper = p.factor_icbper; } self.items.push(item); self._refreshItemsTable(); } }).render(document.body); }

  _openClientPicker() { var self = this; new App.ClientPicker({ onSelect: function (c) { self.destinatario = c; self._renderHTML(); } }).render(document.body); }

  async _submit() {
    if (!this.destinatario) { alert('Selecciona un destinatario'); return; }
    if (this.items.length === 0) { alert('Agrega al menos un producto'); return; }
    var payload = { serie: this.form.serie, fecha_emision: this.form.fecha_emision, cod_traslado: this.form.cod_traslado, mod_traslado: this.form.mod_traslado, fecha_traslado: this.form.fecha_traslado, peso_total: this.form.peso_total, und_peso_total: this.form.und_peso_total, num_bultos: this.form.num_bultos, partida_ubigeo: this.form.partida_ubigeo, partida_direccion: this.form.partida_direccion, llegada_ubigeo: this.form.llegada_ubigeo, llegada_direccion: this.form.llegada_direccion, vehiculo_placa: this.form.vehiculo_placa, conductor_num_doc: this.form.conductor_num_doc, conductor_nombre: this.form.conductor_nombre, observacion: this.form.observacion, destinatario: { tipo_doc: this.destinatario.tipo_doc, num_doc: this.destinatario.num_doc, razon_social: this.destinatario.razon_social, direccion: this.destinatario.direccion || '', email: this.destinatario.email }, items: this.items.map(function (it) { return Object.assign({}, it, { cantidad: parseFloat(it.cantidad), precio_unitario: parseFloat(it.precio_unitario) }); }) };
    var btn = this.container.querySelector('#f-submit');
    btn.disabled = true; btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 icon-spin"></i> Emitiendo...'; App.refreshIcons();
    try { var res = await App.api.crearGuia(payload); new App.ResponseModal({ response: res, error: null, tipo: 'guias-remision', pdfFormat: this.pdfFormat }).render(document.body); this.items = []; this.form.observacion = ''; this.destinatario = null; }
    catch (e) { new App.ResponseModal({ response: null, error: e, tipo: 'guias-remision' }).render(document.body); }
    finally { btn.disabled = false; btn.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i> Emitir Gu\u00eda'; }
  }
};
