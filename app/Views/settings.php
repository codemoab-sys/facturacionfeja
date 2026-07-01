<div>
  <h1 class="page-title">
    <i data-lucide="settings" class="w-7 h-7"></i> Configuraci&oacute;n
  </h1>

  <div style="display: flex; flex-direction: column; gap: 2rem;">
    <form id="config-form">
      <div class="card">
        <h2 class="section-title">
          <i data-lucide="globe" class="w-5 h-5"></i> Conexi&oacute;n API
        </h2>
        <p style="font-size: 0.8rem; color: rgb(100 116 139); margin-bottom: 1rem;">
          Configura la URL y credenciales de la API de facturaci&oacute;n electr&oacute;nica.
        </p>
        <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;" class="config-grid-2">
          <div style="grid-column: 1 / -1;">
            <label class="label">URL Base</label>
            <input id="cfg-url" class="input font-mono" value="<?= htmlspecialchars($cfg['base_url'] ?? '', ENT_QUOTES) ?>" placeholder="https://ejemplo.com/api/v1" />
          </div>
          <div>
            <label class="label">X-Api-Key</label>
            <input id="cfg-key" class="input font-mono" type="password" value="<?= htmlspecialchars($cfg['api_key'] ?? '', ENT_QUOTES) ?>" placeholder="Ingresa tu API Key" />
          </div>
          <div>
            <label class="label">X-Api-Secret</label>
            <input id="cfg-secret" class="input font-mono" type="password" value="<?= htmlspecialchars($cfg['api_secret'] ?? '', ENT_QUOTES) ?>" placeholder="Ingresa tu API Secret" />
          </div>
        </div>
        <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; margin-top: 1.5rem;">
          <button type="submit" id="cfg-save" class="btn-primary">
            <i data-lucide="save" class="w-4 h-4"></i> Guardar configuraci&oacute;n
          </button>
          <button type="button" id="cfg-test" class="btn-secondary">
            <i data-lucide="wifi" class="w-4 h-4"></i> Probar conexi&oacute;n
          </button>
          <button type="button" id="cfg-ir-dashboard" class="btn-primary" style="display: none;">
            <i data-lucide="arrow-right" class="w-4 h-4"></i> Ir al Dashboard
          </button>
        </div>
        <div id="cfg-result" style="display: none; margin-top: 1rem;"></div>
      </div>
    </form>

    <div class="card">
      <h2 class="section-title">
        <i data-lucide="shield" class="w-5 h-5"></i> Certificado Digital
      </h2>
      <p style="font-size: 0.8rem; color: rgb(100 116 139); margin-bottom: 1rem;">
        Sube tu certificado <strong>.p12</strong> o <strong>.pfx</strong> para firmar documentos electr&oacute;nicos.
      </p>
      <div style="display: flex; flex-direction: column; gap: 1rem;">
        <div>
          <label class="label">Archivo de certificado</label>
          <input id="cfg-cert-file" type="file" accept=".p12,.pfx" class="input" style="padding: 0.5rem;" />
        </div>
        <div>
          <label class="label">Contrase&ntilde;a del certificado</label>
          <input id="cfg-cert-pass" type="password" class="input" placeholder="Ingresa la contrase&ntilde;a" />
        </div>
        <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
          <button type="button" id="cfg-upload-cert" class="btn-primary">
            <i data-lucide="upload" class="w-4 h-4"></i> Subir certificado
          </button>
          <button type="button" id="cfg-delete-cert" class="btn-danger" style="display: none;">
            <i data-lucide="trash-2" class="w-4 h-4"></i> Eliminar certificado
          </button>
        </div>
        <div id="cfg-cert-status" style="display: none;"></div>
      </div>
    </div>
  </div>

  <style>
    @media (min-width: 640px) { .config-grid-2 { grid-template-columns: repeat(2, 1fr) !important; } }
  </style>
</div>