<?php
declare(strict_types=1);

namespace App\Modules\Products\Controllers;

use App\Framework\ApiController;
use App\Modules\Products\Services\ProductService;

class ProductApiController extends ApiController
{
    private ProductService $service;

    public function __construct(?ProductService $service = null)
    {
        parent::__construct();
        $this->service = $service ?? new ProductService();
    }

    public function listar(array $params = []): void
    {
        $this->requireAuth();
        $buscar = $this->param('buscar', '');
        $items = $this->service->listar($this->userId, $buscar);
        $this->success($items);
    }

    public function obtener(array $params): void
    {
        $this->requireAuth();
        $id = (int)($params['id'] ?? 0);
        $item = $this->service->obtener($id, $this->userId);
        if (!$item) {
            $this->error('Producto no encontrado', 404);
        }
        $this->success($item);
    }

    public function guardar(array $params): void
    {
        $this->requireAuth();
        $id = (int)($params['id'] ?? 0);
        $result = $this->service->guardar($this->userId, $id, $this->all());
        if (!$result['success']) {
            $this->error($result['message'], 400);
        }
        $this->success(null, $result['message']);
    }

    public function eliminar(array $params): void
    {
        $this->requireAuth();
        $id = (int)($params['id'] ?? 0);
        $result = $this->service->eliminar($id, $this->userId);
        if (!$result['success']) {
            $this->error($result['message'], 404);
        }
        $this->success(null, 'Producto eliminado');
    }

    public function listarCategorias(array $params = []): void
    {
        $items = $this->service->listarCategorias();
        $this->success($items);
    }
}
