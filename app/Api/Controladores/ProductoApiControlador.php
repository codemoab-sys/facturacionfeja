<?php
declare(strict_types=1);

namespace App\Api\Controladores;

use App\Servicios\ServicioProducto;

class ProductoApiControlador extends ApiControlador
{
    private ServicioProducto $servicio;

    public function __construct(?ServicioProducto $servicio = null)
    {
        parent::__construct();
        $this->servicio = $servicio ?? new ServicioProducto();
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

        $data = $this->servicio->obtenerConCategorias($id);
        if (!$data['item']) {
            $this->error('Producto no encontrado', 404);
            return;
        }

        $this->success($data['item'], '', ['categorias' => $data['categorias']]);
    }

    public function guardar(array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        $request = $this->getRequest();
        $data = [
            'codigo'             => $request->input('codigo', ''),
            'descripcion'        => $request->input('descripcion', ''),
            'precio_unitario'    => $request->input('precio_unitario', 0),
            'precio_compra'      => $request->input('precio_compra', 0),
            'unidad'             => $request->input('unidad', 'NIU'),
            'tip_afe_igv'        => $request->input('tip_afe_igv', '10'),
            'cod_producto_sunat' => $request->input('cod_producto_sunat', ''),
            'categoria_id'       => $request->input('categoria_id'),
            'icbper'             => $request->input('icbper'),
            'factor_icbper'      => $request->input('factor_icbper'),
            'stock_minimo'       => $request->input('stock_minimo', 0),
            'stock'              => $request->input('stock', 0),
        ];

        $result = $id
            ? $this->servicio->actualizar($id, $this->userId(), $data)
            : $this->servicio->crear($this->userId(), $data);

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

    public function listarCategorias(): void
    {
        $items = $this->servicio->listarCategorias();
        $this->success($items);
    }
}
