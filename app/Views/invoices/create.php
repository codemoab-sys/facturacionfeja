<div>
  <h1 class="page-title">
    <i data-lucide="file-text" class="w-7 h-7"></i> Nueva Factura
  </h1>
  <form id="f-form" style="display: flex; flex-direction: column; gap: 1.5rem;">
    <div class="card">
      <h2 class="section-title">Datos del documento</h2>
      <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;" class="form-grid-4">
        <div><label class="label">Serie</label>
          <input id="f-serie" class="input font-mono" value="F001" maxlength="4" required /></div>
        <div><label class="label">Fecha emisión</label>
          <input id="f-fecha" type="date" class="input" required /></div>
        <div><label class="label">Moneda</label>
          <select id="f-moneda" class="input">
            <option value="PEN" selected>PEN (Soles)</option>
            <option value="USD">USD (Dólares)</option>
            <option value="EUR">EUR (Euros)</option>
          </select></div>
        <div><label class="label">Forma de pago</label>
          <select id="f-pago" class="input">
            <option value="Contado" selected>Contado</option>
            <option value="Credito">Crédito</option>
          </select></div>
      </div>
    </div>
    <div id="f-cuotas-section" class="card" style="display: none;">
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
        <h2 class="section-title" style="margin-bottom: 0;">Cuotas de pago</h2>
        <button type="button" id="f-add-cuota" class="btn-primary text-sm"><i data-lucide="plus" class="w-4 h-4"></i> Agregar cuota</button>
      </div>
      <div id="f-cuotas-rows"><p style="font-size: 0.875rem; color: rgb(100 116 139);">Agrega al menos una cuota.</p></div>
    </div>
    <div class="card">
      <h2 class="section-title">Cliente</h2>
      <div id="f-client-selector"></div>
    </div>
    <div class="card">
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
        <h2 class="section-title" style="margin-bottom: 0;">Productos / Servicios</h2>
        <button type="button" id="f-add-prod" class="btn-primary text-sm"><i data-lucide="plus" class="w-4 h-4"></i> Agregar producto</button>
      </div>
      <div id="f-items-table"></div>
    </div>
    <div class="card">
      <label class="label">Observaciones (opcional)</label>
      <textarea id="f-obs" class="input" rows="2" placeholder="Comentarios adicionales..."></textarea>
    </div>
    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
      <div id="f-pdf-format"></div>
      <button type="submit" id="f-submit" class="btn-primary">
        <i data-lucide="check" class="w-4 h-4"></i> Emitir Factura
      </button>
    </div>
  </form>
  <style>
    @media (min-width: 640px) { .form-grid-4 { grid-template-columns: repeat(2, 1fr) !important; } }
    @media (min-width: 768px) { .form-grid-4 { grid-template-columns: repeat(4, 1fr) !important; } }
  </style>
</div>
