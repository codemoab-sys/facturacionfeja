<?php
declare(strict_types=1);

namespace App\Modules\Clients\Services;

use App\Modules\Clients\Repositories\ClientRepository;

class ClientService
{
    private ClientRepository $repo;

    public function __construct(?ClientRepository $repo = null)
    {
        $this->repo = $repo ?? new ClientRepository();
    }

    public function listar(int $userId, string $buscar = ''): array
    {
        return $this->repo->listarPorUsuario($userId, $buscar);
    }

    public function obtener(int $id, ?int $userId = null): ?array
    {
        $item = $this->repo->findById($id);
        if ($item && $userId !== null && $item['user_id'] != $userId) return null;
        return $item;
    }

    public function guardar(int $userId, int $id, array $data): array
    {
        $numDoc = trim($data['num_doc'] ?? '');
        $razonSocial = trim($data['razon_social'] ?? '');
        if (!$numDoc || !$razonSocial) {
            return ['success' => false, 'message' => 'Documento y razón social son requeridos'];
        }

        $fields = [
            'tipo_doc'    => trim($data['tipo_doc'] ?? '6'),
            'num_doc'     => $numDoc,
            'razon_social' => $razonSocial,
            'direccion'   => trim($data['direccion'] ?? ''),
            'email'       => trim($data['email'] ?? ''),
        ];

        if ($id) {
            $existing = $this->repo->findById($id);
            if (!$existing || $existing['user_id'] != $userId) {
                return ['success' => false, 'message' => 'Cliente no encontrado'];
            }
            $this->repo->updateClient($id, $fields);
            return ['success' => true, 'message' => 'Cliente actualizado'];
        }

        $dup = $this->repo->buscarPorDoc($userId, $numDoc);
        if ($dup) {
            return ['success' => false, 'message' => 'Ya existe un cliente con ese documento'];
        }

        $fields['user_id'] = $userId;
        $this->repo->createClient($fields);
        return ['success' => true, 'message' => 'Cliente creado'];
    }

    public function eliminar(int $id, int $userId): array
    {
        $item = $this->repo->findById($id);
        if (!$item || $item['user_id'] != $userId) {
            return ['success' => false, 'message' => 'Cliente no encontrado'];
        }
        $this->repo->deleteClient($id);
        return ['success' => true, 'message' => 'Cliente eliminado'];
    }
}
