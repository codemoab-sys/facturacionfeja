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
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

// ── API (proxied) ──
$router->get('/api/config', function () {
    auth();
    $ctrl = new \App\Controllers\ConfigController();
    $ctrl->show();
});
$router->post('/api/config', function () {
    auth();
    $ctrl = new \App\Controllers\ConfigController();
    $ctrl->update();
});

$router->post('/api/test-conexion', function () {
    auth();
    (new \App\Controllers\ConfigController())->testConexion();
});
$router->get('/api/empresa', function () { auth(); (new \App\Controllers\DashboardController())->proxy('getEmpresa', []); });
$router->get('/api/sucursales', function () { auth(); (new \App\Controllers\DashboardController())->proxy('listSucursales', []); });
$router->get('/api/series', function () { auth(); (new \App\Controllers\DocumentController())->proxySeries(); });
$router->get('/api/clientes', function () { auth(); (new \App\Controllers\ClientController())->list(); });
$router->get('/api/buscar-documento', function () { auth(); (new \App\Controllers\ClientController())->buscarDocumento(); });

$router->post('/api/facturas', function () { auth(); (new \App\Controllers\InvoiceController())->store(); });
$router->get('/api/facturas', function () { auth(); (new \App\Controllers\InvoiceController())->index(); });
$router->get('/api/facturas/{id}', function ($p) { auth(); (new \App\Controllers\InvoiceController())->show($p); });

$router->post('/api/boletas', function () { auth(); (new \App\Controllers\BoletaController())->store(); });
$router->get('/api/boletas', function () { auth(); (new \App\Controllers\BoletaController())->index(); });
$router->get('/api/boletas/{id}', function ($p) { auth(); (new \App\Controllers\BoletaController())->show($p); });

$router->post('/api/notas-credito', function () { auth(); (new \App\Controllers\CreditNoteController())->store(); });
$router->get('/api/notas-credito', function () { auth(); (new \App\Controllers\CreditNoteController())->index(); });

$router->post('/api/notas-debito', function () { auth(); (new \App\Controllers\DebitNoteController())->store(); });
$router->get('/api/notas-debito', function () { auth(); (new \App\Controllers\DebitNoteController())->index(); });

$router->post('/api/guias-remision', function () { auth(); (new \App\Controllers\DispatchGuideController())->store(); });
$router->get('/api/guias-remision', function () { auth(); (new \App\Controllers\DispatchGuideController())->index(); });

$router->post('/api/resumenes', function () { auth(); (new \App\Controllers\SummaryController())->store(); });
$router->get('/api/resumenes', function () { auth(); (new \App\Controllers\SummaryController())->index(); });
$router->get('/api/resumenes/{id}/estado', function ($p) { auth(); (new \App\Controllers\SummaryController())->estado($p); });

$router->get('/api/{tipo}/{id}/pdf', function ($p) { auth(); (new \App\Controllers\DocumentController())->downloadPdf($p); });
$router->get('/api/{tipo}/{id}/xml', function ($p) { auth(); (new \App\Controllers\DocumentController())->downloadXml($p); });
$router->get('/api/{tipo}/{id}/cdr', function ($p) { auth(); (new \App\Controllers\DocumentController())->downloadCdr($p); });

// ── Panel endpoints ──
$router->get('/api/panel/indicadores', function () { auth(); (new \App\Controllers\DashboardController())->proxy('panelIndicadores', []); });
$router->get('/api/panel/documentos-recientes', function () { auth(); (new \App\Controllers\DashboardController())->proxy('panelDocumentosRecientes', []); });
$router->get('/api/panel/ventas-mensuales', function () { auth(); (new \App\Controllers\DashboardController())->proxy('panelVentasMensuales', []); });
$router->get('/api/panel/estado-sunat', function () { auth(); (new \App\Controllers\DashboardController())->proxy('panelEstadoSunat', []); });
$router->get('/api/panel/por-moneda', function () { auth(); (new \App\Controllers\DashboardController())->proxy('panelPorMoneda', []); });

// ── Demo data endpoints ──
$router->get('/api/productos-demo', function () { auth(); (new \App\Controllers\ProductController())->list(); });
$router->get('/api/clientes-demo', function () { auth(); (new \App\Controllers\ClientController())->demoList(); });

// ── Pages (render HTML) ──
$router->get('/', 'DashboardController@index');
$router->get('/nueva-factura', 'InvoiceController@create');
$router->get('/nueva-boleta', 'BoletaController@create');
$router->get('/nueva-nc', 'CreditNoteController@create');
$router->get('/nueva-nd', 'DebitNoteController@create');
$router->get('/nueva-guia', 'DispatchGuideController@create');
$router->get('/configuracion', 'ConfigController@index');
$router->get('/documentos/{tipo}', 'DocumentController@index');
$router->get('/resumenes', 'SummaryController@index');
