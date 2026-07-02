<?php
declare(strict_types=1);

use App\Nucleo\Contenedor;
use App\Nucleo\Sesion;

function auth(): void
{
    if (!Sesion::has('user_id')) {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $isApi = strpos($uri, '/api/') !== false || strpos($uri, BASE_PATH . '/api/') !== false;
        if ($isApi) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'No autenticado']);
        } else {
            header('Location: ' . BASE_PATH . '/login');
        }
        exit;
    }
}

function ctrl(string $class): object
{
    return Contenedor::getInstance()->make($class);
}
