var App = window.App || (window.App = {});

(function () {
  var BASE = (typeof BASE_PATH !== 'undefined' ? BASE_PATH : '') + '/api';

  async function request(method, path, body) {
    var url = BASE + path;
    var opts = {
      method: method,
      headers: { 'Accept': 'application/json' },
    };

    if (body !== undefined) {
      opts.headers['Content-Type'] = 'application/json';
      opts.body = JSON.stringify(body);
    }

    var response = await fetch(url, opts);
    var contentType = response.headers.get('content-type') || '';

    if (!contentType.includes('application/json')) {
      if (!response.ok) throw new Error('Error ' + response.status + ': ' + response.statusText);
      return await response.blob();
    }

    var json = await response.json();

    if (!response.ok || !json.success) {
      var msg = json.message || ('Error ' + response.status);
      var err = new Error(msg);
      err.status = response.status;
      err.errors = json.errors;
      err.data = json;
      throw err;
    }

    return json;
  }

  App.api = {
    // ── Empresa / sucursales / series ──
    obtenerEmpresa: function () { return request('GET', '/empresa'); },
    listarSucursales: function () { return request('GET', '/sucursales'); },
    listarSeries: function (params) { return request('GET', '/series' + (params || '')); },
    listarClientes: function (buscar) { return request('GET', '/clientes?buscar=' + encodeURIComponent(buscar || '')); },
    buscarDocumento: function (tipo, numero) { return request('GET', '/buscar-documento?tipo=' + tipo + '&numero=' + numero); },

    // ── Facturas ──
    crearFactura: function (data) { return request('POST', '/facturas', data); },
    listarFacturas: function (query) { return request('GET', '/facturas' + (query || '')); },
    verFactura: function (id) { return request('GET', '/facturas/' + id); },

    // ── Boletas ──
    crearBoleta: function (data) { return request('POST', '/boletas', data); },
    listarBoletas: function (query) { return request('GET', '/boletas' + (query || '')); },
    verBoleta: function (id) { return request('GET', '/boletas/' + id); },

    // ── Notas de crédito ──
    crearNotaCredito: function (data) { return request('POST', '/notas-credito', data); },
    listarNotasCredito: function (query) { return request('GET', '/notas-credito' + (query || '')); },

    // ── Notas de débito ──
    crearNotaDebito: function (data) { return request('POST', '/notas-debito', data); },
    listarNotasDebito: function (query) { return request('GET', '/notas-debito' + (query || '')); },

    // ── Guías de remisión ──
    crearGuia: function (data) { return request('POST', '/guias-remision', data); },
    listarGuias: function (query) { return request('GET', '/guias-remision' + (query || '')); },

    // ── Resúmenes ──
    crearResumen: function (data) { return request('POST', '/resumenes', data); },
    listarResumenes: function (query) { return request('GET', '/resumenes' + (query || '')); },
    estadoResumen: function (id) { return request('GET', '/resumenes/' + id + '/estado'); },

    // ── Descargas ──
    descargarPdf: function (tipo, id, format) { return request('GET', '/' + tipo + '/' + id + '/pdf?format=' + (format || 'a4')); },
    descargarXml: function (tipo, id) { return request('GET', '/' + tipo + '/' + id + '/xml'); },
    descargarCdr: function (tipo, id) { return request('GET', '/' + tipo + '/' + id + '/cdr'); },

    // ── Panel ──
    panelIndicadores: function () { return request('GET', '/panel/indicadores'); },
    panelDocumentosRecientes: function () { return request('GET', '/panel/documentos-recientes'); },
    panelVentasMensuales: function () { return request('GET', '/panel/ventas-mensuales'); },
    panelEstadoSunat: function () { return request('GET', '/panel/estado-sunat'); },
    panelPorMoneda: function () { return request('GET', '/panel/por-moneda'); },

    // ── Configuración ──
    guardarConfig: function (data) { return request('POST', '/config', data); },
    obtenerConfig: function () { return request('GET', '/config'); },

    // ── Auth ──
    iniciarSesion: function (usuario, password) {
      return fetch(BASE_PATH + '/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'usuario=' + encodeURIComponent(usuario) + '&password=' + encodeURIComponent(password),
      }).then(function (r) { return r.json(); });
    },
    cerrarSesion: function () { window.location.href = (typeof BASE_PATH !== 'undefined' ? BASE_PATH : '') + '/logout'; },

    probarConexion: function (data) { return request('POST', '/test-conexion', data); },

    // ── Certificado digital ──
    subirCertificado: function (formData) {
      var url = BASE + '/certificado';
      return fetch(url, {
        method: 'POST',
        headers: { 'Accept': 'application/json' },
        body: formData,
      }).then(function (r) { return r.json(); });
    },
    eliminarCertificado: function () { return request('DELETE', '/certificado'); },
    estadoCertificado: function () { return request('GET', '/certificado'); },

    // ── Logo empresa ──
    subirLogo: function (formData) {
      var url = BASE + '/logo';
      return fetch(url, {
        method: 'POST',
        headers: { 'Accept': 'application/json' },
        body: formData,
      }).then(function (r) { return r.json(); });
    },
    eliminarLogo: function () { return request('DELETE', '/logo'); },
    estadoLogo: function () { return request('GET', '/logo'); },

    // ── Demo ──
    listarProductosDemo: function () { return request('GET', '/productos-demo'); },
    listarProductosDemo: function () { return request('GET', '/productos-demo'); },
    listarClientesDemo: function () { return request('GET', '/clientes-demo'); },

    // ── Inventario ──
    inventarioProductos: function (buscar) { return request('GET', '/inventario/productos' + (buscar ? '?buscar=' + encodeURIComponent(buscar) : '')); },
    inventarioMovimientos: function (filtros) {
      var q = [];
      if (filtros) { for (var k in filtros) { if (filtros[k]) q.push(k + '=' + encodeURIComponent(filtros[k])); } }
      return request('GET', '/inventario/movimientos' + (q.length ? '?' + q.join('&') : ''));
    },
    inventarioRegistrarMovimiento: function (data) { return request('POST', '/inventario/movimiento', data); },
    inventarioProductoDetalle: function (id) { return request('GET', '/inventario/productos/' + id); },
    inventarioStockBajo: function () { return request('GET', '/inventario/stock-bajo'); },

    // ── Compras ──
    listarCompras: function (buscar) { return request('GET', '/compras' + (buscar ? '?buscar=' + encodeURIComponent(buscar) : '')); },
    obtenerCompra: function (id) { return request('GET', '/compras/' + id); },
    crearCompra: function (data) { return request('POST', '/compras', data); },
    eliminarCompra: function (id) { return request('DELETE', '/compras/' + id); },

    // ── Aliases para compatibilidad ──
    getEmpresa: function () { return request('GET', '/empresa'); },
    listSucursales: function () { return request('GET', '/sucursales'); },
    listSeries: function (params) { return request('GET', '/series' + (params || '')); },
    listClientes: function (buscar) { return request('GET', '/clientes?buscar=' + encodeURIComponent(buscar || '')); },
    configGuardar: function (data) { return request('POST', '/config', data); },
    configObtener: function () { return request('GET', '/config'); },
    login: function (usuario, password) {
      return fetch(BASE_PATH + '/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'usuario=' + encodeURIComponent(usuario) + '&password=' + encodeURIComponent(password),
      }).then(function (r) { return r.json(); });
    },
    logout: function () { window.location.href = (typeof BASE_PATH !== 'undefined' ? BASE_PATH : '') + '/logout'; },
    testConexion: function (data) { return request('POST', '/test-conexion', data); },
    // ── Productos ──
    listarProductos: function (buscar) { return request('GET', '/productos' + (buscar ? '?buscar=' + encodeURIComponent(buscar) : '')); },
    obtenerProducto: function (id) { return request('GET', '/productos/' + id); },
    guardarProducto: function (data, id) { return request('POST', id ? '/productos/' + id : '/productos', data); },
    eliminarProducto: function (id) { return request('DELETE', '/productos/' + id); },
    listarCategorias: function () { return request('GET', '/categorias'); },
    // ── Clientes ──
    listarClientesLocal: function (buscar) { return request('GET', '/clientes-local' + (buscar ? '?buscar=' + encodeURIComponent(buscar) : '')); },
    obtenerCliente: function (id) { return request('GET', '/clientes-local/' + id); },
    guardarCliente: function (data, id) { return request('POST', id ? '/clientes-local/' + id : '/clientes-local', data); },
    eliminarCliente: function (id) { return request('DELETE', '/clientes-local/' + id); },

  };
})();
