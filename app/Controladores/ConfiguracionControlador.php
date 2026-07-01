<?php
declare(strict_types=1);

namespace App\Controladores;

use App\Nucleo\Controlador;
use App\Nucleo\Sesion;

class ConfiguracionControlador extends Controlador
{
    public function index(array $params = []): void
    {
        $cfg = Sesion::get('api_config', []);
        $this->render('settings', [
            'pageTitle' => 'Configuración',
            'cfg' => $cfg,
        ]);
    }

    // ── Métodos legacy que delegan a los nuevos controllers ──

    public function mostrar(array $params = []): void
    {
        $ctrl = new ConfiguracionApiControlador();
        $ctrl->mostrar();
    }

    public function actualizar(array $params = []): void
    {
        $ctrl = new ConfiguracionApiControlador();
        $ctrl->actualizar();
    }

    public function probarConexion(array $params = []): void
    {
        $ctrl = new ConfiguracionApiControlador();
        $ctrl->probarConexion();
    }

    public function subirCertificado(array $params = []): void
    {
        $ctrl = new CertificadoControlador();
        $ctrl->subir();
    }

    public function eliminarCertificado(array $params = []): void
    {
        $ctrl = new CertificadoControlador();
        $ctrl->eliminar();
    }

    public function estadoCertificado(array $params = []): void
    {
        $ctrl = new CertificadoControlador();
        $ctrl->estado();
    }

    public function subirLogo(array $params = []): void
    {
        $ctrl = new LogoControlador();
        $ctrl->subir();
    }

    public function eliminarLogo(array $params = []): void
    {
        $ctrl = new LogoControlador();
        $ctrl->eliminar();
    }

    public function estadoLogo(array $params = []): void
    {
        $ctrl = new LogoControlador();
        $ctrl->estado();
    }

    public function imagenLogo(array $params = []): void
    {
        $ctrl = new LogoControlador();
        $ctrl->imagen();
    }
}
