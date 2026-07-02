<?php
declare(strict_types=1);

namespace App\Modules\Purchases\Controllers;

use App\Framework\ApiController;
use App\Modules\Purchases\Services\PurchaseService;

class PurchaseApiController extends ApiController
{
    private PurchaseService $servicio;

    public function __construct(?PurchaseService $servicio = null)
    {
        parent::__construct();
        $this->servicio = $servicio ?? new PurchaseService();
    }

    public function listar(): void
    {
        $this->requireAuth();
        $buscar = $this->param('buscar', '');
        $this->success($this->servicio->listar($this->userId, $buscar));
    }

    public function obtener(array $params): void
    {
        $this->requireAuth();
        $id = (int)($params['id'] ?? 0);
        if (!$id) $this->error('ID requerido');
        $item = $this->servicio->obtener($id, $this->userId);
        if (!$item) $this->error('Compra no encontrada', 404);
        $this->success($item);
    }

    public function guardar(): void
    {
        $this->requireAuth();
        $data = [
            'proveedor'        => $this->input('proveedor', ''),
            'numero_documento' => $this->input('numero_documento', ''),
            'tipo_documento'   => $this->input('tipo_documento', 'FACTURA'),
            'fecha_emision'    => $this->input('fecha_emision', date('Y-m-d')),
            'observaciones'    => $this->input('observaciones', ''),
            'subtotal'         => (float)$this->input('subtotal', 0),
            'igv'              => (float)$this->input('igv', 0),
            'total'            => (float)$this->input('total', 0),
            'detalles'         => $this->input('detalles', []),
        ];
        $result = $this->servicio->crear($this->userId, $data);
        $this->json($result, $result['success'] ? 200 : 400);
    }

    public function eliminar(array $params): void
    {
        $this->requireAuth();
        $id = (int)($params['id'] ?? 0);
        if (!$id) $this->error('ID requerido');
        $result = $this->servicio->eliminar($id, $this->userId);
        $this->json($result, $result['success'] ? 200 : 400);
    }
}
