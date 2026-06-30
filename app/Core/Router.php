<?php
namespace App\Core;

class Router
{
    private $routes = [];
    private $basePath = '';

    public function setBasePath($path)
    {
        $this->basePath = rtrim($path, '/');
    }

    public function get($path, $handler)
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post($path, $handler)
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put($path, $handler)
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete($path, $handler)
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute($method, $path, $handler)
    {
        $regex = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        $regex = '#^' . $regex . '$#';
        $this->routes[] = [
            'method'  => $method,
            'regex'   => $regex,
            'handler' => $handler,
        ];
    }

    public function dispatch(Request $request)
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
        echo json_encode(['success' => false, 'message' => 'Route not found: ' . $method . ' ' . $uri]);
        exit;
    }

    private function executeHandler($handler, $params)
    {
        if (is_callable($handler)) {
            call_user_func($handler, $params);
            return;
        }

        if (is_string($handler)) {
            $parts = explode('@', $handler);
            $controllerName = 'App\\Controllers\\' . $parts[0];
            $methodName = $parts[1] ?? 'index';

            if (!class_exists($controllerName)) {
                throw new \Exception("Controller not found: " . $controllerName);
            }

            $controller = new $controllerName();
            if (!method_exists($controller, $methodName)) {
                throw new \Exception("Method not found: " . $controllerName . '::' . $methodName);
            }

            $controller->$methodName($params);
        }
    }
}
