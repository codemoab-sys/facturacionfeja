var App = window.App || (window.App = {});

(function () {
  var LABELS = {
    'facturas':        { titulo: 'Facturas',         icon: 'file-text',       apiMethod: 'listarFacturas',     hasCdr: true  },
    'boletas':         { titulo: 'Boletas',          icon: 'receipt',         apiMethod: 'listarBoletas',      hasCdr: true  },
    'notas-credito':   { titulo: 'Notas de Cr\u00e9dito', icon: 'trending-down',   apiMethod: 'listarNotasCredito', hasCdr: true  },
    'notas-debito':    { titulo: 'Notas de D\u00e9bito',  icon: 'trending-up',     apiMethod: 'listarNotasDebito',  hasCdr: true  },
    'guias-remision':  { titulo: 'Gu\u00edas de Remisi\u00f3n',icon: 'truck',           apiMethod: 'listarGuias',        hasCdr: false },
  };

  App.DocumentList = class DocumentList {
    constructor(tipo) {
      this.tipo = tipo || 'facturas';
      this.config = LABELS[this.tipo] || LABELS['facturas'];
      this.docs = [];
      this.loading = true;
      this.error = null;
      this.filtro = { estado: '', buscar: '' };
      this.downloading = null;
      this.container = null;
    }

    render(container) {
      this.container = container;
      var titulo = this.container.querySelector('#dl-title-text');
      if (titulo) titulo.textContent = this.config.titulo;
      var icon = this.container.querySelector('#dl-title-icon');
      if (icon) icon.setAttribute('data-lucide', this.config.icon);
      this.container.querySelector('#dl-tipo').value = this.tipo;
      this._bind();
      this._load();
    }

    _bodyHTML() {
      if (this.loading) return '<div style="text-align: center; padding: 2rem 0; color: rgb(148 163 184); display: flex; align-items: center; justify-content: center; gap: 0.5rem;"><i data-lucide="loader-2" class="w-5 h-5 icon-spin"></i> Cargando...</div>';
      if (this.error) return '<div style="padding: 1rem; background: rgb(254 242 242); color: rgb(185 28 28); border-radius: 0.5rem; display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="x-circle" class="w-5 h-5"></i> ' + App.escapeHtml(this.error) + '</div>';
      if (this.docs.length === 0) return '<div style="text-align: center; padding: 2rem 0; color: rgb(148 163 184);">Sin documentos</div>';
      var self = this;
      return '<div class="table-wrap"><table class="table-std">'
        + '<thead><tr><th>N\u00famero</th><th>Fecha</th><th>Cliente</th><th style="text-align: right;">Total</th><th>Estado</th><th>Acciones</th></tr></thead>'
        + '<tbody>' + this.docs.map(function (d) { return self._rowHTML(d); }).join('') + '</tbody>'
        + '</table></div>';
    }

    _rowHTML(d) {
      var total = (d.totales && d.totales.total != null) ? d.totales.total : (d.mto_imp_venta != null ? d.mto_imp_venta : (d.monto_total != null ? d.monto_total : 0));
      var estado = (d.sunat && d.sunat.estado) || d.sunat_status || null;
      var clienteNombre = (d.cliente && d.cliente.razon_social) || d.client_razon_social || (d.destinatario && d.destinatario.razon_social) || '-';
      var numero = d.numero_completo || (d.serie + '-' + d.correlativo);
      var self = this;
      function actionBtn(kind, icon, label, color) {
        var key = d.id + '_' + kind;
        var loading = self.downloading === key;
        return '<button type="button" data-download="' + kind + '" data-id="' + d.id + '" ' + (loading ? 'disabled' : '') + ' title="Descargar ' + label + '" style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.5rem; border-radius: 0.375rem; ' + color + ' background: transparent; border: none; cursor: pointer;">'
          + (loading ? '<i data-lucide="loader-2" class="w-[14px] h-[14px] icon-spin"></i>' : '<i data-lucide="' + icon + '" class="w-[14px] h-[14px]"></i>') + ' ' + label + '</button>';
      }
      return '<tr>'
        + '<td class="font-mono font-semibold" style="color: rgb(15 23 42);">' + App.escapeHtml(numero) + '</td>'
        + '<td style="color: rgb(71 85 105);">' + App.escapeHtml((d.fecha_emision || '').slice(0, 10)) + '</td>'
        + '<td style="max-width: 20rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">' + App.escapeHtml(clienteNombre) + '</td>'
        + '<td style="text-align: right; font-weight: 700; color: rgb(15 23 42);">' + App.fmtMoney(total, d.tipo_moneda) + '</td>'
        + '<td>' + App.estadoBadgeHTML(estado) + '</td>'
        + '<td><div style="display: flex; align-items: center; gap: 0.25rem;">'
          + actionBtn('pdf', 'file-text', 'PDF', 'color: rgb(37 99 235);')
          + actionBtn('xml', 'file-code', 'XML', 'color: rgb(71 85 105);')
          + (this.config.hasCdr ? actionBtn('cdr', 'file-archive', 'CDR', 'color: rgb(217 119 6);') : '')
        + '</div></td>'
        + '</tr>';
    }

    _refreshBody() {
      this.container.querySelector('#dl-body').innerHTML = this._bodyHTML();
      App.refreshIcons();
      this._bindDownloads();
    }

    _bind() {
      var self = this;
      this.container.querySelector('#dl-buscar').addEventListener('input', function (e) { self.filtro.buscar = e.target.value; });
      this.container.querySelector('#dl-estado').addEventListener('change', function (e) { self.filtro.estado = e.target.value; });
      this.container.querySelector('#dl-filtrar').addEventListener('click', function () { self._load(); });
      this._bindDownloads();
    }

    _bindDownloads() {
      var self = this;
      this.container.querySelectorAll('[data-download]').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var kind = btn.dataset.download;
          var id = parseInt(btn.dataset.id, 10);
          var doc = self.docs.find(function (d) { return d.id === id; });
          if (doc) self._descargar(doc, kind);
        });
      });
    }

    async _load() {
      this.loading = true; this.error = null; this._refreshBody();
      try {
        var params = new URLSearchParams();
        if (this.filtro.estado) params.append('estado', this.filtro.estado);
        if (this.filtro.buscar) params.append('buscar', this.filtro.buscar);
        var query = params.toString() ? ('?' + params.toString()) : '';
        var res = await App.api[this.config.apiMethod](query);
        this.docs = Array.isArray(res.data) ? res.data : ((res.data && res.data.datos) || []);
      } catch (e) { this.error = e.message; }
      finally { this.loading = false; this._refreshBody(); }
    }

    async _descargar(doc, kind) {
      var key = doc.id + '_' + kind;
      this.downloading = key; this._refreshBody();
      try {
        var numero = doc.numero_completo || (doc.serie + '-' + doc.correlativo);
        var blob, filename;
        var tipoBase = this.tipo;
        if (kind === 'pdf') { blob = await App.api.descargarPdf(tipoBase, doc.id, 'a4'); filename = numero + '.pdf'; }
        else if (kind === 'xml') { blob = await App.api.descargarXml(tipoBase, doc.id); filename = numero + '.xml'; }
        else if (kind === 'cdr') { blob = await App.api.descargarCdr(tipoBase, doc.id); filename = 'R-' + numero + '.zip'; }
        App.descargarBlob(blob, filename);
      } catch (e) { alert('Error al descargar ' + kind.toUpperCase() + ': ' + e.message); }
      finally { this.downloading = null; this._refreshBody(); }
    }
  };
})();
