<?php
declare(strict_types=1);

namespace App\Modules\Purchases\Controllers;

use App\Framework\ApiController;

class PurchaseApiController extends ApiController
{
    public function listar(): void
    {
        $this->requireAuth();
        $buscar = $this->param('buscar', '');
        $servicio = new \App\Servicios\ServicioCompra();
        $items = $servicio->listar($this->userId, $buscar);
        $this->success($items);
    }

    public function obtener(array $params): void
    {
        $this->requireAuth();
        $id = (int)($params['id'] ?? 0);
        if (!$id) $this->error('ID requerido');
        $servicio = new \App\Servicios\ServicioCompra();
        $item = $servicio->obtener($id, $this->userId);
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
        $servicio = new \App\Servicios\ServicioCompra();
        $result = $servicio->crear($this->userId, $data);
        $this->json($result, $result['success'] ? 200 : 400);
    }

    public function eliminar(array $params): void
    {
        $this->requireAuth();
        $id = (int)($params['id'] ?? 0);
        if (!$id) $this->error('ID requerido');
        $servicio = new \App\Servicios\ServicioCompra();
        $result = $servicio->eliminar($id, $this->userId);
        $this->json($result, $result['success'] ? 200 : 400);
    }
}
