var App = window.App || (window.App = {});

App.Productos = class Productos {
  constructor() {
    this.container = null;
    this.editando = false;
  }

  render(container) {
    this.container = container;
    this._cargarCategorias();
    this._listar();
    this._bind();
  }

  async _cargarCategorias() {
    try {
      var res = await App.api.listarCategorias();
      if (res.success && res.data) {
        App.CATEGORIAS = res.data;
      }
    } catch (e) {}
  }

  async _listar(buscar) {
    var tbody = this.container.querySelector('#prod-tbody');
    tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 2rem; color: rgb(148 163 184);"><i data-lucide="loader-2" class="w-5 h-5 icon-spin"></i> Cargando...</td></tr>';
    App.refreshIcons();

    try {
      var q = buscar || this.container.querySelector('#prod-buscar').value.trim();
      var url = '/productos' + (q ? '?buscar=' + encodeURIComponent(q) : '');
      var res = await App.api.listarProductos(q);

      if (!res.success) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 2rem; color: rgb(185 28 28);">Error al cargar</td></tr>';
        return;
      }

      var items = res.data || [];
      if (items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 2rem; color: rgb(148 163 184);"><i data-lucide="package" class="w-5 h-5"></i> Ning\u00fan producto registrado</td></tr>';
        App.refreshIcons();
        return;
      }

      var html = '';
      for (var i = 0; i < items.length; i++) {
        var p = items[i];
        var igvLabel = { '10': 'Gravado', '20': 'Exonerado', '30': 'Inafecto' }[p.tip_afe_igv] || p.tip_afe_igv;
        html += '<tr>'
          + '<td class="font-mono text-xs">' + App.escapeHtml(p.codigo) + '</td>'
          + '<td><div>' + App.escapeHtml(p.descripcion) + '</div>'
          + (p.cod_producto_sunat ? '<div class="text-xs" style="color:rgb(148 163 184);">SUNAT: ' + App.escapeHtml(p.cod_producto_sunat) + '</div>' : '')
          + '</td>'
          + '<td class="text-xs">' + App.escapeHtml(p.categoria || '') + '</td>'
          + '<td class="text-xs">' + App.escapeHtml(p.unidad) + '</td>'
          + '<td class="text-right font-semibold">' + App.fmtMoney(p.precio_unitario) + '</td>'
          + '<td class="text-xs">' + igvLabel + '</td>'
          + '<td style="text-align: center; white-space: nowrap;">'
          + '<button class="btn-ghost btn-edit-prod text-xs" data-id="' + p.id + '" title="Editar"><i data-lucide="pencil" class="w-4 h-4"></i></button>'
          + '<button class="btn-ghost btn-del-prod text-xs" style="color: rgb(220 38 38);" data-id="' + p.id + '" title="Eliminar"><i data-lucide="trash-2" class="w-4 h-4"></i></button>'
          + '</td>'
          + '</tr>';
      }
      tbody.innerHTML = html;
      App.refreshIcons();
      this._bindRowButtons();
    } catch (e) {
      tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 2rem; color: rgb(185 28 28);">' + App.escapeHtml(e.message) + '</td></tr>';
      App.refreshIcons();
    }
  }

  _bind() {
    var self = this;

    this.container.querySelector('#btn-crear-producto').addEventListener('click', function () {
      self._abrirModal();
    });

    this.container.querySelector('#prod-btn-buscar').addEventListener('click', function () {
      self._listar();
    });

    this.container.querySelector('#prod-buscar').addEventListener('keydown', function (e) {
      if (e.key === 'Enter') self._listar();
    });

    this.container.querySelector('#prod-modal-close').addEventListener('click', function () {
      self._cerrarModal();
    });

    this.container.querySelector('#prod-modal-cancel').addEventListener('click', function () {
      self._cerrarModal();
    });

    this.container.querySelector('#prod-modal').addEventListener('click', function (e) {
      var inner = self.container.querySelector('[data-stop]');
      if (inner && !inner.contains(e.target)) self._cerrarModal();
    });

    this.container.querySelector('#prod-form').addEventListener('submit', function (e) {
      e.preventDefault();
      self._guardar();
    });
  }

  _bindRowButtons() {
    var self = this;
    this.container.querySelectorAll('.btn-edit-prod').forEach(function (btn) {
      btn.addEventListener('click', function () {
        self._editar(parseInt(btn.dataset.id));
      });
    });
    this.container.querySelectorAll('.btn-del-prod').forEach(function (btn) {
      btn.addEventListener('click', function () {
        self._eliminar(parseInt(btn.dataset.id));
      });
    });
  }

  _abrirModal(item) {
    var modal = this.container.querySelector('#prod-modal');
    var title = this.container.querySelector('#prod-modal-title');
    var idInput = this.container.querySelector('#prod-id');

    if (item) {
      title.textContent = 'Editar producto';
      this.editando = true;
      idInput.value = item.id || '';
      this.container.querySelector('#prod-codigo').value = item.codigo || '';
      this.container.querySelector('#prod-cod-sunat').value = item.cod_producto_sunat || '';
      this.container.querySelector('#prod-descripcion').value = item.descripcion || '';
      this.container.querySelector('#prod-unidad').value = item.unidad || 'NIU';
      this.container.querySelector('#prod-precio').value = item.precio_unitario || '';
      this.container.querySelector('#prod-igv').value = item.tip_afe_igv || '10';
      this.container.querySelector('#prod-icbper').value = item.icbper || '';
      this.container.querySelector('#prod-factor-icbper').value = item.factor_icbper || '';
      this._seleccionarCategoria(item.categoria_id);
    } else {
      title.textContent = 'Nuevo producto';
      this.editando = false;
      idInput.value = '';
      this.container.querySelector('#prod-form').reset();
      this.container.querySelector('#prod-codigo').value = '';
      this.container.querySelector('#prod-cod-sunat').value = '';
      this.container.querySelector('#prod-descripcion').value = '';
      this.container.querySelector('#prod-unidad').value = 'NIU';
      this.container.querySelector('#prod-precio').value = '';
      this.container.querySelector('#prod-igv').value = '10';
      this.container.querySelector('#prod-icbper').value = '';
      this.container.querySelector('#prod-factor-icbper').value = '';
      this.container.querySelector('#prod-categoria').value = '';
    }

    modal.style.display = 'flex';
    this.container.querySelector('#prod-codigo').focus();
  }

  _cerrarModal() {
    this.container.querySelector('#prod-modal').style.display = 'none';
  }

  _seleccionarCategoria(categoriaId) {
    var sel = this.container.querySelector('#prod-categoria');
    for (var i = 0; i < sel.options.length; i++) {
      if (sel.options[i].value == categoriaId) {
        sel.selectedIndex = i;
        return;
      }
    }
  }

  _llenarCategorias(select, selectedId) {
    var cats = App.CATEGORIAS || [];
    for (var i = 0; i < cats.length; i++) {
      var opt = document.createElement('option');
      opt.value = cats[i].id;
      opt.textContent = cats[i].nombre;
      if (cats[i].id == selectedId) opt.selected = true;
      select.appendChild(opt);
    }
  }

  async _editar(id) {
    try {
      var res = await App.api.obtenerProducto(id);
      if (res.success && res.data) {
        var cats = res.extra && res.extra.categorias ? res.extra.categorias : App.CATEGORIAS || [];
        var select = this.container.querySelector('#prod-categoria');
        select.innerHTML = '<option value="">Sin categor\u00eda</option>';
        for (var i = 0; i < cats.length; i++) {
          var opt = document.createElement('option');
          opt.value = cats[i].id;
          opt.textContent = cats[i].nombre;
          select.appendChild(opt);
        }
        App.CATEGORIAS = cats;
        this._abrirModal(res.data);
      } else {
        Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'No se pudo cargar el producto' });
      }
    } catch (e) {
      Swal.fire({ icon: 'error', title: 'Error', text: e.message });
    }
  }

  async _guardar() {
    var id = this.container.querySelector('#prod-id').value;
    var data = {
      codigo: this.container.querySelector('#prod-codigo').value.trim(),
      cod_producto_sunat: this.container.querySelector('#prod-cod-sunat').value.trim(),
      descripcion: this.container.querySelector('#prod-descripcion').value.trim(),
      unidad: this.container.querySelector('#prod-unidad').value,
      precio_unitario: this.container.querySelector('#prod-precio').value,
      tip_afe_igv: this.container.querySelector('#prod-igv').value,
      categoria_id: this.container.querySelector('#prod-categoria').value || '',
      icbper: this.container.querySelector('#prod-icbper').value || '',
      factor_icbper: this.container.querySelector('#prod-factor-icbper').value || '',
    };

    var btn = this.container.querySelector('#prod-modal-save');
    btn.disabled = true;
    btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 icon-spin"></i> Guardando...';
    App.refreshIcons();

    try {
      var res = await App.api.guardarProducto(data, id ? parseInt(id) : 0);
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
      title: '\u00bfEliminar producto?',
      text: 'Esta acci\u00f3n no se puede deshacer',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc2626',
      cancelButtonText: 'Cancelar',
      confirmButtonText: 'S\u00ed, eliminar',
    });

    if (!result.isConfirmed) return;

    try {
      var res = await App.api.eliminarProducto(id);
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
