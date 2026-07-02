<?php
declare(strict_types=1);

use App\Nucleo\Contenedor;
use App\Nucleo\Sesion;
use App\Servicios\ServicioAutenticacion;

$router = \App\Nucleo\App::getInstance()->getRouter();

function auth(): void
{
    if (!Sesion::has('user_id')) {
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

function ctrl(string $class): object
{
    return Contenedor::getInstance()->make($class);
}

// ── Auth ──
$router->get('/login', 'AutenticacionControlador@mostrarLogin');
$router->post('/login', 'AutenticacionControlador@iniciarSesion');
$router->get('/logout', 'AutenticacionControlador@cerrarSesion');

// ── API Config ──
$router->get('/api/config', function () {
    auth();
    ctrl(\App\Controladores\ConfiguracionApiControlador::class)->mostrar();
});
$router->post('/api/config', function () {
    auth();
    ctrl(\App\Controladores\ConfiguracionApiControlador::class)->actualizar();
});
$router->post('/api/test-conexion', function () {
    auth();
    ctrl(\App\Controladores\ConfiguracionApiControlador::class)->probarConexion();
});

// ── API Certificado ──
$router->post('/api/certificado', function () {
    auth();
    ctrl(\App\Controladores\CertificadoControlador::class)->subir();
});
$router->delete('/api/certificado', function () {
    auth();
    ctrl(\App\Controladores\CertificadoControlador::class)->eliminar();
});
$router->get('/api/certificado', function () {
    auth();
    ctrl(\App\Controladores\CertificadoControlador::class)->estado();
});

// ── API Logo ──
$router->post('/api/logo', function () {
    auth();
    ctrl(\App\Controladores\LogoControlador::class)->subir();
});
$router->delete('/api/logo', function () {
    auth();
    ctrl(\App\Controladores\LogoControlador::class)->eliminar();
});
$router->get('/api/logo', function () {
    auth();
    ctrl(\App\Controladores\LogoControlador::class)->estado();
});
$router->get('/api/logo-imagen', function () {
    if (Sesion::has('user_id')) auth();
    ctrl(\App\Controladores\LogoControlador::class)->imagen();
});

// ── API endpoints ──
$router->get('/api/empresa', function () { auth(); ctrl(\App\Controladores\PanelControlador::class)->procesar('getEmpresa', []); });
$router->get('/api/sucursales', function () { auth(); ctrl(\App\Controladores\PanelControlador::class)->procesar('listSucursales', []); });
$router->get('/api/series', function () { auth(); ctrl(\App\Controladores\DocumentoControlador::class)->procesarSeries(); });
$router->get('/api/clientes', function () { auth(); ctrl(\App\Controladores\ClienteControlador::class)->listar(); });
$router->get('/api/buscar-documento', function () { auth(); ctrl(\App\Controladores\ClienteControlador::class)->buscarDocumento(); });

$router->post('/api/facturas', function () { auth(); ctrl(\App\Controladores\FacturaControlador::class)->guardar(); });
$router->get('/api/facturas', function () { auth(); ctrl(\App\Controladores\FacturaControlador::class)->index(); });
$router->get('/api/facturas/{id}', function ($p) { auth(); ctrl(\App\Controladores\FacturaControlador::class)->mostrar($p); });

$router->post('/api/boletas', function () { auth(); ctrl(\App\Controladores\BoletaControlador::class)->guardar(); });
$router->get('/api/boletas', function () { auth(); ctrl(\App\Controladores\BoletaControlador::class)->index(); });
$router->get('/api/boletas/{id}', function ($p) { auth(); ctrl(\App\Controladores\BoletaControlador::class)->mostrar($p); });

$router->post('/api/notas-credito', function () { auth(); ctrl(\App\Controladores\NotaCreditoControlador::class)->guardar(); });
$router->get('/api/notas-credito', function () { auth(); ctrl(\App\Controladores\NotaCreditoControlador::class)->index(); });

$router->post('/api/notas-debito', function () { auth(); ctrl(\App\Controladores\NotaDebitoControlador::class)->guardar(); });
$router->get('/api/notas-debito', function () { auth(); ctrl(\App\Controladores\NotaDebitoControlador::class)->index(); });

$router->post('/api/guias-remision', function () { auth(); ctrl(\App\Controladores\GuiaRemisionControlador::class)->guardar(); });
$router->get('/api/guias-remision', function () { auth(); ctrl(\App\Controladores\GuiaRemisionControlador::class)->index(); });

$router->post('/api/resumenes', function () { auth(); ctrl(\App\Controladores\ResumenControlador::class)->guardar(); });
$router->get('/api/resumenes', function () { auth(); ctrl(\App\Controladores\ResumenControlador::class)->indexApi(); });
$router->get('/api/resumenes/{id}/estado', function ($p) { auth(); ctrl(\App\Controladores\ResumenControlador::class)->estado($p); });

$router->get('/api/{tipo}/{id}/pdf', function ($p) { auth(); ctrl(\App\Controladores\DocumentoControlador::class)->descargarPdf($p); });
$router->get('/api/{tipo}/{id}/xml', function ($p) { auth(); ctrl(\App\Controladores\DocumentoControlador::class)->descargarXml($p); });
$router->get('/api/{tipo}/{id}/cdr', function ($p) { auth(); ctrl(\App\Controladores\DocumentoControlador::class)->descargarCdr($p); });

// ── Panel endpoints ──
$router->get('/api/panel/indicadores', function () { auth(); ctrl(\App\Controladores\PanelControlador::class)->procesar('panelIndicadores', []); });
$router->get('/api/panel/documentos-recientes', function () { auth(); ctrl(\App\Controladores\PanelControlador::class)->procesar('panelDocumentosRecientes', []); });
$router->get('/api/panel/ventas-mensuales', function () { auth(); ctrl(\App\Controladores\PanelControlador::class)->procesar('panelVentasMensuales', []); });
$router->get('/api/panel/estado-sunat', function () { auth(); ctrl(\App\Controladores\PanelControlador::class)->procesar('panelEstadoSunat', []); });
$router->get('/api/panel/por-moneda', function () { auth(); ctrl(\App\Controladores\PanelControlador::class)->procesar('panelPorMoneda', []); });

// ── Productos CRUD ──
$router->get('/api/productos', function () { auth(); ctrl(\App\Controladores\ProductoControlador::class)->listar(); });
$router->get('/api/productos/{id}', function ($p) { auth(); ctrl(\App\Controladores\ProductoControlador::class)->obtener([$p]); });
$router->post('/api/productos', function () { auth(); ctrl(\App\Controladores\ProductoControlador::class)->guardar(); });
$router->post('/api/productos/{id}', function ($p) { auth(); ctrl(\App\Controladores\ProductoControlador::class)->guardar([$p]); });
$router->delete('/api/productos/{id}', function ($p) { auth(); ctrl(\App\Controladores\ProductoControlador::class)->eliminar([$p]); });
$router->get('/api/categorias', function () { auth(); ctrl(\App\Controladores\ProductoControlador::class)->listarCategorias(); });

// ── Clientes CRUD ──
$router->get('/api/clientes-local', function () { auth(); ctrl(\App\Controladores\ClienteControlador::class)->listar(); });
$router->get('/api/clientes-local/{id}', function ($p) { auth(); ctrl(\App\Controladores\ClienteControlador::class)->obtener([$p]); });
$router->post('/api/clientes-local', function () { auth(); ctrl(\App\Controladores\ClienteControlador::class)->guardar(); });
$router->post('/api/clientes-local/{id}', function ($p) { auth(); ctrl(\App\Controladores\ClienteControlador::class)->guardar([$p]); });
$router->delete('/api/clientes-local/{id}', function ($p) { auth(); ctrl(\App\Controladores\ClienteControlador::class)->eliminar([$p]); });

// ── Demo data endpoints ──
$router->get('/api/productos-demo', function () { auth(); ctrl(\App\Controladores\ProductoControlador::class)->listarDemo(); });
$router->get('/api/clientes-demo', function () { auth(); ctrl(\App\Controladores\ClienteControlador::class)->listarDemo(); });

// ── Pages (render HTML) ──
$router->get('/', function () { auth(); ctrl(\App\Controladores\PanelControlador::class)->index(); });
$router->get('/nueva-factura', function () { auth(); ctrl(\App\Controladores\FacturaControlador::class)->create(); });
$router->get('/nueva-boleta', function () { auth(); ctrl(\App\Controladores\BoletaControlador::class)->create(); });
$router->get('/nueva-nc', function () { auth(); ctrl(\App\Controladores\NotaCreditoControlador::class)->create(); });
$router->get('/nueva-nd', function () { auth(); ctrl(\App\Controladores\NotaDebitoControlador::class)->create(); });
$router->get('/nueva-guia', function () { auth(); ctrl(\App\Controladores\GuiaRemisionControlador::class)->create(); });
$router->get('/productos', function () { auth(); ctrl(\App\Controladores\ProductoControlador::class)->index(); });
$router->get('/clientes', function () { auth(); ctrl(\App\Controladores\ClienteControlador::class)->index(); });
$router->get('/configuracion', function () { auth(); ctrl(\App\Controladores\ConfiguracionControlador::class)->index(); });
$router->get('/documentos/{tipo}', function ($p) { auth(); ctrl(\App\Controladores\DocumentoControlador::class)->index($p); });
$router->get('/resumenes', function () { auth(); ctrl(\App\Controladores\ResumenControlador::class)->index(); });
