<?php
declare(strict_types=1);

$router = \App\Framework\App::getInstance()->getRouter();

// ── Auth ──
$router->get('/login', \App\Modules\Auth\Controllers\LoginController::class . '@mostrarLogin');
$router->post('/login', \App\Modules\Auth\Controllers\LoginController::class . '@iniciarSesion');
$router->get('/logout', \App\Modules\Auth\Controllers\LoginController::class . '@cerrarSesion');

// ── Pages ──
$router->get('/', function () { auth(); ctrl(\App\Modules\Dashboard\Controllers\DashboardController::class)->index(); });
$router->get('/nueva-factura', function () { auth(); ctrl(\App\Modules\Documents\Controllers\InvoiceController::class)->create(); });
$router->get('/nueva-boleta', function () { auth(); ctrl(\App\Modules\Documents\Controllers\ReceiptController::class)->create(); });
$router->get('/nueva-nc', function () { auth(); ctrl(\App\Modules\Documents\Controllers\CreditNoteController::class)->create(); });
$router->get('/nueva-nd', function () { auth(); ctrl(\App\Modules\Documents\Controllers\DebitNoteController::class)->create(); });
$router->get('/nueva-guia', function () { auth(); ctrl(\App\Modules\Documents\Controllers\DispatchGuideController::class)->create(); });
$router->get('/productos', function () { auth(); ctrl(\App\Modules\Products\Controllers\ProductController::class)->index(); });
$router->get('/clientes', function () { auth(); ctrl(\App\Modules\Clients\Controllers\ClientController::class)->index(); });
$router->get('/configuracion', function () { auth(); ctrl(\App\Modules\Settings\Controllers\SettingsController::class)->index(); });
$router->get('/documentos/{tipo}', function ($p) { auth(); ctrl(\App\Modules\Documents\Controllers\DocumentListController::class)->index($p); });
$router->get('/resumenes', function () { auth(); ctrl(\App\Modules\Documents\Controllers\SummaryController::class)->index(); });
$router->get('/inventario', function () { auth(); ctrl(\App\Modules\Dashboard\Controllers\DashboardController::class)->inventario(); });
$router->get('/compras', function () { auth(); ctrl(\App\Modules\Dashboard\Controllers\DashboardController::class)->compras(); });
