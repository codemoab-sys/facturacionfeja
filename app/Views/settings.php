<div>
  <h1 class="page-title">
    <i data-lucide="settings" class="w-7 h-7"></i> Configuración
  </h1>
  <form id="config-form" style="display: flex; flex-direction: column; gap: 1.5rem;">
    <div class="card">
      <h2 class="section-title">Conexión API</h2>
      <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;" class="config-grid-2">
        <div>
          <label class="label">URL Base</label>
          <input id="cfg-url" class="input font-mono" value="<?= htmlspecialchars($cfg['base_url'] ?? '', ENT_QUOTES) ?>" placeholder="https://ejemplo.com/api/v1" />
        </div>
      </div>
    </div>
    <div class="card">
      <h2 class="section-title">Credenciales</h2>
      <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;" class="config-grid-2">
        <div>
          <label class="label">X-Api-Key</label>
          <input id="cfg-key" class="input font-mono" type="password" value="<?= htmlspecialchars($cfg['api_key'] ?? '', ENT_QUOTES) ?>" placeholder="Ingresa tu API Key" />
        </div>
        <div>
          <label class="label">X-Api-Secret</label>
          <input id="cfg-secret" class="input font-mono" type="password" value="<?= htmlspecialchars($cfg['api_secret'] ?? '', ENT_QUOTES) ?>" placeholder="Ingresa tu API Secret" />
        </div>
      </div>
    </div>
    <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
      <button type="submit" id="cfg-save" class="btn-primary">
        <i data-lucide="save" class="w-4 h-4"></i> Guardar configuración
      </button>
      <button type="button" id="cfg-test" class="btn-secondary">
        <i data-lucide="wifi" class="w-4 h-4"></i> Probar conexión
      </button>
      <button type="button" id="cfg-ir-dashboard" class="btn-primary" style="display: none;">
        <i data-lucide="arrow-right" class="w-4 h-4"></i> Ir al Dashboard
      </button>
    </div>
    <div id="cfg-result" style="display: none;"></div>
  </form>
  <style>
    @media (min-width: 640px) { .config-grid-2 { grid-template-columns: repeat(2, 1fr) !important; } }
  </style>
</div>