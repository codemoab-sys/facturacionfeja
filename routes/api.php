<?php
declare(strict_types=1);

$router = \App\Framework\App::getInstance()->getRouter();

use App\Framework\Session;
use App\Framework\Database\Connection;

// ── API Health ──
$router->get('/api/health', function () { echo json_encode(Connection::test()); exit; });

// ── API Config ──
$router->get('/api/config', function () { auth(); ctrl(\App\Modules\Settings\Controllers\SettingsApiController::class)->mostrar(); });
$router->post('/api/config', function () { auth(); ctrl(\App\Modules\Settings\Controllers\SettingsApiController::class)->actualizar(); });
$router->post('/api/test-conexion', function () { auth(); ctrl(\App\Modules\Settings\Controllers\SettingsApiController::class)->probarConexion(); });

// ── API Certificado ──
$router->post('/api/certificado', function () { auth(); ctrl(\App\Modules\Settings\Controllers\CertificateController::class)->subir(); });
$router->delete('/api/certificado', function () { auth(); ctrl(\App\Modules\Settings\Controllers\CertificateController::class)->eliminar(); });
$router->get('/api/certificado', function () { auth(); ctrl(\App\Modules\Settings\Controllers\CertificateController::class)->estado(); });

// ── API Logo ──
$router->post('/api/logo', function () { auth(); ctrl(\App\Modules\Settings\Controllers\LogoController::class)->subir(); });
$router->delete('/api/logo', function () { auth(); ctrl(\App\Modules\Settings\Controllers\LogoController::class)->eliminar(); });
$router->get('/api/logo', function () { auth(); ctrl(\App\Modules\Settings\Controllers\LogoController::class)->estado(); });
$router->get('/api/logo-imagen', function () {
    if (Session::has('user_id')) auth();
    ctrl(\App\Modules\Settings\Controllers\LogoController::class)->imagen();
});

// ── API SUNAT proxy endpoints ──
$router->get('/api/empresa', function () { auth(); ctrl(\App\Modules\Dashboard\Controllers\DashboardController::class)->procesar('getEmpresa', []); });
$router->get('/api/sucursales', function () { auth(); ctrl(\App\Modules\Dashboard\Controllers\DashboardController::class)->procesar('listSucursales', []); });
$router->get('/api/series', function () { auth(); ctrl(\App\Modules\Documents\Controllers\DocumentListController::class)->procesarSeries(); });
$router->get('/api/clientes', function () { auth(); ctrl(\App\Modules\Clients\Controllers\ClientApiController::class)->listar(); });
$router->get('/api/buscar-documento', function () { auth(); ctrl(\App\Modules\Clients\Controllers\ClientApiController::class)->buscarDocumento(); });

// ── API Facturas ──
$router->post('/api/facturas', function () { auth(); ctrl(\App\Modules\Documents\Controllers\InvoiceController::class)->guardar(); });
$router->get('/api/facturas', function () { auth(); ctrl(\App\Modules\Documents\Controllers\InvoiceController::class)->index(); });
$router->get('/api/facturas/{id}', function ($p) { auth(); ctrl(\App\Modules\Documents\Controllers\InvoiceController::class)->mostrar($p); });

// ── API Boletas ──
$router->post('/api/boletas', function () { auth(); ctrl(\App\Modules\Documents\Controllers\ReceiptController::class)->guardar(); });
$router->get('/api/boletas', function () { auth(); ctrl(\App\Modules\Documents\Controllers\ReceiptController::class)->index(); });
$router->get('/api/boletas/{id}', function ($p) { auth(); ctrl(\App\Modules\Documents\Controllers\ReceiptController::class)->mostrar($p); });

// ── API Notas Crédito ──
$router->post('/api/notas-credito', function () { auth(); ctrl(\App\Modules\Documents\Controllers\CreditNoteController::class)->guardar(); });
$router->get('/api/notas-credito', function () { auth(); ctrl(\App\Modules\Documents\Controllers\CreditNoteController::class)->index(); });

// ── API Notas Débito ──
$router->post('/api/notas-debito', function () { auth(); ctrl(\App\Modules\Documents\Controllers\DebitNoteController::class)->guardar(); });
$router->get('/api/notas-debito', function () { auth(); ctrl(\App\Modules\Documents\Controllers\DebitNoteController::class)->index(); });

// ── API Guías Remisión ──
$router->post('/api/guias-remision', function () { auth(); ctrl(\App\Modules\Documents\Controllers\DispatchGuideController::class)->guardar(); });
$router->get('/api/guias-remision', function () { auth(); ctrl(\App\Modules\Documents\Controllers\DispatchGuideController::class)->index(); });

// ── API Resúmenes ──
$router->post('/api/resumenes', function () { auth(); ctrl(\App\Modules\Documents\Controllers\SummaryController::class)->guardar(); });
$router->get('/api/resumenes', function () { auth(); ctrl(\App\Modules\Documents\Controllers\SummaryController::class)->indexApi(); });
$router->get('/api/resumenes/{id}/estado', function ($p) { auth(); ctrl(\App\Modules\Documents\Controllers\SummaryController::class)->estado($p); });

// ── API Descargas ──
$router->get('/api/{tipo}/{id}/pdf', function ($p) { auth(); ctrl(\App\Modules\Documents\Controllers\DocumentListController::class)->descargarPdf($p); });
$router->get('/api/{tipo}/{id}/xml', function ($p) { auth(); ctrl(\App\Modules\Documents\Controllers\DocumentListController::class)->descargarXml($p); });
$router->get('/api/{tipo}/{id}/cdr', function ($p) { auth(); ctrl(\App\Modules\Documents\Controllers\DocumentListController::class)->descargarCdr($p); });

// ── API Panel ──
$router->get('/api/panel/indicadores', function () { auth(); ctrl(\App\Modules\Dashboard\Controllers\DashboardController::class)->procesar('panelIndicadores', []); });
$router->get('/api/panel/documentos-recientes', function () { auth(); ctrl(\App\Modules\Dashboard\Controllers\DashboardController::class)->procesar('panelDocumentosRecientes', []); });
$router->get('/api/panel/ventas-mensuales', function () { auth(); ctrl(\App\Modules\Dashboard\Controllers\DashboardController::class)->procesar('panelVentasMensuales', []); });
$router->get('/api/panel/estado-sunat', function () { auth(); ctrl(\App\Modules\Dashboard\Controllers\DashboardController::class)->procesar('panelEstadoSunat', []); });
$router->get('/api/panel/por-moneda', function () { auth(); ctrl(\App\Modules\Dashboard\Controllers\DashboardController::class)->procesar('panelPorMoneda', []); });

// ── API Productos CRUD ──
$router->get('/api/productos', function () { auth(); ctrl(\App\Modules\Products\Controllers\ProductApiController::class)->listar(); });
$router->get('/api/productos/{id}', function ($p) { auth(); ctrl(\App\Modules\Products\Controllers\ProductApiController::class)->obtener($p); });
$router->post('/api/productos', function () { auth(); ctrl(\App\Modules\Products\Controllers\ProductApiController::class)->guardar([]); });
$router->post('/api/productos/{id}', function ($p) { auth(); ctrl(\App\Modules\Products\Controllers\ProductApiController::class)->guardar($p); });
$router->delete('/api/productos/{id}', function ($p) { auth(); ctrl(\App\Modules\Products\Controllers\ProductApiController::class)->eliminar($p); });
$router->get('/api/categorias', function () { auth(); ctrl(\App\Modules\Products\Controllers\ProductApiController::class)->listarCategorias(); });

// ── API Clientes CRUD ──
$router->get('/api/clientes-local', function () { auth(); ctrl(\App\Modules\Clients\Controllers\ClientApiController::class)->listar(); });
$router->get('/api/clientes-local/{id}', function ($p) { auth(); ctrl(\App\Modules\Clients\Controllers\ClientApiController::class)->obtener($p); });
$router->post('/api/clientes-local', function () { auth(); ctrl(\App\Modules\Clients\Controllers\ClientApiController::class)->guardar([]); });
$router->post('/api/clientes-local/{id}', function ($p) { auth(); ctrl(\App\Modules\Clients\Controllers\ClientApiController::class)->guardar($p); });
$router->delete('/api/clientes-local/{id}', function ($p) { auth(); ctrl(\App\Modules\Clients\Controllers\ClientApiController::class)->eliminar($p); });

// ── API Inventario ──
$router->get('/api/inventario/productos', function () { auth(); ctrl(\App\Modules\Inventory\Controllers\InventoryApiController::class)->listarProductos(); });
$router->get('/api/inventario/movimientos', function () { auth(); ctrl(\App\Modules\Inventory\Controllers\InventoryApiController::class)->listarMovimientos(); });
$router->post('/api/inventario/movimiento', function () { auth(); ctrl(\App\Modules\Inventory\Controllers\InventoryApiController::class)->registrarMovimiento(); });

// ── API Compras ──
$router->get('/api/compras', function () { auth(); ctrl(\App\Modules\Purchases\Controllers\PurchaseApiController::class)->listar(); });
$router->get('/api/compras/{id}', function ($p) { auth(); ctrl(\App\Modules\Purchases\Controllers\PurchaseApiController::class)->obtener($p); });
$router->post('/api/compras', function () { auth(); ctrl(\App\Modules\Purchases\Controllers\PurchaseApiController::class)->guardar(); });
$router->delete('/api/compras/{id}', function ($p) { auth(); ctrl(\App\Modules\Purchases\Controllers\PurchaseApiController::class)->eliminar($p); });
