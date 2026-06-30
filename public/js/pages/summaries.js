var App = window.App || (window.App = {});

App.Summaries = class Summaries {
  constructor() {
    this.items = [];
    this.loading = true;
    this.error = null;
    this.refreshing = null;
    this.container = null;
    this.showForm = false;
    this.formData = { fecha_resumen: App.todayISO(), documentos: [] };
  }

  async render(container) {
    this.container = container;
    this._load();
  }

  _load() {
    var self = this;
    this.loading = true;
    this._refreshBody();
    App.api.listarResumenes().then(function (res) {
      self.items = Array.isArray(res.data) ? res.data : (res.data && res.data.datos ? res.data.datos : []);
      self.loading = false;
      self._refreshBody();
    }).catch(function (e) {
      self.error = e.message;
      self.loading = false;
      self._refreshBody();
    });
  }

  _bodyHTML() {
    if (this.loading) return '<div style="text-align: center; padding: 2rem 0; color: rgb(148 163 184); display: flex; align-items: center; justify-content: center; gap: 0.5rem;"><i data-lucide="loader-2" class="w-5 h-5 icon-spin"></i> Cargando...</div>';
    if (this.error) return '<div style="padding: 1rem; background: rgb(254 242 242); color: rgb(185 28 28); border-radius: 0.5rem; display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="x-circle" class="w-5 h-5"></i> ' + App.escapeHtml(this.error) + '</div>';
    if (this.items.length === 0) return '<div style="text-align: center; padding: 2rem 0; color: rgb(148 163 184);">Sin res\u00famenes registrados</div>';
    var self = this;
    return '<table class="table-std">'
      + '<thead><tr><th>ID</th><th>Fecha</th><th>Documentos</th><th>Estado</th><th>Acciones</th></tr></thead>'
      + '<tbody>'
      + this.items.map(function (d) {
        var estado = d.estado || d.sunat_status || 'pendiente';
        var key = d.id + '_refresh';
        var loading = self.refreshing === key;
        return '<tr>'
          + '<td class="font-mono">' + App.escapeHtml(String(d.id)) + '</td>'
          + '<td>' + App.escapeHtml((d.fecha_resumen || '').slice(0, 10)) + '</td>'
          + '<td>' + ((d.cantidad_docs || d.cantidad || 0) + ' docs') + '</td>'
          + '<td>' + App.estadoBadgeHTML(estado) + '</td>'
          + '<td>'
            + (estado === 'pendiente' || estado === 'procesando'
              ? '<button type="button" data-refresh="' + d.id + '" ' + (loading ? 'disabled' : '') + ' class="btn-ghost text-xs" style="padding: 0.25rem 0.5rem;">'
                + (loading ? '<i data-lucide="loader-2" class="w-3 h-3 icon-spin"></i>' : '<i data-lucide="refresh-cw" class="w-3 h-3"></i>') + ' Consultar</button>'
              : '')
          + '</td>'
        + '</tr>';
      }).join('')
      + '</tbody>'
    + '</table>';
  }

  _refreshBody() {
    var wrap = this.container.querySelector('#s-table-wrap');
    if (wrap) { wrap.innerHTML = this._bodyHTML(); App.refreshIcons(); this._bindTable(); }
  }

  _bind() {
    var self = this;
    this.container.querySelector('#s-new').addEventListener('click', function () { self._openNewForm(); });
  }

  _bindTable() {
    var self = this;
    this.container.querySelectorAll('[data-refresh]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var id = parseInt(btn.dataset.refresh, 10);
        var key = id + '_refresh';
        self.refreshing = key;
        self._refreshBody();
        App.api.estadoResumen(id).then(function (res) {
          if (res.success) { self._load(); }
          else { self.refreshing = null; self._refreshBody(); }
        }).catch(function () { self.refreshing = null; self._refreshBody(); });
      });
    });
  }

  _openNewForm() {
    var self = this;
    var docRows = '';
    for (var i = 0; i < 5; i++) {
      docRows += '<div style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem;">'
        + '<select class="input" data-sd-tipo="' + i + '"><option value="03">Boleta</option></select>'
        + '<input class="input font-mono" data-sd-serie="' + i + '" placeholder="Serie" value="B001" maxlength="4" />'
        + '<input class="input font-mono" data-sd-inicio="' + i + '" placeholder="Inicio" value="1" />'
        + '<input class="input font-mono" data-sd-fin="' + i + '" placeholder="Fin" value="1" />'
        + '</div>';
    }
    var html = '<div class="fixed inset-0 flex items-center justify-center z-50 p-4" style="background: rgb(0 0 0 / 0.5);">'
      + '<div class="bg-white rounded-xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden" data-stop="1">'
        + '<div class="p-4 flex items-center justify-between border-b">'
          + '<h2 class="text-lg font-semibold flex items-center gap-2"><i data-lucide="plus" class="w-5 h-5"></i> Nuevo Resumen Diario</h2>'
          + '<button id="sn-close" style="color: rgb(148 163 184);"><i data-lucide="x" class="w-5 h-5"></i></button>'
        + '</div>'
        + '<div class="p-4 overflow-y-auto" style="flex: 1;">'
          + '<div class="mb-4"><label class="label">Fecha del resumen</label><input id="sn-fecha" type="date" class="input" value="' + self.formData.fecha_resumen + '" /></div>'
          + '<div><label class="label">Documentos a incluir</label>' + docRows + '</div>'
        + '</div>'
        + '<div class="p-4 border-t flex justify-end gap-2">'
          + '<button id="sn-cancel" class="btn-secondary">Cancelar</button>'
          + '<button id="sn-submit" class="btn-primary"><i data-lucide="send" class="w-4 h-4"></i> Enviar a SUNAT</button>'
        + '</div>'
      + '</div>'
    + '</div>';
    var div = document.createElement('div');
    div.innerHTML = html;
    document.body.appendChild(div);
    App.refreshIcons();
    div.querySelector('#sn-close').addEventListener('click', function () { div.remove(); });
    div.querySelector('#sn-cancel').addEventListener('click', function () { div.remove(); });
    div.querySelector('[data-stop]').addEventListener('click', function (e) { e.stopPropagation(); });
    div.addEventListener('click', function () { div.remove(); });
    div.querySelector('#sn-submit').addEventListener('click', function () {
      var documentos = [];
      for (var i = 0; i < 5; i++) {
        var tipo = div.querySelector('[data-sd-tipo="' + i + '"]');
        var serie = div.querySelector('[data-sd-serie="' + i + '"]');
        var inicio = div.querySelector('[data-sd-inicio="' + i + '"]');
        var fin = div.querySelector('[data-sd-fin="' + i + '"]');
        if (serie && serie.value) {
          documentos.push({ tipo_doc: tipo ? tipo.value : '03', serie: serie.value, correlativo_inicio: inicio ? inicio.value : '1', correlativo_fin: fin ? fin.value : '1' });
        }
      }
      var payload = { fecha_resumen: div.querySelector('#sn-fecha').value, documentos: documentos };
      var btn = div.querySelector('#sn-submit');
      btn.disabled = true; btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 icon-spin"></i> Enviando...'; App.refreshIcons();
      App.api.crearResumen(payload).then(function (res) {
        div.remove();
        if (res.success) { self._load(); }
        else { alert(res.message || 'Error al crear resumen'); }
      }).catch(function (e) { alert('Error: ' + e.message); btn.disabled = false; btn.innerHTML = '<i data-lucide="send" class="w-4 h-4"></i> Enviar a SUNAT'; });
    });
  }
};
