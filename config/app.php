<?php
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
define('BASE_PATH', ($scriptDir === '/' || $scriptDir === '\\') ? '' : rtrim($scriptDir, '/'));
define('API_DEFAULT_BASE_URL', getenv('API_DEFAULT_BASE_URL') ?: '');

$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

$env = getenv('APP_ENV') ?: 'production';

if ($env === 'local') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

return [
    'env' => $env,
    'url' => getenv('APP_URL') ?: '',
];
