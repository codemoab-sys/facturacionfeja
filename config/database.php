<?php
// Lee credenciales desde .env — edita .env, NO este archivo
return [
    'host'    => getenv('DB_HOST') ?: '127.0.0.1',
    'port'    => getenv('DB_PORT') ?: '3306',
    'dbname'  => getenv('DB_NAME') ?: 'avicola1_facturacionfeja',
    'username'=> getenv('DB_USER') ?: 'avicola1_facturacion',
    'password'=> getenv('DB_PASS') ?: 'Exenk123@@@pro9',
    'charset' => 'utf8mb4',
];
