<div>
  <h1 class="page-title">
    <i data-lucide="layout-dashboard" class="w-7 h-7"></i> Inicio
  </h1>
  <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; margin-bottom: 1.5rem;" class="sm-grid-3 md-grid-5">
    <a data-link="/nueva-factura" style="background: rgb(59 130 246); color: white; padding: 1rem; border-radius: 1rem; text-align: center; box-shadow: 0 1px 2px rgb(15 23 42 / 0.05); cursor: pointer; transition: transform 0.15s; text-decoration: none; display: block;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
      <i data-lucide="file-text" style="width: 2rem; height: 2rem; margin: 0 auto 0.5rem;"></i>
      <div style="font-size: 0.75rem; font-weight: 700; letter-spacing: -0.015em;">Nueva Factura</div>
    </a>
    <a data-link="/nueva-boleta" style="background: rgb(99 102 241); color: white; padding: 1rem; border-radius: 1rem; text-align: center; box-shadow: 0 1px 2px rgb(15 23 42 / 0.05); cursor: pointer; transition: transform 0.15s; text-decoration: none; display: block;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
      <i data-lucide="receipt" style="width: 2rem; height: 2rem; margin: 0 auto 0.5rem;"></i>
      <div style="font-size: 0.75rem; font-weight: 700; letter-spacing: -0.015em;">Nueva Boleta</div>
    </a>
    <a data-link="/nueva-nc" style="background: rgb(245 158 11); color: white; padding: 1rem; border-radius: 1rem; text-align: center; box-shadow: 0 1px 2px rgb(15 23 42 / 0.05); cursor: pointer; transition: transform 0.15s; text-decoration: none; display: block;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
      <i data-lucide="trending-down" style="width: 2rem; height: 2rem; margin: 0 auto 0.5rem;"></i>
      <div style="font-size: 0.75rem; font-weight: 700; letter-spacing: -0.015em;">Nota Crédito</div>
    </a>
    <a data-link="/nueva-nd" style="background: rgb(249 115 22); color: white; padding: 1rem; border-radius: 1rem; text-align: center; box-shadow: 0 1px 2px rgb(15 23 42 / 0.05); cursor: pointer; transition: transform 0.15s; text-decoration: none; display: block;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
      <i data-lucide="trending-up" style="width: 2rem; height: 2rem; margin: 0 auto 0.5rem;"></i>
      <div style="font-size: 0.75rem; font-weight: 700; letter-spacing: -0.015em;">Nota Débito</div>
    </a>
    <a data-link="/nueva-guia" style="background: rgb(34 197 94); color: white; padding: 1rem; border-radius: 1rem; text-align: center; box-shadow: 0 1px 2px rgb(15 23 42 / 0.05); cursor: pointer; transition: transform 0.15s; text-decoration: none; display: block;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
      <i data-lucide="truck" style="width: 2rem; height: 2rem; margin: 0 auto 0.5rem;"></i>
      <div style="font-size: 0.75rem; font-weight: 700; letter-spacing: -0.015em;">Guía Remisión</div>
    </a>
  </div>
  <style>
    @media (min-width: 640px) { .sm-grid-3 { grid-template-columns: repeat(3, 1fr) !important; } }
    @media (min-width: 768px) { .md-grid-5 { grid-template-columns: repeat(5, 1fr) !important; } }
  </style>
  <div id="dashboard-content">
    <div style="text-align: center; padding: 2rem 0; color: rgb(148 163 184); display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
      <i data-lucide="loader-2" class="w-5 h-5 icon-spin"></i> Cargando...
    </div>
  </div>
</div>
