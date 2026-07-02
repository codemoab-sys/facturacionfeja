<?php
declare(strict_types=1);

namespace App\Api\Controladores;

use App\Servicios\ServicioCliente;

class ClienteApiControlador extends ApiControlador
{
    private ServicioCliente $servicio;

    public function __construct(?ServicioCliente $servicio = null)
    {
        parent::__construct();
        $this->servicio = $servicio ?? new ServicioCliente();
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

        $item = $this->servicio->obtener($id);
        if (!$item) {
            $this->error('Cliente no encontrado', 404);
            return;
        }

        $this->success($item);
    }

    public function guardar(array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        $request = $this->getRequest();
        $data = [
            'tipo_doc'     => $request->input('tipo_doc', '6'),
            'num_doc'      => $request->input('num_doc', ''),
            'razon_social' => $request->input('razon_social', ''),
            'direccion'    => $request->input('direccion', ''),
            'email'        => $request->input('email', ''),
            'telefono'     => $request->input('telefono', ''),
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
}
