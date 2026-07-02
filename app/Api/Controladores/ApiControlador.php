<?php
declare(strict_types=1);

namespace App\Api\Controladores;

use App\Nucleo\Contenedor;
use App\Nucleo\Solicitud;
use App\Nucleo\Sesion;
use App\Nucleo\App;

class ApiControlador
{
    protected ?Contenedor $container = null;
    protected ?Solicitud $request = null;

    public function __construct()
    {
        $this->container = Contenedor::getInstance();
        $this->request = App::getInstance()->getRequest();
    }

    protected function getRequest(): Solicitud
    {
        return $this->request;
    }

    protected function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    protected function userId(): int
    {
        return (int) Sesion::get('user_id', 0);
    }

    protected function error(string $message, int $code = 400, array $extra = []): void
    {
        $this->json(array_merge(['success' => false, 'message' => $message], $extra), $code);
    }

    protected function success(mixed $data = null, string $message = '', array $extra = []): void
    {
        $response = ['success' => true];
        if ($message) $response['message'] = $message;
        if ($data !== null) $response['data'] = $data;
        $this->json(array_merge($response, $extra));
    }
}
