<?php
declare(strict_types=1);

namespace App\Controladores;

use App\Nucleo\Controlador;
use App\Nucleo\Sesion;
use App\Servicios\ServicioAutenticacion;

class AutenticacionControlador extends Controlador
{
    private ServicioAutenticacion $authService;

    public function __construct(?ServicioAutenticacion $authService = null)
    {
        parent::__construct();
        $this->authService = $authService ?? new ServicioAutenticacion();
    }

    public function mostrarLogin(array $params = []): void
    {
        if (Sesion::has('user_id')) {
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
