<?php
declare(strict_types=1);

namespace App\Modules\Clients\Controllers;

use App\Framework\ApiController;
use App\Modules\Clients\Services\ClientService;

class ClientApiController extends ApiController
{
    private ClientService $service;

    public function __construct(?ClientService $service = null)
    {
        parent::__construct();
        $this->service = $service ?? new ClientService();
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
        if (!$item) $this->error('Cliente no encontrado', 404);
        $this->success($item);
    }

    public function guardar(array $params): void
    {
        $this->requireAuth();
        $id = (int)($params['id'] ?? 0);
        $result = $this->service->guardar($this->userId, $id, $this->all());
        if (!$result['success']) $this->error($result['message'], 400);
        $this->success(null, $result['message']);
    }

    public function eliminar(array $params): void
    {
        $this->requireAuth();
        $id = (int)($params['id'] ?? 0);
        $result = $this->service->eliminar($id, $this->userId);
        if (!$result['success']) $this->error($result['message'], 404);
        $this->success(null, 'Cliente eliminado');
    }

    public function buscarDocumento(array $params = []): void
    {
        $api = new \App\Modules\Documents\Services\SunatApiService();
        $tipo   = $this->param('tipo', '6');
        $numero = $this->param('numero', '');
        $result = $api->get('/buscar-documento?tipo=' . urlencode($tipo) . '&numero=' . urlencode($numero));
        $this->json($result);
    }
}
