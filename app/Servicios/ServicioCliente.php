<?php
declare(strict_types=1);

namespace App\Servicios;

use App\Repositorios\RepositorioCliente;

class ServicioCliente
{
    private RepositorioCliente $repo;

    public function __construct(?RepositorioCliente $repo = null)
    {
        $this->repo = $repo ?? new RepositorioCliente();
    }

    public function listar(int $userId, string $buscar = ''): array
    {
        return $this->repo->listarPorUsuario($userId, $buscar);
    }

    public function obtener(int $id): ?array
    {
        return $this->repo->find('clientes', $id);
    }

    public function crear(int $userId, array $data): array
    {
        $numDoc = trim($data['num_doc'] ?? '');
        $razonSocial = trim($data['razon_social'] ?? '');

        if (!$numDoc || !$razonSocial) {
            return ['success' => false, 'message' => 'N° Documento y Razón Social son requeridos'];
        }

        $dup = $this->repo->buscarPorDoc($userId, $numDoc);
        if ($dup) {
            return ['success' => false, 'message' => 'Ya existe un cliente con ese documento'];
        }

        $id = $this->repo->create('clientes', [
            'user_id'      => $userId,
            'tipo_doc'     => trim($data['tipo_doc'] ?? '6'),
            'num_doc'      => $numDoc,
            'razon_social' => $razonSocial,
            'direccion'    => trim($data['direccion'] ?? ''),
            'email'        => trim($data['email'] ?? ''),
            'telefono'     => trim($data['telefono'] ?? ''),
        ]);

        return ['success' => true, 'message' => 'Cliente creado', 'id' => $id];
    }

    public function actualizar(int $id, int $userId, array $data): array
    {
        $existing = $this->repo->find('clientes', $id);
        if (!$existing || $existing['user_id'] != $userId) {
            return ['success' => false, 'message' => 'Cliente no encontrado'];
        }

        $this->repo->update('clientes', $id, [
            'tipo_doc'     => trim($data['tipo_doc'] ?? $existing['tipo_doc']),
            'num_doc'      => trim($data['num_doc'] ?? $existing['num_doc']),
            'razon_social' => trim($data['razon_social'] ?? $existing['razon_social']),
            'direccion'    => trim($data['direccion'] ?? ''),
            'email'        => trim($data['email'] ?? ''),
            'telefono'     => trim($data['telefono'] ?? ''),
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

        return ['success' => true, 'message' => 'Cliente actualizado'];
    }

    public function eliminar(int $id, int $userId): array
    {
        $existing = $this->repo->find('clientes', $id);
        if (!$existing || $existing['user_id'] != $userId) {
            return ['success' => false, 'message' => 'Cliente no encontrado'];
        }

        $this->repo->delete('clientes', $id);
        return ['success' => true, 'message' => 'Cliente eliminado'];
    }
}
