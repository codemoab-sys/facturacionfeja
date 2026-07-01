<?php
use App\Core\Session;

$router = \App\Core\App::getInstance()->getRouter();

function auth()
{
    if (!Session::has('user_id')) {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        if (strpos($uri, '/api/') === 0) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'No autenticado']);
        } else {
            header('Location: ' . BASE_PATH . '/login');
        }
        exit;
    }
}

// ── Auth ──
$router->get('/login', 'AuthController@mostrarLogin');
$router->post('/login', 'AuthController@iniciarSesion');
$router->get('/logout', 'AuthController@cerrarSesion');

// ── API (proxied) ──
$router->get('/api/config', function () {
    auth();
    $ctrl = new \App\Controllers\ConfiguracionController();
    $ctrl->mostrar();
});
$router->post('/api/config', function () {
    auth();
    $ctrl = new \App\Controllers\ConfiguracionController();
    $ctrl->actualizar();
});

$router->post('/api/test-conexion', function () {
    auth();
    (new \App\Controllers\ConfiguracionController())->probarConexion();
});
$router->post('/api/certificado', function () {
    auth();
    (new \App\Controllers\ConfiguracionController())->subirCertificado();
});
$router->delete('/api/certificado', function () {
    auth();
    (new \App\Controllers\ConfiguracionController())->eliminarCertificado();
});
$router->get('/api/certificado', function () {
    auth();
    (new \App\Controllers\ConfiguracionController())->estadoCertificado();
});
$router->get('/api/empresa', function () { auth(); (new \App\Controllers\PanelController())->procesar('getEmpresa', []); });
$router->get('/api/sucursales', function () { auth(); (new \App\Controllers\PanelController())->procesar('listSucursales', []); });
$router->get('/api/series', function () { auth(); (new \App\Controllers\DocumentoController())->procesarSeries(); });
$router->get('/api/clientes', function () { auth(); (new \App\Controllers\ClienteController())->listar(); });
$router->get('/api/buscar-documento', function () { auth(); (new \App\Controllers\ClienteController())->buscarDocumento(); });

$router->post('/api/facturas', function () { auth(); (new \App\Controllers\FacturaController())->guardar(); });
$router->get('/api/facturas', function () { auth(); (new \App\Controllers\FacturaController())->index(); });
$router->get('/api/facturas/{id}', function ($p) { auth(); (new \App\Controllers\FacturaController())->mostrar($p); });

$router->post('/api/boletas', function () { auth(); (new \App\Controllers\BoletaController())->guardar(); });
$router->get('/api/boletas', function () { auth(); (new \App\Controllers\BoletaController())->index(); });
$router->get('/api/boletas/{id}', function ($p) { auth(); (new \App\Controllers\BoletaController())->mostrar($p); });

$router->post('/api/notas-credito', function () { auth(); (new \App\Controllers\NotaCreditoController())->guardar(); });
$router->get('/api/notas-credito', function () { auth(); (new \App\Controllers\NotaCreditoController())->index(); });

$router->post('/api/notas-debito', function () { auth(); (new \App\Controllers\NotaDebitoController())->guardar(); });
$router->get('/api/notas-debito', function () { auth(); (new \App\Controllers\NotaDebitoController())->index(); });

$router->post('/api/guias-remision', function () { auth(); (new \App\Controllers\GuiaRemisionController())->guardar(); });
$router->get('/api/guias-remision', function () { auth(); (new \App\Controllers\GuiaRemisionController())->index(); });

$router->post('/api/resumenes', function () { auth(); (new \App\Controllers\ResumenController())->guardar(); });
$router->get('/api/resumenes', function () { auth(); (new \App\Controllers\ResumenController())->indexApi(); });
$router->get('/api/resumenes/{id}/estado', function ($p) { auth(); (new \App\Controllers\ResumenController())->estado($p); });

$router->get('/api/{tipo}/{id}/pdf', function ($p) { auth(); (new \App\Controllers\DocumentoController())->descargarPdf($p); });
$router->get('/api/{tipo}/{id}/xml', function ($p) { auth(); (new \App\Controllers\DocumentoController())->descargarXml($p); });
$router->get('/api/{tipo}/{id}/cdr', function ($p) { auth(); (new \App\Controllers\DocumentoController())->descargarCdr($p); });

// ── Panel endpoints ──
$router->get('/api/panel/indicadores', function () { auth(); (new \App\Controllers\PanelController())->procesar('panelIndicadores', []); });
$router->get('/api/panel/documentos-recientes', function () { auth(); (new \App\Controllers\PanelController())->procesar('panelDocumentosRecientes', []); });
$router->get('/api/panel/ventas-mensuales', function () { auth(); (new \App\Controllers\PanelController())->procesar('panelVentasMensuales', []); });
$router->get('/api/panel/estado-sunat', function () { auth(); (new \App\Controllers\PanelController())->procesar('panelEstadoSunat', []); });
$router->get('/api/panel/por-moneda', function () { auth(); (new \App\Controllers\PanelController())->procesar('panelPorMoneda', []); });

// ── Demo data endpoints ──
$router->get('/api/productos-demo', function () { auth(); (new \App\Controllers\ProductoController())->listar(); });
$router->get('/api/clientes-demo', function () { auth(); (new \App\Controllers\ClienteController())->listarDemo(); });

// ── Pages (render HTML) ──
$router->get('/', function () { auth(); (new \App\Controllers\PanelController())->index(); });
$router->get('/nueva-factura', function () { auth(); (new \App\Controllers\FacturaController())->create(); });
$router->get('/nueva-boleta', function () { auth(); (new \App\Controllers\BoletaController())->create(); });
$router->get('/nueva-nc', function () { auth(); (new \App\Controllers\NotaCreditoController())->create(); });
$router->get('/nueva-nd', function () { auth(); (new \App\Controllers\NotaDebitoController())->create(); });
$router->get('/nueva-guia', function () { auth(); (new \App\Controllers\GuiaRemisionController())->create(); });
$router->get('/configuracion', function () { auth(); (new \App\Controllers\ConfiguracionController())->index(); });
$router->get('/documentos/{tipo}', function ($p) { auth(); (new \App\Controllers\DocumentoController())->index($p); });
$router->get('/resumenes', function () { auth(); (new \App\Controllers\ResumenController())->index(); });
