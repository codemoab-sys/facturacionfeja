<?php
declare(strict_types=1);

use App\Nucleo\Sesion;

$router = \App\Nucleo\App::getInstance()->getRouter();

// ── API Config ──
$router->get('/api/config', function () { auth(); ctrl(\App\Controladores\ConfiguracionApiControlador::class)->mostrar(); });
$router->post('/api/config', function () { auth(); ctrl(\App\Controladores\ConfiguracionApiControlador::class)->actualizar(); });
$router->post('/api/test-conexion', function () { auth(); ctrl(\App\Controladores\ConfiguracionApiControlador::class)->probarConexion(); });

// ── API Certificado ──
$router->post('/api/certificado', function () { auth(); ctrl(\App\Controladores\CertificadoControlador::class)->subir(); });
$router->delete('/api/certificado', function () { auth(); ctrl(\App\Controladores\CertificadoControlador::class)->eliminar(); });
$router->get('/api/certificado', function () { auth(); ctrl(\App\Controladores\CertificadoControlador::class)->estado(); });

// ── API Logo ──
$router->post('/api/logo', function () { auth(); ctrl(\App\Controladores\LogoControlador::class)->subir(); });
$router->delete('/api/logo', function () { auth(); ctrl(\App\Controladores\LogoControlador::class)->eliminar(); });
$router->get('/api/logo', function () { auth(); ctrl(\App\Controladores\LogoControlador::class)->estado(); });
$router->get('/api/logo-imagen', function () {
    if (Sesion::has('user_id')) auth();
    ctrl(\App\Controladores\LogoControlador::class)->imagen();
});

// ── API SUNAT proxy endpoints ──
$router->get('/api/empresa', function () { auth(); ctrl(\App\Controladores\PanelControlador::class)->procesar('getEmpresa', []); });
$router->get('/api/sucursales', function () { auth(); ctrl(\App\Controladores\PanelControlador::class)->procesar('listSucursales', []); });
$router->get('/api/series', function () { auth(); ctrl(\App\Controladores\DocumentoControlador::class)->procesarSeries(); });
$router->get('/api/clientes', function () { auth(); ctrl(\App\Controladores\ClienteControlador::class)->listar(); });
$router->get('/api/buscar-documento', function () { auth(); ctrl(\App\Controladores\ClienteControlador::class)->buscarDocumento(); });

// ── API Facturas ──
$router->post('/api/facturas', function () { auth(); ctrl(\App\Controladores\FacturaControlador::class)->guardar(); });
$router->get('/api/facturas', function () { auth(); ctrl(\App\Controladores\FacturaControlador::class)->index(); });
$router->get('/api/facturas/{id}', function ($p) { auth(); ctrl(\App\Controladores\FacturaControlador::class)->mostrar($p); });

// ── API Boletas ──
$router->post('/api/boletas', function () { auth(); ctrl(\App\Controladores\BoletaControlador::class)->guardar(); });
$router->get('/api/boletas', function () { auth(); ctrl(\App\Controladores\BoletaControlador::class)->index(); });
$router->get('/api/boletas/{id}', function ($p) { auth(); ctrl(\App\Controladores\BoletaControlador::class)->mostrar($p); });

// ── API Notas Crédito ──
$router->post('/api/notas-credito', function () { auth(); ctrl(\App\Controladores\NotaCreditoControlador::class)->guardar(); });
$router->get('/api/notas-credito', function () { auth(); ctrl(\App\Controladores\NotaCreditoControlador::class)->index(); });

// ── API Notas Débito ──
$router->post('/api/notas-debito', function () { auth(); ctrl(\App\Controladores\NotaDebitoControlador::class)->guardar(); });
$router->get('/api/notas-debito', function () { auth(); ctrl(\App\Controladores\NotaDebitoControlador::class)->index(); });

// ── API Guías Remisión ──
$router->post('/api/guias-remision', function () { auth(); ctrl(\App\Controladores\GuiaRemisionControlador::class)->guardar(); });
$router->get('/api/guias-remision', function () { auth(); ctrl(\App\Controladores\GuiaRemisionControlador::class)->index(); });

// ── API Resúmenes ──
$router->post('/api/resumenes', function () { auth(); ctrl(\App\Controladores\ResumenControlador::class)->guardar(); });
$router->get('/api/resumenes', function () { auth(); ctrl(\App\Controladores\ResumenControlador::class)->indexApi(); });
$router->get('/api/resumenes/{id}/estado', function ($p) { auth(); ctrl(\App\Controladores\ResumenControlador::class)->estado($p); });

// ── API Descargas ──
$router->get('/api/{tipo}/{id}/pdf', function ($p) { auth(); ctrl(\App\Controladores\DocumentoControlador::class)->descargarPdf($p); });
$router->get('/api/{tipo}/{id}/xml', function ($p) { auth(); ctrl(\App\Controladores\DocumentoControlador::class)->descargarXml($p); });
$router->get('/api/{tipo}/{id}/cdr', function ($p) { auth(); ctrl(\App\Controladores\DocumentoControlador::class)->descargarCdr($p); });

// ── API Panel ──
$router->get('/api/panel/indicadores', function () { auth(); ctrl(\App\Controladores\PanelControlador::class)->procesar('panelIndicadores', []); });
$router->get('/api/panel/documentos-recientes', function () { auth(); ctrl(\App\Controladores\PanelControlador::class)->procesar('panelDocumentosRecientes', []); });
$router->get('/api/panel/ventas-mensuales', function () { auth(); ctrl(\App\Controladores\PanelControlador::class)->procesar('panelVentasMensuales', []); });
$router->get('/api/panel/estado-sunat', function () { auth(); ctrl(\App\Controladores\PanelControlador::class)->procesar('panelEstadoSunat', []); });
$router->get('/api/panel/por-moneda', function () { auth(); ctrl(\App\Controladores\PanelControlador::class)->procesar('panelPorMoneda', []); });

// ── API Productos CRUD ──
$router->get('/api/productos', function () { auth(); ctrl(\App\Api\Controladores\ProductoApiControlador::class)->listar(); });
$router->get('/api/productos/{id}', function ($p) { auth(); ctrl(\App\Api\Controladores\ProductoApiControlador::class)->obtener($p); });
$router->post('/api/productos', function () { auth(); ctrl(\App\Api\Controladores\ProductoApiControlador::class)->guardar([]); });
$router->post('/api/productos/{id}', function ($p) { auth(); ctrl(\App\Api\Controladores\ProductoApiControlador::class)->guardar($p); });
$router->delete('/api/productos/{id}', function ($p) { auth(); ctrl(\App\Api\Controladores\ProductoApiControlador::class)->eliminar($p); });
$router->get('/api/categorias', function () { auth(); ctrl(\App\Api\Controladores\ProductoApiControlador::class)->listarCategorias(); });

// ── API Clientes CRUD ──
$router->get('/api/clientes-local', function () { auth(); ctrl(\App\Api\Controladores\ClienteApiControlador::class)->listar(); });
$router->get('/api/clientes-local/{id}', function ($p) { auth(); ctrl(\App\Api\Controladores\ClienteApiControlador::class)->obtener($p); });
$router->post('/api/clientes-local', function () { auth(); ctrl(\App\Api\Controladores\ClienteApiControlador::class)->guardar([]); });
$router->post('/api/clientes-local/{id}', function ($p) { auth(); ctrl(\App\Api\Controladores\ClienteApiControlador::class)->guardar($p); });
$router->delete('/api/clientes-local/{id}', function ($p) { auth(); ctrl(\App\Api\Controladores\ClienteApiControlador::class)->eliminar($p); });

// ── API Inventario ──
$router->get('/api/inventario/productos', function () { auth(); ctrl(\App\Api\Controladores\InventarioApiControlador::class)->listarProductos(); });
$router->get('/api/inventario/movimientos', function () { auth(); ctrl(\App\Api\Controladores\InventarioApiControlador::class)->listarMovimientos(); });
$router->post('/api/inventario/movimiento', function () { auth(); ctrl(\App\Api\Controladores\InventarioApiControlador::class)->registrarMovimiento(); });

// ── API Compras ──
$router->get('/api/compras', function () { auth(); ctrl(\App\Api\Controladores\CompraApiControlador::class)->listar(); });
$router->get('/api/compras/{id}', function ($p) { auth(); ctrl(\App\Api\Controladores\CompraApiControlador::class)->obtener($p); });
$router->post('/api/compras', function () { auth(); ctrl(\App\Api\Controladores\CompraApiControlador::class)->guardar(); });
$router->delete('/api/compras/{id}', function ($p) { auth(); ctrl(\App\Api\Controladores\CompraApiControlador::class)->eliminar($p); });


