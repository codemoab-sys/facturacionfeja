<div>
  <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
    <h1 class="page-title" style="margin-bottom: 0;">
      <i data-lucide="package" class="w-7 h-7"></i> Productos
    </h1>
    <button id="btn-crear-producto" class="btn-primary">
      <i data-lucide="plus" class="w-4 h-4"></i> Nuevo producto
    </button>
  </div>

  <div class="card" style="margin-bottom: 1rem;">
    <div style="display: flex; gap: 0.75rem; align-items: center;">
      <input id="prod-buscar" class="input" placeholder="Buscar por c&oacute;digo, descripci&oacute;n o categor&iacute;a..." style="flex: 1;" />
      <button id="prod-btn-buscar" class="btn-secondary">
        <i data-lucide="search" class="w-4 h-4"></i>
      </button>
    </div>
  </div>

  <div class="card">
    <div class="table-wrap">
      <table class="table-std">
        <thead>
          <tr>
            <th>C&oacute;digo</th>
            <th>Descripci&oacute;n</th>
            <th>Categor&iacute;a</th>
            <th>Und</th>
            <th style="text-align: right;">Precio</th>
            <th>IGV</th>
            <th style="text-align: center;">Acciones</th>
          </tr>
        </thead>
        <tbody id="prod-tbody">
          <tr>
            <td colspan="7" style="text-align: center; padding: 2rem; color: rgb(148 163 184);">
              <i data-lucide="loader-2" class="w-5 h-5 icon-spin"></i> Cargando...
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal crear/editar producto -->
<div id="prod-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none; background: rgb(0 0 0 / 0.5);">
  <div class="bg-white rounded-xl w-full max-w-2xl max-h-[90vh] flex flex-col" data-stop="1" style="background: white;">
    <div class="p-4 flex items-center justify-between">
      <h2 class="text-lg font-semibold flex items-center gap-2">
        <i data-lucide="package" class="w-5 h-5"></i>
        <span id="prod-modal-title">Nuevo producto</span>
      </h2>
      <button id="prod-modal-close" style="color: rgb(148 163 184); background: none; border: none; cursor: pointer;">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>
    <div class="flex-1 overflow-auto p-4">
      <form id="prod-form" style="display: flex; flex-direction: column; gap: 1rem;">
        <input type="hidden" id="prod-id" />

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          <div>
            <label class="label">C&oacute;digo *</label>
            <input id="prod-codigo" class="input" required placeholder="Ej: P010" />
          </div>
          <div>
            <label class="label">C&oacute;digo SUNAT</label>
            <input id="prod-cod-sunat" class="input" placeholder="Ej: 43211503" />
          </div>
        </div>

        <div>
          <label class="label">Descripci&oacute;n *</label>
          <textarea id="prod-descripcion" class="input" required rows="2" placeholder="Descripci&oacute;n del producto"></textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
          <div>
            <label class="label">Unidad</label>
            <select id="prod-unidad" class="input">
              <option value="NIU">NIU (Unidad)</option>
              <option value="KG">KG (Kilogramo)</option>
              <option value="BG">BG (Bolsa)</option>
              <option value="HUR">HUR (Hora)</option>
              <option value="DAY">DAY (D&iacute;a)</option>
              <option value="MON">MON (Mes)</option>
              <option value="ZZ">ZZ (Servicio)</option>
            </select>
          </div>
          <div>
            <label class="label">Precio unitario *</label>
            <input id="prod-precio" class="input" type="number" step="0.01" min="0" required placeholder="0.00" />
          </div>
          <div>
            <label class="label">Tipo IGV</label>
            <select id="prod-igv" class="input">
              <option value="10">10 - Gravado</option>
              <option value="20">20 - Exonerado</option>
              <option value="30">30 - Inafecto</option>
            </select>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
          <div>
            <label class="label">Categor&iacute;a</label>
            <select id="prod-categoria" class="input">
              <option value="">Sin categor&iacute;a</option>
            </select>
          </div>
          <div>
            <label class="label">ICBPER</label>
            <input id="prod-icbper" class="input" type="number" step="0.01" min="0" placeholder="0.00" />
          </div>
          <div>
            <label class="label">Factor ICBPER</label>
            <input id="prod-factor-icbper" class="input" type="number" step="0.01" min="0" placeholder="0.00" />
          </div>
        </div>

        <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 0.5rem;">
          <button type="button" id="prod-modal-cancel" class="btn-secondary">Cancelar</button>
          <button type="submit" id="prod-modal-save" class="btn-primary">
            <i data-lucide="save" class="w-4 h-4"></i> Guardar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
  @media (min-width: 640px) {
    #prod-modal .bg-white { background: white; }
  }
</style>