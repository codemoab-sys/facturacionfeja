<?php
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
define('BASE_PATH', ($scriptDir === '/' || $scriptDir === '\\') ? '' : rtrim($scriptDir, '/'));
define('API_DEFAULT_BASE_URL', '');

error_reporting(0);
ini_set('display_errors', '0');

return [
    'env' => 'production',
    'url' => '',
];
