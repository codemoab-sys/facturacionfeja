<div>
  <h1 class="page-title">
    <i data-lucide="truck" class="w-7 h-7"></i> Nueva Guía de Remisión
  </h1>
  <form id="f-form" style="display: flex; flex-direction: column; gap: 1.5rem;">
    <div class="card">
      <h2 class="section-title">Datos del documento</h2>
      <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;" class="form-grid-4">
        <div><label class="label">Serie</label>
          <input id="f-serie" class="input font-mono" value="T001" maxlength="4" required /></div>
        <div><label class="label">Fecha emisión</label>
          <input id="f-fecha" type="date" class="input" required /></div>
        <div><label class="label">Motivo traslado</label>
          <select id="f-motivo" class="input">
            <option value="01">Venta</option>
            <option value="02">Compra</option>
            <option value="04">Traslado entre establecimientos</option>
            <option value="08">Importación</option>
            <option value="09">Exportación</option>
            <option value="13">Otros</option>
          </select></div>
        <div><label class="label">Modalidad</label>
          <select id="f-modalidad" class="input">
            <option value="01">Transporte público</option>
            <option value="02">Transporte privado</option>
          </select></div>
      </div>
    </div>
    <div class="card">
      <h2 class="section-title">Fechas</h2>
      <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;" class="form-grid-2">
        <div><label class="label">Fecha de traslado</label>
          <input id="f-fecha-traslado" type="date" class="input" required /></div>
      </div>
    </div>
    <div class="card">
      <h2 class="section-title">Destinatario</h2>
      <div id="f-client-selector"></div>
    </div>
    <div class="card">
      <h2 class="section-title">Trayecto</h2>
      <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;" class="form-grid-2">
        <div><label class="label">Ubigeo partida</label>
          <input id="f-partida-ubigeo" class="input font-mono" value="150101" /></div>
        <div><label class="label">Dirección partida</label>
          <input id="f-partida-dir" class="input" value="AV. LIMA 123" /></div>
        <div><label class="label">Ubigeo llegada</label>
          <input id="f-llegada-ubigeo" class="input font-mono" value="150101" /></div>
        <div><label class="label">Dirección llegada</label>
          <input id="f-llegada-dir" class="input" placeholder="Dirección de llegada..." /></div>
      </div>
    </div>
    <div class="card">
      <h2 class="section-title">Transporte</h2>
      <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;" class="form-grid-3">
        <div><label class="label">Placa vehículo</label>
          <input id="f-placa" class="input font-mono" value="ABC-123" /></div>
        <div><label class="label">Doc. conductor</label>
          <input id="f-conductor-doc" class="input font-mono" placeholder="DNI o RUC" /></div>
        <div><label class="label">Nombre conductor</label>
          <input id="f-conductor-nombre" class="input" placeholder="Nombre del conductor" /></div>
      </div>
    </div>
    <div class="card">
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
        <h2 class="section-title" style="margin-bottom: 0;">Productos</h2>
        <button type="button" id="f-add-prod" class="btn-primary text-sm"><i data-lucide="plus" class="w-4 h-4"></i> Agregar producto</button>
      </div>
      <div id="f-items-table"></div>
    </div>
    <div class="card">
      <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;" class="form-grid-3">
        <div><label class="label">Peso total</label>
          <input id="f-peso" type="number" class="input" value="10" step="0.01" /></div>
        <div><label class="label">Unidad peso</label>
          <select id="f-peso-und" class="input">
            <option value="KGM">KG</option>
            <option value="TNE">Toneladas</option>
          </select></div>
        <div><label class="label">N° bultos</label>
          <input id="f-bultos" type="number" class="input" value="1" min="1" /></div>
      </div>
    </div>
    <div class="card">
      <label class="label">Observaciones (opcional)</label>
      <textarea id="f-obs" class="input" rows="2" placeholder="Comentarios adicionales..."></textarea>
    </div>
    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 1rem; flex-wrap: wrap;">
      <div id="f-pdf-format"></div>
      <button type="submit" id="f-submit" class="btn-primary">
        <i data-lucide="check" class="w-4 h-4"></i> Emitir Guía
      </button>
    </div>
  </form>
  <style>
    @media (min-width: 640px) { .form-grid-2 { grid-template-columns: repeat(2, 1fr) !important; } }
    @media (min-width: 640px) { .form-grid-3 { grid-template-columns: repeat(3, 1fr) !important; } }
    @media (min-width: 640px) { .form-grid-4 { grid-template-columns: repeat(2, 1fr) !important; } }
    @media (min-width: 768px) { .form-grid-4 { grid-template-columns: repeat(4, 1fr) !important; } }
  </style>
</div>
