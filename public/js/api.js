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
    getEmpresa: function () { return request('GET', '/empresa'); },
    listSucursales: function () { return request('GET', '/sucursales'); },
    listSeries: function (params) { return request('GET', '/series' + (params || '')); },
    listClientes: function (buscar) { return request('GET', '/clientes?buscar=' + encodeURIComponent(buscar || '')); },
    buscarDocumento: function (tipo, numero) { return request('GET', '/buscar-documento?tipo=' + tipo + '&numero=' + numero); },
    crearFactura: function (data) { return request('POST', '/facturas', data); },
    listarFacturas: function (query) { return request('GET', '/facturas' + (query || '')); },
    verFactura: function (id) { return request('GET', '/facturas/' + id); },
    crearBoleta: function (data) { return request('POST', '/boletas', data); },
    listarBoletas: function (query) { return request('GET', '/boletas' + (query || '')); },
    verBoleta: function (id) { return request('GET', '/boletas/' + id); },
    crearNotaCredito: function (data) { return request('POST', '/notas-credito', data); },
    listarNotasCredito: function (query) { return request('GET', '/notas-credito' + (query || '')); },
    crearNotaDebito: function (data) { return request('POST', '/notas-debito', data); },
    listarNotasDebito: function (query) { return request('GET', '/notas-debito' + (query || '')); },
    crearGuia: function (data) { return request('POST', '/guias-remision', data); },
    listarGuias: function (query) { return request('GET', '/guias-remision' + (query || '')); },
    crearResumen: function (data) { return request('POST', '/resumenes', data); },
    listarResumenes: function (query) { return request('GET', '/resumenes' + (query || '')); },
    estadoResumen: function (id) { return request('GET', '/resumenes/' + id + '/estado'); },
    descargarPdf: function (tipo, id, format) { return request('GET', '/' + tipo + '/' + id + '/pdf?format=' + (format || 'a4')); },
    descargarXml: function (tipo, id) { return request('GET', '/' + tipo + '/' + id + '/xml'); },
    descargarCdr: function (tipo, id) { return request('GET', '/' + tipo + '/' + id + '/cdr'); },
    panelIndicadores: function () { return request('GET', '/panel/indicadores'); },
    panelDocumentosRecientes: function () { return request('GET', '/panel/documentos-recientes'); },
    panelVentasMensuales: function () { return request('GET', '/panel/ventas-mensuales'); },
    panelEstadoSunat: function () { return request('GET', '/panel/estado-sunat'); },
    panelPorMoneda: function () { return request('GET', '/panel/por-moneda'); },
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
    productosDemo: function () { return request('GET', '/productos-demo'); },
    clientesDemo: function () { return request('GET', '/clientes-demo'); },
  };
})();
