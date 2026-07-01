<?php
declare(strict_types=1);

namespace App\Nucleo;

use App\Validacion\Validador;

class Solicitud
{
    private string $method;
    private string $path;
    private array $body;
    private string $basePath;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];

        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        $this->basePath = $scriptName === '/' || $scriptName === '\\' ? '' : rtrim($scriptName, '/');

        $uri = $_SERVER['REQUEST_URI'];
        $pos = strpos($uri, '?');
        $uri = $pos !== false ? substr($uri, 0, $pos) : $uri;
        $uri = rtrim($uri, '/') ?: '/';

        if ($this->basePath && strpos($uri, $this->basePath) === 0) {
            $uri = substr($uri, strlen($this->basePath)) ?: '/';
        }

        $this->path = $uri;

        $raw = file_get_contents('php://input');
        $this->body = json_decode($raw, true) ?? [];
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? ($this->body[$key] ?? $default);
    }

    public function all(): array
    {
        return array_merge($_GET, $_POST, $this->body);
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->post($key, $default);
    }

    public function validate(array $rules): ?array
    {
        $validator = new Validador();
        return $validator->validate($this->all(), $rules);
    }
}
