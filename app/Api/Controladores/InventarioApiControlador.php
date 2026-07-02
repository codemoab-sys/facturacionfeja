<?php
declare(strict_types=1);

namespace App\Api\Controladores;

use App\Servicios\ServicioInventario;
use App\Servicios\ServicioProducto;

class InventarioApiControlador extends ApiControlador
{
    private ServicioInventario $servicio;
    private ServicioProducto $servicioProducto;

    public function __construct(
        ?ServicioInventario $servicio = null,
        ?ServicioProducto $servicioProducto = null
    ) {
        parent::__construct();
        $this->servicio = $servicio ?? new ServicioInventario();
        $this->servicioProducto = $servicioProducto ?? new ServicioProducto();
    }

    public function listarProductos(): void
    {
        $buscar = $_GET['buscar'] ?? '';
        $items = $this->servicio->listarProductosConStock($this->userId(), $buscar);
        $this->success($items);
    }

    public function listarMovimientos(): void
    {
        $request = $this->getRequest();
        $filtros = [
            'tipo'       => $request->get('tipo', ''),
            'producto_id' => $request->get('producto_id', ''),
            'desde'      => $request->get('desde', ''),
            'hasta'      => $request->get('hasta', ''),
        ];
        $items = $this->servicio->listarMovimientos($this->userId(), $filtros);
        $this->success($items);
    }

    public function registrarMovimiento(): void
    {
        $request = $this->getRequest();
        $productoId = (int)$request->input('producto_id', 0);
        $tipo = $request->input('tipo', '');
        $cantidad = (float)$request->input('cantidad', 0);
        $motivo = $request->input('motivo', '');

        if (!$productoId) {
            $this->error('Producto requerido');
            return;
        }

        $result = $this->servicio->registrarMovimiento(
            $this->userId(), $productoId, $tipo, $cantidad, $motivo
        );

        $this->json($result, $result['success'] ? 200 : 400);
    }

}
