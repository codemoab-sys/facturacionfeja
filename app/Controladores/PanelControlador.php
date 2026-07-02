<?php
declare(strict_types=1);

namespace App\Controladores;

use App\Nucleo\Controlador;
use App\Servicios\ServicioApiSunat;

class PanelControlador extends Controlador
{
    public function index(array $params = []): void
    {
        $this->render('dashboard', ['pageTitle' => 'Inicio']);
    }

    public function inventario(array $params = []): void
    {
        $this->render('inventario/index', ['pageTitle' => 'Inventario']);
    }

    public function compras(array $params = []): void
    {
        $this->render('compras/index', ['pageTitle' => 'Compras']);
    }

    public function procesar(string $method, array $ignored): void
    {
        $api = new ServicioApiSunat();

        $result = match ($method) {
            'listSucursales' => $api->get('/sucursales'),
            'getEmpresa' => $api->get('/empresa'),
            'panelIndicadores' => $api->get('/panel/indicadores'),
            'panelDocumentosRecientes' => $api->get('/panel/documentos-recientes'),
            'panelVentasMensuales' => $api->get('/panel/ventas-mensuales'),
            'panelEstadoSunat' => $api->get('/panel/estado-sunat'),
            'panelPorMoneda' => $api->get('/panel/por-moneda'),
            default => ['success' => false, 'message' => 'Unknown method'],
        };

        $this->json($result);
    }
}
