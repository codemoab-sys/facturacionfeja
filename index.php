<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/app/Helpers.php';
require_once __DIR__ . '/app/Core/App.php';
require_once __DIR__ . '/app/Core/Controller.php';
require_once __DIR__ . '/app/Core/Model.php';
require_once __DIR__ . '/app/Core/Router.php';
require_once __DIR__ . '/app/Core/Request.php';
require_once __DIR__ . '/app/Core/Session.php';
require_once __DIR__ . '/app/Services/SunatApiService.php';

// Crear directorio de logs si no existe
$logDir = __DIR__ . '/storage/logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

$app = \App\Core\App::getInstance();
$app->run();
