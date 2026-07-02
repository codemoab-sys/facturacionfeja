var App = window.App || (window.App = {});

App.Clientes = class Clientes {
  constructor() {
    this.container = null;
    this.editando = false;
  }

  render(container) {
    this.container = container;
    this._listar();
    this._bind();
  }

  async _listar(buscar) {
    var tbody = this.container.querySelector('#cli-tbody');
    tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 2rem; color: rgb(148 163 184);"><i data-lucide="loader-2" class="w-5 h-5 icon-spin"></i> Cargando...</td></tr>';
    App.refreshIcons();

    try {
      var q = buscar || this.container.querySelector('#cli-buscar').value.trim();
      var res = await App.api.listarClientesLocal(q);

      if (!res.success) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 2rem; color: rgb(185 28 28);">Error al cargar</td></tr>';
        return;
      }

      var items = res.data || [];
      if (items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 2rem; color: rgb(148 163 184);"><i data-lucide="users" class="w-5 h-5"></i> Ning\u00fan cliente registrado</td></tr>';
        App.refreshIcons();
        return;
      }

      var html = '';
      for (var i = 0; i < items.length; i++) {
        var c = items[i];
        var tipo = c.tipo_doc === '6' ? 'RUC' : c.tipo_doc === '1' ? 'DNI' : c.tipo_doc;
        html += '<tr>'
          + '<td class="text-xs">' + tipo + '</td>'
          + '<td class="font-mono text-xs">' + App.escapeHtml(c.num_doc) + '</td>'
          + '<td class="font-semibold">' + App.escapeHtml(c.razon_social) + '</td>'
          + '<td class="text-xs" style="color: rgb(71 85 105);">' + App.escapeHtml(c.direccion || '') + '</td>'
          + '<td class="text-xs">' + App.escapeHtml(c.email || '') + '</td>'
          + '<td style="text-align: center; white-space: nowrap;">'
          + '<button class="btn-ghost btn-edit-cli text-xs" data-id="' + c.id + '" title="Editar"><i data-lucide="pencil" class="w-4 h-4"></i></button>'
          + '<button class="btn-ghost btn-del-cli text-xs" style="color: rgb(220 38 38);" data-id="' + c.id + '" title="Eliminar"><i data-lucide="trash-2" class="w-4 h-4"></i></button>'
          + '</td>'
          + '</tr>';
      }
      tbody.innerHTML = html;
      App.refreshIcons();
      this._bindRowButtons();
    } catch (e) {
      tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 2rem; color: rgb(185 28 28);">' + App.escapeHtml(e.message) + '</td></tr>';
      App.refreshIcons();
    }
  }

  _bind() {
    var self = this;

    this.container.querySelector('#btn-crear-cliente').addEventListener('click', function () {
      self._abrirModal();
    });

    this.container.querySelector('#cli-btn-buscar').addEventListener('click', function () {
      self._listar();
    });

    this.container.querySelector('#cli-buscar').addEventListener('keydown', function (e) {
      if (e.key === 'Enter') self._listar();
    });

    this.container.querySelector('#cli-modal-close').addEventListener('click', function () {
      self._cerrarModal();
    });

    this.container.querySelector('#cli-modal-cancel').addEventListener('click', function () {
      self._cerrarModal();
    });

    this.container.querySelector('#cli-modal').addEventListener('click', function (e) {
      var inner = self.container.querySelector('[data-stop]');
      if (inner && !inner.contains(e.target)) self._cerrarModal();
    });

    this.container.querySelector('#cli-form').addEventListener('submit', function (e) {
      e.preventDefault();
      self._guardar();
    });
  }

  _bindRowButtons() {
    var self = this;
    this.container.querySelectorAll('.btn-edit-cli').forEach(function (btn) {
      btn.addEventListener('click', function () {
        self._editar(parseInt(btn.dataset.id));
      });
    });
    this.container.querySelectorAll('.btn-del-cli').forEach(function (btn) {
      btn.addEventListener('click', function () {
        self._eliminar(parseInt(btn.dataset.id));
      });
    });
  }

  _abrirModal(item) {
    var modal = this.container.querySelector('#cli-modal');
    var title = this.container.querySelector('#cli-modal-title');

    if (item) {
      title.textContent = 'Editar cliente';
      this.editando = true;
      this.container.querySelector('#cli-id').value = item.id || '';
      this.container.querySelector('#cli-tipo-doc').value = item.tipo_doc || '6';
      this.container.querySelector('#cli-num-doc').value = item.num_doc || '';
      this.container.querySelector('#cli-razon').value = item.razon_social || '';
      this.container.querySelector('#cli-direccion').value = item.direccion || '';
      this.container.querySelector('#cli-email').value = item.email || '';
    } else {
      title.textContent = 'Nuevo cliente';
      this.editando = false;
      this.container.querySelector('#cli-id').value = '';
      this.container.querySelector('#cli-form').reset();
      this.container.querySelector('#cli-tipo-doc').value = '6';
    }

    modal.style.display = 'flex';
    this.container.querySelector('#cli-num-doc').focus();
  }

  _cerrarModal() {
    this.container.querySelector('#cli-modal').style.display = 'none';
  }

  async _editar(id) {
    try {
      var res = await App.api.obtenerCliente(id);
      if (res.success && res.data) {
        this._abrirModal(res.data);
      } else {
        Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'No se pudo cargar el cliente' });
      }
    } catch (e) {
      Swal.fire({ icon: 'error', title: 'Error', text: e.message });
    }
  }

  async _guardar() {
    var id = this.container.querySelector('#cli-id').value;
    var data = {
      tipo_doc: this.container.querySelector('#cli-tipo-doc').value,
      num_doc: this.container.querySelector('#cli-num-doc').value.trim(),
      razon_social: this.container.querySelector('#cli-razon').value.trim(),
      direccion: this.container.querySelector('#cli-direccion').value.trim(),
      email: this.container.querySelector('#cli-email').value.trim(),
    };

    var btn = this.container.querySelector('#cli-modal-save');
    btn.disabled = true;
    btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 icon-spin"></i> Guardando...';
    App.refreshIcons();

    try {
      var res = await App.api.guardarCliente(data, id ? parseInt(id) : 0);
      btn.disabled = false;
      btn.innerHTML = '<i data-lucide="save" class="w-4 h-4"></i> Guardar';

      if (res.success) {
        Swal.fire({ icon: 'success', title: 'Guardado', text: res.message, timer: 1500, showConfirmButton: false });
        this._cerrarModal();
        this._listar();
      } else {
        Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Error al guardar' });
      }
    } catch (e) {
      btn.disabled = false;
      btn.innerHTML = '<i data-lucide="save" class="w-4 h-4"></i> Guardar';
      Swal.fire({ icon: 'error', title: 'Error', text: e.message });
    }
  }

  async _eliminar(id) {
    var result = await Swal.fire({
      title: '\u00bfEliminar cliente?',
      text: 'Esta acci\u00f3n no se puede deshacer',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc2626',
      cancelButtonText: 'Cancelar',
      confirmButtonText: 'S\u00ed, eliminar',
    });

    if (!result.isConfirmed) return;

    try {
      var res = await App.api.eliminarCliente(id);
      if (res.success) {
        Swal.fire({ icon: 'success', title: 'Eliminado', text: res.message, timer: 1500, showConfirmButton: false });
        this._listar();
      } else {
        Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Error al eliminar' });
      }
    } catch (e) {
      Swal.fire({ icon: 'error', title: 'Error', text: e.message });
    }
  }
};
