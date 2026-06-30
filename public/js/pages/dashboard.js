var App = window.App || (window.App = {});

(function () {
  var COLORS_ESTADO = {
    pendiente: '#eab308',
    enviado:   '#3b82f6',
    aceptado:  '#22c55e',
    rechazado: '#ef4444',
    anulado:   '#94a3b8',
  };
  var COLORS_MONEDA = ['#2563eb', '#f59e0b', '#8b5cf6', '#06b6d4'];
  var TIPO_LABEL = {
    factura: 'Factura',
    boleta: 'Boleta',
    nota_credito: 'Nota Cr\u00e9dito',
    nota_debito: 'Nota D\u00e9bito',
  };

  App.Dashboard = class Dashboard {
    constructor() {
      this.indicadores = null;
      this.recientes = [];
      this.ventasMes = null;
      this.estadoSunat = null;
      this.porMoneda = null;
      this.loading = true;
      this.error = null;
      this.container = null;
      this.charts = {};
    }

    async render(container) {
      this.container = container;
      this._renderLoading();
      try {
        var results = await Promise.all([
          App.api.panelIndicadores().catch(function () { return null; }),
          App.api.panelDocumentosRecientes().catch(function () { return null; }),
          App.api.panelVentasMensuales().catch(function () { return null; }),
          App.api.panelEstadoSunat().catch(function () { return null; }),
          App.api.panelPorMoneda().catch(function () { return null; }),
        ]);
        if (results[0] && results[0].data) this.indicadores = results[0].data;
        if (results[1] && results[1].data && results[1].data.documentos) this.recientes = results[1].data.documentos;
        if (results[2] && results[2].data) this.ventasMes = results[2].data;
        if (results[3] && results[3].data) this.estadoSunat = results[3].data;
        if (results[4] && results[4].data) this.porMoneda = results[4].data;
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
        this._renderContent();
        this._renderCharts();
      }
    }

    _renderLoading() {
      var content = this.container.querySelector('#dashboard-content');
      if (content) {
        content.innerHTML = '<div style="text-align: center; padding: 2rem 0; color: rgb(148 163 184); display: flex; align-items: center; justify-content: center; gap: 0.5rem;">'
          + '<i data-lucide="loader-2" class="w-5 h-5 icon-spin"></i> Cargando...</div>';
        App.refreshIcons();
      }
    }

    _renderContent() {
      var content = this.container.querySelector('#dashboard-content');
      if (!content) return;
      if (this.error) {
        content.innerHTML = '<div style="padding: 1rem; background: rgb(254 242 242); border-radius: 0.5rem; color: rgb(153 27 27);">'
          + '<div style="display: flex; align-items: center; gap: 0.5rem;">'
          + '<i data-lucide="x-circle" class="w-5 h-5"></i> ' + App.escapeHtml(this.error) + '</div>'
          + '<div style="font-size: 0.875rem; margin-top: 0.25rem;">Verifica tu <a href="/configuracion" style="text-decoration: underline; cursor: pointer;">configuraci\u00f3n</a>.</div>'
          + '</div>';
        App.refreshIcons();
        return;
      }
      var html = '';
      if (this.indicadores) html += this._kpisHTML();
      html += this._barChartHTML();
      html += this._donutsHTML();
      if (this.recientes.length > 0) html += this._recientesHTML();
      content.innerHTML = html + '<style>'
        + '@media (min-width: 768px) { .kpi-grid-4 { grid-template-columns: repeat(4, 1fr) !important; } }'
        + '@media (min-width: 768px) { .donuts-grid-2 { grid-template-columns: repeat(2, 1fr) !important; } }'
        + '</style>';
      this._bind();
      App.refreshIcons();
    }

    _kpisHTML() {
      var i = this.indicadores;
      return '<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; margin-bottom: 1.5rem;" class="kpi-grid-4">'
        + this._kpiCardHTML('Ventas hoy', i.hoy && i.hoy.ventas, i.hoy && i.hoy.documentos)
        + this._kpiCardHTML('Esta semana', i.semana && i.semana.ventas, i.semana && i.semana.documentos)
        + this._kpiCardHTML('Mes actual', i.mes_actual && i.mes_actual.ventas, i.mes_actual && i.mes_actual.documentos)
        + this._kpiCardHTML('Vs mes anterior', i.crecimiento && i.crecimiento.vs_mes_anterior, undefined, { isGrowth: true, suffix: '%' })
        + '</div>';
    }

    _kpiCardHTML(label, value, cantidad, opts) {
      opts = opts || {};
      var isGrowth = !!opts.isGrowth;
      var suffix = opts.suffix || '';
      var n = (value !== undefined && value !== null) ? parseFloat(value) : null;
      var formatted = n !== null ? App.fmtNumber(n) : '0.00';
      var positive = isGrowth && (n || 0) > 0;
      var negative = isGrowth && (n || 0) < 0;
      var prefix = isGrowth ? '' : 'S/ ';
      var valueColor = positive ? 'rgb(22 163 74)' : negative ? 'rgb(220 38 38)' : 'rgb(15 23 42)';
      return '<div class="card">'
        + '<div style="font-size: 10px; text-transform: uppercase; color: rgb(100 116 139); font-weight: 700; letter-spacing: 0.1em;">' + App.escapeHtml(label) + '</div>'
        + '<div style="font-size: 1.25rem; font-weight: 800; margin-top: 0.25rem; letter-spacing: -0.015em; color: ' + valueColor + ';">'
        + (isGrowth && positive ? '+' : '') + prefix + formatted + suffix + '</div>'
        + (cantidad !== undefined ? '<div style="font-size: 0.75rem; color: rgb(100 116 139); margin-top: 0.25rem; font-weight: 500;">' + (cantidad || 0) + ' docs</div>' : '')
        + '</div>';
    }

    _barChartHTML() {
      var total = this.ventasMes && this.ventasMes.total_12_meses;
      return '<div class="card" style="margin-bottom: 1.5rem;">'
        + '<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">'
          + '<h2 class="section-title" style="margin-bottom: 0;">'
            + '<i data-lucide="bar-chart-3" class="w-5 h-5" style="color: rgb(37 99 235);"></i> Ventas \u00faltimos 12 meses'
          + '</h2>'
          + (total !== undefined
            ? '<div style="text-align: right;">'
              + '<div style="font-size: 0.625rem; font-weight: 700; color: rgb(100 116 139); text-transform: uppercase; letter-spacing: 0.05em;">Total</div>'
              + '<div style="font-size: 1.125rem; font-weight: 800; color: rgb(15 23 42);">' + App.fmtMoney(total) + '</div>'
            + '</div>'
            : '')
        + '</div>'
        + '<div style="position: relative; height: 280px;"><canvas id="chart-bar"></canvas></div>'
      + '</div>';
    }

    _donutsHTML() {
      var tasa = this.estadoSunat && this.estadoSunat.tasa_aceptacion;
      return '<div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 1.5rem;" class="donuts-grid-2">'
        + '<div class="card">'
          + '<h2 class="section-title">'
            + '<i data-lucide="pie-chart" class="w-5 h-5" style="color: rgb(22 163 74);"></i> Estado SUNAT (mes)'
          + '</h2>'
          + '<div style="position: relative; height: 220px;"><canvas id="chart-estado"></canvas></div>'
          + (tasa !== undefined
            ? '<div style="text-align: center; margin-top: 0.75rem; font-size: 0.875rem;">'
              + '<span style="color: rgb(100 116 139);">Tasa aceptaci\u00f3n: </span>'
              + '<span style="font-weight: 800; color: rgb(22 163 74);">' + tasa + '%</span>'
            + '</div>'
            : '')
        + '</div>'
        + '<div class="card">'
          + '<h2 class="section-title">'
            + '<i data-lucide="coins" class="w-5 h-5" style="color: rgb(217 119 6);"></i> Ventas por moneda (mes)'
          + '</h2>'
          + '<div style="position: relative; height: 220px;"><canvas id="chart-moneda"></canvas></div>'
        + '</div>'
      + '</div>';
    }

    _recientesHTML() {
      var rows = this.recientes.slice(0, 10).map(function (d) {
        return '<tr>'
          + '<td>' + App.escapeHtml(TIPO_LABEL[d.tipo] || d.tipo || '') + '</td>'
          + '<td class="font-mono">' + App.escapeHtml(d.numero || '') + '</td>'
          + '<td style="max-width: 20rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">' + App.escapeHtml(d.cliente || '') + '</td>'
          + '<td style="text-align: right;">' + App.fmtMoney(d.total || 0, d.moneda) + '</td>'
          + '<td>' + App.estadoBadgeHTML(d.estado_sunat) + '</td>'
          + '</tr>';
      }).join('');
      return '<div class="card">'
        + '<h2 class="section-title">'
          + '<i data-lucide="clipboard-list" class="w-5 h-5"></i> Documentos recientes'
        + '</h2>'
        + '<div class="table-wrap">'
          + '<table class="table-std">'
            + '<thead><tr><th>Tipo</th><th>N\u00famero</th><th>Cliente</th>'
            + '<th style="text-align: right;">Total</th><th>Estado</th></tr></thead>'
            + '<tbody>' + rows + '</tbody>'
          + '</table>'
        + '</div>'
        + '</div>';
    }

    _renderCharts() {
      if (!window.Chart) return;
      this._destroyCharts();
      if (this.ventasMes && this.ventasMes.meses) {
        var labels = this.ventasMes.meses.map(function (m) { return (m.mes_label || '').split(' ')[0].substring(0, 3); });
        var data = this.ventasMes.meses.map(function (m) { return m.ventas; });
        var ctx = document.getElementById('chart-bar');
        if (ctx) {
          var grad = ctx.getContext('2d').createLinearGradient(0, 0, 0, 260);
          grad.addColorStop(0, 'rgb(59 130 246 / 1)');
          grad.addColorStop(1, 'rgb(59 130 246 / 0.4)');
          this.charts.bar = new Chart(ctx, {
            type: 'bar',
            data: { labels: labels, datasets: [{ data: data, backgroundColor: grad, borderRadius: 8, borderSkipped: false }] },
            options: {
              responsive: true, maintainAspectRatio: false,
              plugins: { legend: { display: false }, tooltip: { backgroundColor: 'white', titleColor: 'rgb(15 23 42)', bodyColor: 'rgb(51 65 85)', borderWidth: 0, padding: 12, cornerRadius: 10, titleFont: { weight: '700', size: 12 }, bodyFont: { size: 12 }, displayColors: false, callbacks: { label: function (ctx) { return App.fmtMoney(ctx.parsed.y); } } } },
              scales: { x: { grid: { display: false }, ticks: { color: 'rgb(100 116 139)', font: { weight: '600', size: 11 } }, border: { display: false } }, y: { grid: { color: 'rgb(226 232 240)', borderDash: [3, 3] }, ticks: { color: 'rgb(100 116 139)', font: { size: 11 }, callback: function (v) { return v >= 1000 ? (v / 1000).toFixed(0) + 'K' : v; } }, border: { display: false } } },
            },
          });
        }
      }
      if (this.estadoSunat && this.estadoSunat.por_estado) {
        var entries = Object.entries(this.estadoSunat.por_estado).filter(function (e) { return e[1] > 0; });
        if (entries.length > 0) {
          var ctx2 = document.getElementById('chart-estado');
          if (ctx2) {
            this.charts.estado = new Chart(ctx2, {
              type: 'doughnut',
              data: { labels: entries.map(function (e) { return e[0]; }), datasets: [{ data: entries.map(function (e) { return e[1]; }), backgroundColor: entries.map(function (e) { return COLORS_ESTADO[e[0]] || '#94a3b8'; }), borderWidth: 3, borderColor: 'white', hoverOffset: 6 }] },
              options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { color: 'rgb(51 65 85)', font: { size: 11, weight: '600' }, usePointStyle: true, boxWidth: 8, padding: 10 } }, tooltip: { backgroundColor: 'white', titleColor: 'rgb(15 23 42)', bodyColor: 'rgb(51 65 85)', borderWidth: 0, padding: 10, cornerRadius: 8, callbacks: { label: function (ctx) { return ' ' + ctx.label + ': ' + ctx.parsed + ' docs'; } } } } },
            });
          }
        }
      }
      if (this.porMoneda && this.porMoneda.monedas && this.porMoneda.monedas.length > 0) {
        var ctx3 = document.getElementById('chart-moneda');
        if (ctx3) {
          this.charts.moneda = new Chart(ctx3, {
            type: 'doughnut',
            data: { labels: this.porMoneda.monedas.map(function (m) { return m.moneda; }), datasets: [{ data: this.porMoneda.monedas.map(function (m) { return m.total; }), backgroundColor: this.porMoneda.monedas.map(function (_, i) { return COLORS_MONEDA[i % COLORS_MONEDA.length]; }), borderWidth: 3, borderColor: 'white', hoverOffset: 6 }] },
            options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { color: 'rgb(51 65 85)', font: { size: 11, weight: '700' }, usePointStyle: true, boxWidth: 8, padding: 10 } }, tooltip: { backgroundColor: 'white', titleColor: 'rgb(15 23 42)', bodyColor: 'rgb(51 65 85)', borderWidth: 0, padding: 10, cornerRadius: 8, callbacks: { label: function (ctx) { return ' ' + ctx.label + ': ' + App.fmtNumber(ctx.parsed); } } } } },
          });
        }
      }
    }

    _destroyCharts() {
      Object.values(this.charts).forEach(function (c) { if (c) c.destroy(); });
      this.charts = {};
    }

    _bind() {
      var self = this;
      this.container.querySelectorAll('[data-link]').forEach(function (el) {
        el.addEventListener('click', function (e) {
          e.preventDefault();
          window.location.href = el.dataset.link;
        });
      });
    }
  };
})();
