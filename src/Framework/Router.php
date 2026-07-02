<?php
declare(strict_types=1);

namespace App\Framework;

class Router
{
    private array $routes = [];
    private string $basePath = '';

    public function setBasePath(string $path): void
    {
        $this->basePath = rtrim($path, '/');
    }

    public function get(string $path, callable|string $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable|string $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, callable|string $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, callable|string $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute(string $method, string $path, callable|string $handler): void
    {
        $regex = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        $regex = '#^' . $regex . '$#';
        $this->routes[] = [
            'method'    => $method,
            'regex'     => $regex,
            'handler'   => $handler,
            'middleware' => [],
        ];
    }

    public function dispatch(Request $request): void
    {
        $method = $request->getMethod();
        $uri = $request->getPath();

        if ($this->basePath) {
            $uri = substr($uri, strlen($this->basePath));
        }
        if ($uri === '' || $uri === false) {
            $uri = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            if (preg_match($route['regex'], $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $this->executeHandler($route['handler'], $params);
                return;
            }
        }

        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Route not found: ' . $method . ' ' . $uri]);
        exit;
    }

    private function executeHandler(callable|string $handler, array $params): void
    {
        if (is_callable($handler)) {
            call_user_func($handler, $params);
            return;
        }

        if (is_string($handler)) {
            $parts = explode('@', $handler);
            $class = $parts[0];
            $method = $parts[1] ?? 'index';

            if (!class_exists($class)) {
                throw new \Exception("Controller not found: " . $class);
            }

            $controller = Container::getInstance()->make($class);
            if (!method_exists($controller, $method)) {
                throw new \Exception("Method not found: " . $class . '::' . $method);
            }

            $controller->$method($params);
        }
    }
}
