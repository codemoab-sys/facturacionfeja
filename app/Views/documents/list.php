<div>
  <h1 class="page-title">
    <i data-lucide="clipboard-list" class="w-7 h-7" id="dl-title-icon"></i> <span id="dl-title-text">Documentos</span>
  </h1>
  <div class="card" style="margin-bottom: 1rem;">
    <div style="display: flex; flex-direction: column; gap: 0.75rem;" class="filter-row">
      <div style="flex: 1;">
        <label class="label">Buscar</label>
        <input id="dl-buscar" class="input" placeholder="Serie, correlativo, cliente..." />
      </div>
      <div>
        <label class="label">Estado SUNAT</label>
        <select id="dl-estado" class="input">
          <option value="">Todos</option>
          <option value="pendiente">Pendiente</option>
          <option value="enviado">Enviado</option>
          <option value="aceptado">Aceptado</option>
          <option value="rechazado">Rechazado</option>
          <option value="anulado">Anulado</option>
        </select>
      </div>
      <button id="dl-filtrar" class="btn-primary"><i data-lucide="search" class="w-4 h-4"></i> Filtrar</button>
    </div>
    <style>@media (min-width: 640px) { .filter-row { flex-direction: row !important; align-items: flex-end !important; } }</style>
  </div>
  <div class="card" id="dl-body">
    <div style="text-align: center; padding: 2rem 0; color: rgb(148 163 184); display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
      <i data-lucide="loader-2" class="w-5 h-5 icon-spin"></i> Cargando...
    </div>
  </div>
  <input type="hidden" id="dl-tipo" value="<?php echo htmlspecialchars($tipo ?? 'facturas'); ?>" />
</div>
