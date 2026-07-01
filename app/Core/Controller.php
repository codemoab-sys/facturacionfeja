<?php
namespace App\Core;

class Controller
{
    protected function view($path, $data = [])
    {
        extract($data);
        $viewFile = __DIR__ . '/../Views/' . $path . '.php';
        if (!file_exists($viewFile)) {
            throw new \Exception("View not found: " . $path);
        }
        require $viewFile;
    }

    protected function render($path, $data = [], $layout = 'layouts/main')
    {
        $data['sessionUser'] = \App\Core\Session::get('user_nombre', 'Usuario');
        $data['sessionLogin'] = \App\Core\Session::get('user_usuario', '');
        $viewContent = $this->captureView($path, $data);
        $data['content'] = $viewContent;
        $this->view($layout, $data);
    }

    protected function captureView($path, $data = [])
    {
        extract($data);
        ob_start();
        $viewFile = __DIR__ . '/../Views/' . $path . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        }
        return ob_get_clean();
    }

    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function redirect($path)
    {
        header('Location: ' . (BASE_PATH ?: '') . $path);
        exit;
    }

    protected function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
}
