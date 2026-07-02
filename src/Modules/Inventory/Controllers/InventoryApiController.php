<?php
declare(strict_types=1);

namespace App\Modules\Inventory\Controllers;

use App\Framework\ApiController;

class InventoryApiController extends ApiController
{
    public function listarProductos(): void
    {
        $this->requireAuth();
        $buscar = $this->param('buscar', '');
        $servicio = new \App\Servicios\ServicioInventario();
        $items = $servicio->listarProductosConStock($this->userId, $buscar);
        $this->success($items);
    }

    public function listarMovimientos(): void
    {
        $this->requireAuth();
        $filtros = [
            'tipo'        => $this->param('tipo', ''),
            'producto_id' => $this->param('producto_id', ''),
            'desde'       => $this->param('desde', ''),
            'hasta'       => $this->param('hasta', ''),
        ];
        $servicio = new \App\Servicios\ServicioInventario();
        $items = $servicio->listarMovimientos($this->userId, $filtros);
        $this->success($items);
    }

    public function registrarMovimiento(): void
    {
        $this->requireAuth();
        $productoId = (int)$this->input('producto_id', 0);
        $tipo = $this->input('tipo', '');
        $cantidad = (float)$this->input('cantidad', 0);
        $motivo = $this->input('motivo', '');

        if (!$productoId) {
            $this->error('Producto requerido');
        }

        $servicio = new \App\Servicios\ServicioInventario();
        $result = $servicio->registrarMovimiento($this->userId, $productoId, $tipo, $cantidad, $motivo);
        $this->json($result, $result['success'] ? 200 : 400);
    }
}
