<?php
declare(strict_types=1);

namespace App\Modules\Auth\Controllers;

use App\Framework\Controller;
use App\Framework\Session;
use App\Modules\Auth\Services\AuthService;

class LoginController extends Controller
{
    private AuthService $authService;

    public function __construct(?AuthService $authService = null)
    {
        parent::__construct();
        $this->authService = $authService ?? new AuthService();
    }

    public function mostrarLogin(array $params = []): void
    {
        if (Session::has('user_id')) {
            $this->redirect('/');
        }
        $this->view('layouts/login');
    }

    public function iniciarSesion(array $params = []): void
    {
        $usuario = trim($this->request->post('usuario', ''));
        $password = $this->request->post('password', '');
        $result = $this->authService->login($usuario, $password);
        $this->json($result->toArray(), $result->getStatusCode());
    }

    public function cerrarSesion(array $params = []): void
    {
        $this->authService->logout();
        $this->redirect('/login');
    }
}
