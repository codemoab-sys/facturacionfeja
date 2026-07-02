<?php
declare(strict_types=1);

namespace App\Framework;

class Controller
{
    protected ?Container $container = null;
    protected ?Request $request = null;

    public function __construct()
    {
        $this->container = Container::getInstance();
        $this->request = App::getInstance()->getRequest();
    }

    protected function view(string $path, array $data = []): void
    {
        extract($data);
        $viewFile = __DIR__ . '/../../app/Vistas/' . $path . '.php';
        if (!file_exists($viewFile)) {
            throw new \Exception("View not found: " . $path);
        }
        require $viewFile;
    }

    protected function render(string $path, array $data = [], string $layout = 'layouts/main'): void
    {
        $data['sessionUser'] = Session::get('user_nombre', 'Usuario');
        $data['sessionLogin'] = Session::get('user_usuario', '');
        $viewContent = $this->captureView($path, $data);
        $data['content'] = $viewContent;
        $this->view($layout, $data);
    }

    protected function captureView(string $path, array $data = []): string
    {
        extract($data);
        ob_start();
        $viewFile = __DIR__ . '/../../app/Vistas/' . $path . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        }
        return ob_get_clean();
    }

    protected function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . (BASE_PATH ?: '') . $path);
        exit;
    }

    protected function getRequest(): Request
    {
        return $this->request;
    }
}
