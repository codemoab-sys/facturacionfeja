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
      var res = await App.api.configObtener();
      if (res.success && res.data) {
        this.container.querySelector('#cfg-url').value = res.data.base_url || '';
        this.container.querySelector('#cfg-key').value = res.data.api_key || '';
        this.container.querySelector('#cfg-secret').value = res.data.api_secret || '';
      }
    } catch (e) {}
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
      var res = await App.api.configGuardar(data);
      if (res.success) {
        btn.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i> Guardado';
        setTimeout(function () {
          btn.innerHTML = '<i data-lucide="save" class="w-4 h-4"></i> Guardar configuraci\u00f3n';
          btn.disabled = false;
        }, 1500);
      } else {
        alert(res.message || 'Error al guardar');
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="save" class="w-4 h-4"></i> Guardar configuraci\u00f3n';
      }
    } catch (e) {
      alert('Error: ' + e.message);
      btn.disabled = false;
      btn.innerHTML = '<i data-lucide="save" class="w-4 h-4"></i> Guardar configuraci\u00f3n';
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
      var res = await App.api.testConexion(data);
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
};
