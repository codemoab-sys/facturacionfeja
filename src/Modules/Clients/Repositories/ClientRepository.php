<?php
declare(strict_types=1);

namespace App\Modules\Clients\Repositories;

use App\Framework\Database\Repository;

class ClientRepository extends Repository
{
    private string $tabla = 'clientes';

    public function findById(int $id): ?array
    {
        return $this->find($this->tabla, $id);
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

    public function createClient(array $data): int
    {
        return $this->create($this->tabla, $data);
    }

    public function updateClient(int $id, array $data): void
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->update($this->tabla, $id, $data);
    }

    public function deleteClient(int $id): void
    {
        $this->delete($this->tabla, $id);
    }

    public function buscarPorDoc(int $userId, string $numDoc): ?array
    {
        $rows = $this->query("SELECT * FROM {$this->tabla} WHERE user_id = ? AND num_doc = ?", [$userId, $numDoc]);
        return $rows[0] ?? null;
    }
}
