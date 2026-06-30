<?php
namespace App\Core;

class Request
{
    private $method;
    private $path;
    private $body;
    private $basePath;

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

    public function getBasePath()
    {
        return $this->basePath;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function get($key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    public function post($key, $default = null)
    {
        return $_POST[$key] ?? ($this->body[$key] ?? $default);
    }

    public function all()
    {
        return array_merge($_GET, $_POST, $this->body);
    }

    public function input($key, $default = null)
    {
        return $this->post($key, $default);
    }
}
