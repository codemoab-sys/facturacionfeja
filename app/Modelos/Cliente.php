<?php
declare(strict_types=1);

namespace App\Modelos;

use App\Nucleo\Modelo;

class Cliente extends Modelo
{
    protected string $tabla = 'clientes';

    public function obtenerPorId(int $id): ?array
    {
        return $this->findById($id);
    }

    public function listarPorUsuario(int $userId, string $buscar = ''): array
    {
        $sql = "SELECT * FROM {$this->tabla} WHERE user_id = ?";
        $params = [$userId];

        if ($buscar) {
            $sql .= " AND (razon_social LIKE ? OR num_doc LIKE ?)";
            $like = '%' . $buscar . '%';
            $params[] = $like;
            $params[] = $like;
        }

        $sql .= " ORDER BY razon_social ASC";
        return $this->query($sql, $params);
    }

    public function crear(array $data): int
    {
        return $this->create($data);
    }

    public function actualizar(int $id, array $data): void
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->update($id, $data);
    }

    public function eliminar(int $id): void
    {
        $this->delete($id);
    }

    public function buscarPorDoc(int $userId, string $numDoc): ?array
    {
        $rows = $this->raw("SELECT * FROM {$this->tabla} WHERE user_id = ? AND num_doc = ?", [$userId, $numDoc]);
        return $rows[0] ?? null;
    }
}
