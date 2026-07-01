var App = window.App || (window.App = {});

App.Login = class Login {
  constructor() {
    this.usuario = '';
    this.password = '';
    this.error = null;
    this.loading = false;
    this.container = null;
  }

  render(container) {
    this.container = container;
    this._renderHTML();
    this._bind();
  }

  _renderHTML() {
    var errorHTML = this.error
      ? '<div style="padding: 0.75rem; background: rgb(254 242 242); border-radius: 0.75rem; font-size: 0.875rem; color: rgb(185 28 28); display: flex; align-items: center; gap: 0.5rem;">'
        + '<i data-lucide="x-circle" class="w-4 h-4" style="flex-shrink: 0;"></i> ' + App.escapeHtml(this.error)
        + '</div>'
      : '';
    var btnContent = this.loading
      ? '<span style="display: inline-block; width: 1rem; height: 1rem; border-radius: 9999px; border: 2px solid rgb(255 255 255 / 0.4); border-top-color: white; animation: icon-spin 0.8s linear infinite;"></span> Entrando...'
      : '<i data-lucide="log-in" class="w-4 h-4"></i> Entrar';
    this.container.innerHTML = ''
      + '<div style="min-height: 100vh; display: flex; background: white;">'
        + '<div class="login-brand" style="display: none; position: relative; overflow: hidden; padding: 3rem; flex-direction: column; justify-content: space-between; background: rgb(15 23 42); color: white; width: 45%;">'
          + '<div style="position: absolute; top: -120px; right: -120px; width: 380px; height: 380px; border-radius: 9999px; background: rgb(37 99 235 / 0.25); pointer-events: none;"></div>'
          + '<div style="position: absolute; bottom: -80px; left: -100px; width: 320px; height: 320px; border-radius: 9999px; background: rgb(59 130 246 / 0.18); pointer-events: none;"></div>'
          + '<div style="position: absolute; top: 35%; left: 55%; width: 180px; height: 180px; border-radius: 9999px; background: rgb(96 165 250 / 0.12); pointer-events: none;"></div>'
      + '<div style="position: relative; z-index: 10; display: flex; align-items: center; gap: 0.75rem;">'
              + '<img src="' + (typeof BASE_PATH !== 'undefined' ? BASE_PATH : '') + '/api/logo-imagen" alt="Logo" style="height: 5rem;" />'
            + '</div>'
          + '<div style="position: relative; z-index: 10;">'
            + '<div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.375rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.025em; margin-bottom: 1.5rem; background: rgb(37 99 235 / 0.25); color: rgb(147 197 253);">'
              + '<i data-lucide="sparkles" class="w-[14px] h-[14px]"></i> Facturaci\u00f3n electr\u00f3nica'
            + '</div>'
            + '<h2 style="font-size: 2.5rem; font-weight: 800; line-height: 1.1; letter-spacing: -0.025em; margin-bottom: 1rem;">'
              + 'Facturaci\u00f3n electr\u00f3nica <span style="color: rgb(96 165 250);">sin complicaciones.</span>'
            + '</h2>'
            + '<p style="color: rgb(203 213 225); font-size: 1rem; line-height: 1.6; max-width: 28rem;">'
              + 'Emite facturas, boletas, notas de cr\u00e9dito y gu\u00edas de remisi\u00f3n conect\u00e1ndote directamente a SUNAT. Todo desde una \u00fanica API.'
            + '</p>'
            + '<div style="margin-top: 2.5rem; display: flex; flex-direction: column; gap: 1rem;">'
              + this._featureHTML('zap', 'Emisi\u00f3n en segundos', 'Env\u00edo directo a SUNAT o en modo lote')
              + this._featureHTML('shield-check', 'Certificado digital', 'Firma XML + validaci\u00f3n SUNAT incluida')
            + '</div>'
          + '</div>'
          + '<div style="position: relative; z-index: 10; display: flex; align-items: center; justify-content: space-between; font-size: 0.75rem; color: rgb(148 163 184);">'
            + '<div>Hecho con ♥ en Per\u00fa</div>'
            + '<div style="font-family: JetBrains Mono, monospace;">v2.0.0</div>'
          + '</div>'
        + '</div>'
        + '<div style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 1.5rem;">'
          + '<div style="width: 100%; max-width: 24rem;">'
            + '<div class="login-mobile-logo" style="display: flex; flex-direction: column; align-items: center; margin-bottom: 2rem;">'
              + '<img src="' + (typeof BASE_PATH !== 'undefined' ? BASE_PATH : '') + '/api/logo-imagen" alt="Logo" style="height: 6rem; margin-bottom: 1rem;" />'
            + '</div>'
            + '<div style="margin-bottom: 2rem;">'
              + '<h1 style="font-size: 1.875rem; font-weight: 800; letter-spacing: -0.025em; color: rgb(15 23 42); margin-bottom: 0.375rem;">Bienvenido <span role="img">\ud83d\udc4b</span></h1>'
              + '<p style="color: rgb(100 116 139); font-size: 0.875rem;">Inicia sesi\u00f3n para acceder al sistema.</p>'
            + '</div>'
            + '<form id="login-form" style="display: flex; flex-direction: column; gap: 1rem;">'
              + '<div>'
                + '<label class="label">Usuario</label>'
                + '<input id="login-user" class="input" autofocus value="' + App.escapeHtml(this.usuario) + '" placeholder="usuario" required />'
              + '</div>'
              + '<div>'
                + '<label class="label">Contrase\u00f1a</label>'
                + '<input id="login-pass" type="password" class="input" value="' + App.escapeHtml(this.password) + '" placeholder="contrase\u00f1a" required />'
              + '</div>'
              + errorHTML
              + '<button type="submit" class="btn-primary" style="width: 100%; margin-top: 0.5rem; padding: 0.75rem 1rem;" ' + (this.loading ? 'disabled' : '') + '>'
                + btnContent
              + '</button>'
            + '</form>'

            + '<div style="margin-top: 1.5rem; text-align: center; font-size: 0.75rem; color: rgb(148 163 184);">'
              + '\u00a9 ' + new Date().getFullYear() + ' SUNAT'
            + '</div>'
          + '</div>'
        + '</div>'
      + '</div>'
      + '<style>'
        + '@media (min-width: 1024px) {'
          + '.login-brand { display: flex !important; }'
          + '.login-mobile-logo { display: none !important; }'
        + '}'
      + '</style>';
    App.refreshIcons();
  }

  _featureHTML(iconName, title, subtitle) {
    return '<div style="display: flex; align-items: flex-start; gap: 0.75rem;">'
      + '<div style="width: 2.5rem; height: 2.5rem; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: rgb(37 99 235 / 0.2);">'
        + '<i data-lucide="' + iconName + '" class="w-5 h-5" style="color: rgb(147 197 253);"></i>'
      + '</div>'
      + '<div>'
        + '<div style="font-weight: 700; color: white; font-size: 0.875rem;">' + App.escapeHtml(title) + '</div>'
        + '<div style="font-size: 0.75rem; color: rgb(148 163 184); margin-top: 0.125rem;">' + App.escapeHtml(subtitle) + '</div>'
      + '</div>'
    + '</div>';
  }

  _bind() {
    var self = this;
    var user = this.container.querySelector('#login-user');
    var pass = this.container.querySelector('#login-pass');
    var form = this.container.querySelector('#login-form');
    user.addEventListener('input', function (e) {
      self.usuario = e.target.value;
      if (self.error) { self.error = null; self._renderHTML(); self._bind(); }
    });
    pass.addEventListener('input', function (e) {
      self.password = e.target.value;
      if (self.error) { self.error = null; self._renderHTML(); self._bind(); }
    });
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      self._submit();
    });
  }

  async _submit() {
    this.loading = true;
    this._renderHTML();
    this._bind();
    try {
      var res = await App.api.login(this.usuario, this.password);
      if (res.success) {
        window.location.href = (typeof BASE_PATH !== 'undefined' ? BASE_PATH : '') + '/';
      } else {
        this.error = res.message || 'Error desconocido';
        this.loading = false;
        this._renderHTML();
        this._bind();
      }
    } catch (e) {
      this.error = 'Error de conexi\u00f3n';
      this.loading = false;
      this._renderHTML();
      this._bind();
    }
  }
};
