<?php
declare(strict_types=1);

namespace App\Api\Controladores;

use App\Servicios\ServicioCompra;

class CompraApiControlador extends ApiControlador
{
    private ServicioCompra $servicio;

    public function __construct(?ServicioCompra $servicio = null)
    {
        parent::__construct();
        $this->servicio = $servicio ?? new ServicioCompra();
    }

    public function listar(): void
    {
        $buscar = $_GET['buscar'] ?? '';
        $items = $this->servicio->listar($this->userId(), $buscar);
        $this->success($items);
    }

    public function obtener(array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        if (!$id) {
            $this->error('ID requerido');
            return;
        }

        $item = $this->servicio->obtener($id, $this->userId());
        if (!$item) {
            $this->error('Compra no encontrada', 404);
            return;
        }

        $this->success($item);
    }

    public function guardar(): void
    {
        $request = $this->getRequest();
        $data = [
            'proveedor'        => $request->input('proveedor', ''),
            'numero_documento' => $request->input('numero_documento', ''),
            'tipo_documento'   => $request->input('tipo_documento', 'FACTURA'),
            'fecha_emision'    => $request->input('fecha_emision', date('Y-m-d')),
            'observaciones'    => $request->input('observaciones', ''),
            'subtotal'         => (float)$request->input('subtotal', 0),
            'igv'              => (float)$request->input('igv', 0),
            'total'            => (float)$request->input('total', 0),
            'detalles'         => $request->input('detalles', []),
        ];

        $result = $this->servicio->crear($this->userId(), $data);
        $this->json($result, $result['success'] ? 200 : 400);
    }

    public function eliminar(array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        if (!$id) {
            $this->error('ID requerido');
            return;
        }

        $result = $this->servicio->eliminar($id, $this->userId());
        $this->json($result, $result['success'] ? 200 : 400);
    }
}
