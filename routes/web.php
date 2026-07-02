<?php
declare(strict_types=1);

$router = \App\Nucleo\App::getInstance()->getRouter();

// ── Auth ──
$router->get('/login', 'AutenticacionControlador@mostrarLogin');
$router->post('/login', 'AutenticacionControlador@iniciarSesion');
$router->get('/logout', 'AutenticacionControlador@cerrarSesion');

// ── Pages ──
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
$router->get('/inventario', function () { auth(); ctrl(\App\Controladores\PanelControlador::class)->inventario(); });
$router->get('/compras', function () { auth(); ctrl(\App\Controladores\PanelControlador::class)->compras(); });
