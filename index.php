<?php
declare(strict_types=1);

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/app/Helpers.php';
require_once __DIR__ . '/app/Nucleo/App.php';
require_once __DIR__ . '/app/Nucleo/Controlador.php';
require_once __DIR__ . '/app/Nucleo/Modelo.php';
require_once __DIR__ . '/app/Nucleo/Enrutador.php';
require_once __DIR__ . '/app/Nucleo/Solicitud.php';
require_once __DIR__ . '/app/Nucleo/Sesion.php';
require_once __DIR__ . '/app/Nucleo/Contenedor.php';
require_once __DIR__ . '/app/Nucleo/ManejadorErrores.php';
require_once __DIR__ . '/app/Servicios/ServicioApiSunat.php';
require_once __DIR__ . '/app/Servicios/ServicioConfiguracion.php';
require_once __DIR__ . '/app/Servicios/ServicioAutenticacion.php';

// Crear directorio de logs si no existe
$logDir = __DIR__ . '/storage/logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relativeClass = substr($class, $len);

    // Check src/ first (new modular architecture)
    $file = __DIR__ . '/src/' . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require $file;
        return;
    }

    // Fallback to app/ (legacy)
    $file = __DIR__ . '/app/' . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Inicializar DI Container
$container = \App\Nucleo\Contenedor::getInstance();

// Bind services
$container->singleton(\App\Servicios\ServicioConfiguracion::class, \App\Servicios\ServicioConfiguracion::class);
$container->singleton(\App\Servicios\ServicioAutenticacion::class, \App\Servicios\ServicioAutenticacion::class);
$container->bind(\App\Repositorios\RepositorioUsuario::class, \App\Repositorios\RepositorioUsuario::class);
$container->bind(\App\Repositorios\RepositorioConfiguracionUsuario::class, \App\Repositorios\RepositorioConfiguracionUsuario::class);

// Registrar error handler
$debug = ($_SERVER['SERVER_NAME'] ?? '') === 'localhost' || ($_SERVER['SERVER_ADDR'] ?? '') === '127.0.0.1';
$errorHandler = new \App\Nucleo\ManejadorErrores($debug);
$errorHandler->register();

$app = \App\Framework\App::getInstance();
$app->run();
