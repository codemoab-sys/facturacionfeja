<?php
declare(strict_types=1);

$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
define('BASE_PATH', ($scriptDir === '/' || $scriptDir === '\\') ? '' : rtrim($scriptDir, '/'));
define('API_DEFAULT_BASE_URL', 'https://apiprueba.moabcode.com/api/v1');

error_reporting(0);
ini_set('display_errors', '0');

return [
    'env' => 'production',
    'url' => '',
];
