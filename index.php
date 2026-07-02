<?php
declare(strict_types=1);

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/app/Helpers.php';

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

    $file = __DIR__ . '/src/' . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require $file;
        return;
    }

    $file = __DIR__ . '/app/' . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Registrar error handler
$debug = ($_SERVER['SERVER_NAME'] ?? '') === 'localhost' || ($_SERVER['SERVER_ADDR'] ?? '') === '127.0.0.1';
$errorHandler = new \App\Framework\ErrorHandler($debug);
$errorHandler->register();

$app = \App\Framework\App::getInstance();
$app->run();
