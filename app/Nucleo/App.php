<?php
declare(strict_types=1);

namespace App\Nucleo;

class App
{
    private static ?App $instance = null;
    private Enrutador $router;
    private Solicitud $request;

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
        $this->request = new Solicitud();
        $this->router = new Enrutador();

        require_once __DIR__ . '/../../routes/helpers.php';
        require_once __DIR__ . '/../../routes/web.php';
        require_once __DIR__ . '/../../routes/api.php';

        $this->router->dispatch($this->request);
    }

    public function getRequest(): Solicitud
    {
        return $this->request;
    }

    public function getRouter(): Enrutador
    {
        return $this->router;
    }
}
