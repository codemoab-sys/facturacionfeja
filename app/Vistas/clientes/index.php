<div>
  <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
    <h1 class="page-title" style="margin-bottom: 0;">
      <i data-lucide="users" class="w-7 h-7"></i> Clientes
    </h1>
    <button id="btn-crear-cliente" class="btn-primary">
      <i data-lucide="plus" class="w-4 h-4"></i> Nuevo cliente
    </button>
  </div>

  <div class="card" style="margin-bottom: 1rem;">
    <div style="display: flex; gap: 0.75rem; align-items: center;">
      <input id="cli-buscar" class="input" placeholder="Buscar por RUC, DNI o raz\u00f3n social..." style="flex: 1;" />
      <button id="cli-btn-buscar" class="btn-secondary">
        <i data-lucide="search" class="w-4 h-4"></i>
      </button>
    </div>
  </div>

  <div class="card">
    <div class="table-wrap">
      <table class="table-std">
        <thead>
          <tr>
            <th>Tipo</th>
            <th>Documento</th>
            <th>Raz\u00f3n Social</th>
            <th>Direcci\u00f3n</th>
            <th>Email</th>
            <th style="text-align: center;">Acciones</th>
          </tr>
        </thead>
        <tbody id="cli-tbody">
          <tr>
            <td colspan="6" style="text-align: center; padding: 2rem; color: rgb(148 163 184);">
              <i data-lucide="loader-2" class="w-5 h-5 icon-spin"></i> Cargando...
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal crear/editar cliente -->
<div id="cli-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none; background: rgb(0 0 0 / 0.5);">
  <div class="bg-white rounded-xl w-full max-w-lg max-h-[90vh] flex flex-col" data-stop="1">
    <div class="p-4 flex items-center justify-between">
      <h2 class="text-lg font-semibold flex items-center gap-2">
        <i data-lucide="users" class="w-5 h-5"></i>
        <span id="cli-modal-title">Nuevo cliente</span>
      </h2>
      <button id="cli-modal-close" style="color: rgb(148 163 184); background: none; border: none; cursor: pointer;">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>
    <div class="flex-1 overflow-auto p-4">
      <form id="cli-form" style="display: flex; flex-direction: column; gap: 1rem;">
        <input type="hidden" id="cli-id" />

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem;">
          <div>
            <label class="label">Tipo doc.</label>
            <select id="cli-tipo-doc" class="input">
              <option value="6">RUC</option>
              <option value="1">DNI</option>
            </select>
          </div>
          <div>
            <label class="label">N\u00famero *</label>
            <input id="cli-num-doc" class="input" required placeholder="Ej: 20555666777" />
          </div>
        </div>

        <div>
          <label class="label">Raz\u00f3n Social *</label>
          <input id="cli-razon" class="input" required placeholder="Nombre o raz\u00f3n social" />
        </div>

        <div>
          <label class="label">Direcci\u00f3n</label>
          <input id="cli-direccion" class="input" placeholder="Direcci\u00f3n fiscal" />
        </div>

        <div>
          <label class="label">Email</label>
          <input id="cli-email" class="input" type="email" placeholder="correo@ejemplo.com" />
        </div>

        <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 0.5rem;">
          <button type="button" id="cli-modal-cancel" class="btn-secondary">Cancelar</button>
          <button type="submit" id="cli-modal-save" class="btn-primary">
            <i data-lucide="save" class="w-4 h-4"></i> Guardar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>