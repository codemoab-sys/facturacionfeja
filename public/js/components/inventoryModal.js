var App = window.App || (window.App = {});

App.InventoryModal = class InventoryModal {
  constructor(opts) {
    this.producto = opts.producto || null;
    this.tipo = opts.tipo || 'entrada';
    this.onSave = opts.onSave || function () {};
    this.root = null;
  }

  _html() {
    var p = this.producto;
    var t = this.tipo;
    var titulo = t === 'entrada' ? 'Entrada de Inventario'
      : t === 'salida' ? 'Salida de Inventario'
      : 'Ajuste de Inventario';
    var color = t === 'entrada' ? '#059669'
      : t === 'salida' ? '#dc2626'
      : '#d97706';

    return ''
      + '<div class="modal-overlay" id="inv-modal-overlay">'
        + '<div class="modal-content" style="max-width: 480px;">'
          + '<div class="modal-header" style="border-left: 4px solid ' + color + ';">'
            + '<h3 style="margin:0;font-size:1rem;font-weight:700;">' + App.escapeHtml(titulo) + '</h3>'
            + '<button id="inv-modal-close" class="modal-close">&times;</button>'
          + '</div>'
          + '<div class="modal-body">'
            + '<div style="margin-bottom:1rem;padding:0.75rem;background:#f8fafc;border-radius:0.5rem;">'
              + '<div style="font-size:0.75rem;color:#64748b;">Producto</div>'
              + '<div style="font-weight:700;font-size:0.9rem;">' + App.escapeHtml(p ? (p.codigo + ' - ' + p.descripcion) : '') + '</div>'
              + '<div style="font-size:0.75rem;color:#64748b;margin-top:0.25rem;">Stock actual: <strong>' + App.fmtNumber(p ? p.stock : 0) + '</strong></div>'
            + '</div>'
            + '<div class="form-group">'
              + '<label class="form-label">Cantidad</label>'
              + '<input type="number" id="inv-cantidad" class="input" step="0.01" min="0.01" value="1" />'
            + '</div>'
            + '<div class="form-group">'
              + '<label class="form-label">Motivo <span style="color:#94a3b8;">(opcional)</span></label>'
              + '<input type="text" id="inv-motivo" class="input" placeholder="Ej: Ajuste por conteo f\u00edsico" />'
            + '</div>'
          + '</div>'
          + '<div class="modal-footer">'
            + '<button id="inv-modal-cancel" class="btn btn-secondary">Cancelar</button>'
            + '<button id="inv-modal-save" class="btn" style="background:' + color + ';color:white;">'
              + 'Registrar ' + (t === 'entrada' ? 'Entrada' : t === 'salida' ? 'Salida' : 'Ajuste')
            + '</button>'
          + '</div>'
        + '</div>'
      + '</div>';
  }

  render(root) {
    this.root = root;
    root.innerHTML = this._html();
    this._bind();
  }

  _bind() {
    var self = this;
    var overlay = document.getElementById('inv-modal-overlay');
    var close = document.getElementById('inv-modal-close');
    var cancel = document.getElementById('inv-modal-cancel');
    var save = document.getElementById('inv-modal-save');

    function closeModal() {
      if (overlay) overlay.remove();
      if (self.root) self.root.innerHTML = '';
    }

    if (close) close.addEventListener('click', closeModal);
    if (cancel) cancel.addEventListener('click', closeModal);
    if (overlay) overlay.addEventListener('click', function (e) { if (e.target === overlay) closeModal(); });

    if (save) save.addEventListener('click', function () {
      var cantidad = parseFloat(document.getElementById('inv-cantidad').value);
      var motivo = document.getElementById('inv-motivo').value;

      if (!cantidad || cantidad <= 0) {
        App.showToast('Ingresa una cantidad v\u00e1lida', 'error');
        return;
      }

      self.onSave({
        producto_id: self.producto.id,
        tipo: self.tipo,
        cantidad: cantidad,
        motivo: motivo,
      });
      closeModal();
    });
  }
};
