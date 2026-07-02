<?php
declare(strict_types=1);

namespace App\Modules\Inventory\Controllers;

use App\Framework\ApiController;
use App\Modules\Inventory\Services\InventoryService;

class InventoryApiController extends ApiController
{
    private InventoryService $servicio;

    public function __construct(?InventoryService $servicio = null)
    {
        parent::__construct();
        $this->servicio = $servicio ?? new InventoryService();
    }

    public function listarProductos(): void
    {
        $this->requireAuth();
        $buscar = $this->param('buscar', '');
        $this->success($this->servicio->listarProductosConStock($this->userId, $buscar));
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
        $this->success($this->servicio->listarMovimientos($this->userId, $filtros));
    }

    public function registrarMovimiento(): void
    {
        $this->requireAuth();
        $productoId = (int)$this->input('producto_id', 0);
        $tipo = $this->input('tipo', '');
        $cantidad = (float)$this->input('cantidad', 0);
        $motivo = $this->input('motivo', '');

        if (!$productoId) $this->error('Producto requerido');

        $result = $this->servicio->registrarMovimiento($this->userId, $productoId, $tipo, $cantidad, $motivo);
        $this->json($result, $result['success'] ? 200 : 400);
    }
}
