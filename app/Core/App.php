<?php
namespace App\Core;

class App
{
    private static $instance = null;
    private $router;
    private $request;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function run()
    {
        session_start();
        $this->request = new Request();
        $this->router = new Router();

        require_once __DIR__ . '/../../routes/web.php';

        $this->router->dispatch($this->request);
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getRouter()
    {
        return $this->router;
    }
}
