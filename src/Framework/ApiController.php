<?php
declare(strict_types=1);

namespace App\Framework;

abstract class ApiController
{
    protected ?Container $container = null;
    protected ?Request $request = null;
    protected ?int $userId = null;

    public function __construct()
    {
        $this->container = Container::getInstance();
        $this->request = App::getInstance()->getRequest();
        $this->userId = Session::get('user_id') ? (int) Session::get('user_id') : null;
    }

    protected function success(mixed $data = null, string $message = 'Operación exitosa', int $code = 200): void
    {
        $response = ['success' => true, 'message' => $message];
        if ($data !== null) {
            $response['data'] = $data;
        }
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function error(string $message, int $code = 400, ?array $errors = null): void
    {
        $response = ['success' => false, 'message' => $message];
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function requireAuth(): void
    {
        if ($this->userId === null) {
            $this->error('No autenticado', 401);
        }
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        return $this->request->input($key, $default);
    }

    protected function all(): array
    {
        return $this->request->all();
    }

    protected function param(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }
}
