var App = window.App || (window.App = {});

App.Settings = class Settings {
  constructor() {
    this.container = null;
  }

  render(container) {
    this.container = container;
    this._load();
  }

  async _load() {
    try {
      var res = await App.api.obtenerConfig();
      if (res.success && res.data) {
        this.container.querySelector('#cfg-url').value = res.data.base_url || '';
        this.container.querySelector('#cfg-key').value = res.data.api_key || '';
        this.container.querySelector('#cfg-secret').value = res.data.api_secret || '';
      }
    } catch (e) {}
    this._checkCert();
    this._bind();
    App.refreshIcons();
  }

  _bind() {
    var self = this;
    var form = this.container.querySelector('#config-form');
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      self._save();
    });
    this.container.querySelector('#cfg-test').addEventListener('click', function () { self._test(); });
    this.container.querySelector('#cfg-ir-dashboard').addEventListener('click', function () {
      window.location.href = (typeof BASE_PATH !== 'undefined' ? BASE_PATH : '') + '/';
    });
    this.container.querySelector('#cfg-upload-cert').addEventListener('click', function () { self._uploadCert(); });
    this.container.querySelector('#cfg-delete-cert').addEventListener('click', function () { self._deleteCert(); });
  }

  async _save() {
    var data = {
      base_url: this.container.querySelector('#cfg-url').value.trim(),
      api_key: this.container.querySelector('#cfg-key').value.trim(),
      api_secret: this.container.querySelector('#cfg-secret').value.trim(),
    };
    var btn = this.container.querySelector('#cfg-save');
    btn.disabled = true;
    btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 icon-spin"></i> Guardando...';
    App.refreshIcons();
    try {
      var res = await App.api.guardarConfig(data);
      btn.disabled = false;
      btn.innerHTML = '<i data-lucide="save" class="w-4 h-4"></i> Guardar configuraci\u00f3n';
      if (res.success) {
        Swal.fire({ icon: 'success', title: 'Guardado', text: 'Configuraci\u00f3n guardada correctamente', timer: 2000, showConfirmButton: false });
      } else {
        Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Error al guardar' });
      }
    } catch (e) {
      btn.disabled = false;
      btn.innerHTML = '<i data-lucide="save" class="w-4 h-4"></i> Guardar configuraci\u00f3n';
      Swal.fire({ icon: 'error', title: 'Error', text: e.message });
    }
  }

  async _test() {
    var btn = this.container.querySelector('#cfg-test');
    var resultDiv = this.container.querySelector('#cfg-result');
    var irBtn = this.container.querySelector('#cfg-ir-dashboard');
    btn.disabled = true;
    btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 icon-spin"></i> Probando...';
    resultDiv.style.display = 'none';
    irBtn.style.display = 'none';
    App.refreshIcons();
    try {
      var data = {
        base_url: this.container.querySelector('#cfg-url').value.trim(),
        api_key: this.container.querySelector('#cfg-key').value.trim(),
        api_secret: this.container.querySelector('#cfg-secret').value.trim(),
      };
      var res = await App.api.probarConexion(data);
      resultDiv.style.display = 'block';
      if (res.success) {
        resultDiv.innerHTML = '<div style="padding: 1rem; background: rgb(220 252 231); border-radius: 0.75rem; color: rgb(22 101 52); display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="check-circle-2" class="w-5 h-5"></i> Conexi\u00f3n exitosa</div>';
        irBtn.style.display = 'inline-flex';
      } else {
        resultDiv.innerHTML = '<div style="padding: 1rem; background: rgb(254 242 242); border-radius: 0.75rem; color: rgb(185 28 28); display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="x-circle" class="w-5 h-5"></i> ' + App.escapeHtml(res.message || 'Error de conexi\u00f3n') + '</div>';
      }
      App.refreshIcons();
    } catch (e) {
      resultDiv.style.display = 'block';
      resultDiv.innerHTML = '<div style="padding: 1rem; background: rgb(254 242 242); border-radius: 0.75rem; color: rgb(185 28 28); display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="x-circle" class="w-5 h-5"></i> ' + App.escapeHtml(e.message) + '</div>';
      App.refreshIcons();
    }
    btn.disabled = false;
    btn.innerHTML = '<i data-lucide="wifi" class="w-4 h-4"></i> Probar conexi\u00f3n';
  }

  async _uploadCert() {
    var fileInput = this.container.querySelector('#cfg-cert-file');
    var passInput = this.container.querySelector('#cfg-cert-pass');
    var btn = this.container.querySelector('#cfg-upload-cert');
    var statusDiv = this.container.querySelector('#cfg-cert-status');

    if (!fileInput.files || !fileInput.files[0]) {
      statusDiv.style.display = 'block';
      statusDiv.innerHTML = '<div style="padding: 0.75rem; background: rgb(254 242 242); border-radius: 0.75rem; color: rgb(185 28 28); display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="x-circle" class="w-4 h-4"></i> Selecciona un archivo .p12, .pfx o .pem</div>';
      App.refreshIcons();
      return;
    }

    var formData = new FormData();
    formData.append('certificado', fileInput.files[0]);
    formData.append('contrasena_certificado', passInput.value.trim());

    btn.disabled = true;
    btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 icon-spin"></i> Subiendo...';
    statusDiv.style.display = 'none';
    App.refreshIcons();

    try {
      var res = await App.api.subirCertificado(formData);
      statusDiv.style.display = 'block';
      if (res.success) {
        statusDiv.innerHTML = '<div style="padding: 0.75rem; background: rgb(220 252 231); border-radius: 0.75rem; color: rgb(22 101 52); display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="check-circle-2" class="w-4 h-4"></i> Certificado subido correctamente</div>';
        this.container.querySelector('#cfg-delete-cert').style.display = 'inline-flex';
      } else {
        statusDiv.innerHTML = '<div style="padding: 0.75rem; background: rgb(254 242 242); border-radius: 0.75rem; color: rgb(185 28 28); display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="x-circle" class="w-4 h-4"></i> ' + App.escapeHtml(res.message || 'Error al subir certificado') + '</div>';
      }
    } catch (e) {
      statusDiv.style.display = 'block';
      statusDiv.innerHTML = '<div style="padding: 0.75rem; background: rgb(254 242 242); border-radius: 0.75rem; color: rgb(185 28 28); display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="x-circle" class="w-4 h-4"></i> ' + App.escapeHtml(e.message) + '</div>';
    }
    btn.disabled = false;
    btn.innerHTML = '<i data-lucide="upload" class="w-4 h-4"></i> Subir certificado';
    App.refreshIcons();
  }

  async _deleteCert() {
    var btn = this.container.querySelector('#cfg-delete-cert');
    var statusDiv = this.container.querySelector('#cfg-cert-status');

    if (!confirm('\u00bfEliminar certificado?')) return;

    btn.disabled = true;
    statusDiv.style.display = 'none';

    try {
      var res = await App.api.eliminarCertificado();
      statusDiv.style.display = 'block';
      if (res.success) {
        statusDiv.innerHTML = '<div style="padding: 0.75rem; background: rgb(254 243 199); border-radius: 0.75rem; color: rgb(146 64 14); display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="info" class="w-4 h-4"></i> Certificado eliminado</div>';
        this.container.querySelector('#cfg-delete-cert').style.display = 'none';
      } else {
        statusDiv.innerHTML = '<div style="padding: 0.75rem; background: rgb(254 242 242); border-radius: 0.75rem; color: rgb(185 28 28); display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="x-circle" class="w-4 h-4"></i> ' + App.escapeHtml(res.message || 'Error') + '</div>';
      }
    } catch (e) {
      statusDiv.style.display = 'block';
      statusDiv.innerHTML = '<div style="padding: 0.75rem; background: rgb(254 242 242); border-radius: 0.75rem; color: rgb(185 28 28); display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="x-circle" class="w-4 h-4"></i> ' + App.escapeHtml(e.message) + '</div>';
    }
    btn.disabled = false;
    App.refreshIcons();
  }

  async _checkCert() {
    try {
      var res = await App.api.estadoCertificado();
      if (res.success && res.data && res.data.tiene_certificado) {
        this.container.querySelector('#cfg-delete-cert').style.display = 'inline-flex';
      }
    } catch (e) {}
  }
};
