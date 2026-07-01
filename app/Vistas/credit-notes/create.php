<div>
  <h1 class="page-title">
    <i data-lucide="trending-down" class="w-7 h-7"></i> Nueva Nota de Crédito
  </h1>
  <form id="f-form" style="display: flex; flex-direction: column; gap: 1.5rem;">
    <div class="card">
      <h2 class="section-title">Datos del documento</h2>
      <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;" class="form-grid-4">
        <div><label class="label">Serie</label>
          <input id="f-serie" class="input font-mono" value="FC01" maxlength="4" required /></div>
        <div><label class="label">Fecha emisión</label>
          <input id="f-fecha" type="date" class="input" required /></div>
        <div><label class="label">Moneda</label>
          <select id="f-moneda" class="input">
            <option value="PEN" selected>PEN (Soles)</option>
            <option value="USD">USD (Dólares)</option>
          </select></div>
        <div><label class="label">Tipo doc. afectado</label>
          <select id="f-doc-tipo" class="input">
            <option value="01">Factura</option>
            <option value="03">Boleta</option>
            <option value="07">Nota de Crédito</option>
            <option value="08">Nota de Débito</option>
          </select></div>
        <div><label class="label">Serie doc. afectado</label>
          <input id="f-doc-serie" class="input font-mono" value="F001" maxlength="4" /></div>
        <div><label class="label">Correlativo doc. afectado</label>
          <input id="f-doc-correlativo" class="input font-mono" placeholder="00000001" /></div>
      </div>
    </div>
    <div class="card">
      <h2 class="section-title">Motivo</h2>
      <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;" class="form-grid-2">
        <div>
          <label class="label">Código motivo</label>
          <select id="f-motivo" class="input">
            <option value="01">Anulación de la operación</option>
            <option value="02">Anulación por error en el RUC</option>
            <option value="03">Corrección por error en la descripción</option>
            <option value="04">Descuento global</option>
            <option value="05">Descuento por ítem</option>
            <option value="06" selected>Devolución total/parcial</option>
            <option value="07">Bonificación/Descuento</option>
            <option value="08">Disminución en el valor</option>
            <option value="09">Otros</option>
          </select></div>
        <div>
          <label class="label">Descripción del motivo</label>
          <input id="f-motivo-desc" class="input" placeholder="Describir motivo..." />
        </div>
      </div>
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
    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 1rem; flex-wrap: wrap;">
      <div id="f-pdf-format"></div>
      <button type="submit" id="f-submit" class="btn-primary">
        <i data-lucide="check" class="w-4 h-4"></i> Emitir Nota de Crédito
      </button>
    </div>
  </form>
  <style>
    @media (min-width: 640px) { .form-grid-4 { grid-template-columns: repeat(2, 1fr) !important; } }
    @media (min-width: 768px) { .form-grid-4 { grid-template-columns: repeat(4, 1fr) !important; } }
    @media (min-width: 640px) { .form-grid-2 { grid-template-columns: repeat(2, 1fr) !important; } }
  </style>
</div>
