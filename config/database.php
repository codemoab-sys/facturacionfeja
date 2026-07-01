<?php
// Lee credenciales desde .env — edita .env, NO este archivo
return [
    'host'    => getenv('DB_HOST') ?: '127.0.0.1',
    'port'    => getenv('DB_PORT') ?: '3306',
    'dbname'  => getenv('DB_NAME') ?: 'facturacionfeja',
    'username'=> getenv('DB_USER') ?: '',
    'password'=> getenv('DB_PASS') ?: '',
    'charset' => 'utf8mb4',
];
