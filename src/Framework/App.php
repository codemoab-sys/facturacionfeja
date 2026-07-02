<?php
declare(strict_types=1);

namespace App\Framework;

class App
{
    private static ?App $instance = null;
    private Router $router;
    private Request $request;

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function run(): void
    {
        session_start();
        $this->request = new Request();
        $this->router = new Router();

        require_once __DIR__ . '/../../routes/helpers.php';
        require_once __DIR__ . '/../../routes/web.php';
        require_once __DIR__ . '/../../routes/api.php';

        $this->router->dispatch($this->request);
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getRouter(): Router
    {
        return $this->router;
    }
}
