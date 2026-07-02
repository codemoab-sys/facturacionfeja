<?php
declare(strict_types=1);

namespace App\Modules\Products\Repositories;

use App\Framework\Database\Repository;

class ProductRepository extends Repository
{
    private string $tabla = 'productos';

    public function findById(int $id): ?array
    {
        return $this->find($this->tabla, $id);
    }

    public function listarPorUsuario(int $userId, string $buscar = ''): array
    {
        $sql = "SELECT p.*, c.nombre AS categoria
                FROM {$this->tabla} p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                WHERE p.user_id = ?";
        $params = [$userId];

        if ($buscar) {
            $sql .= " AND (p.codigo LIKE ? OR p.descripcion LIKE ? OR c.nombre LIKE ?)";
            $like = '%' . $buscar . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $sql .= " ORDER BY p.codigo ASC";
        return $this->query($sql, $params);
    }

    public function createProduct(array $data): int
    {
        return $this->create($this->tabla, $data);
    }

    public function updateProduct(int $id, array $data): void
    {
        $this->update($this->tabla, $id, $data);
    }

    public function deleteProduct(int $id): void
    {
        $this->delete($this->tabla, $id);
    }

    public function buscarPorCodigo(int $userId, string $codigo): ?array
    {
        $rows = $this->query("SELECT * FROM {$this->tabla} WHERE user_id = ? AND codigo = ?", [$userId, $codigo]);
        return $rows[0] ?? null;
    }

    public function actualizarStock(int $id, float $nuevoStock): void
    {
        $this->execute("UPDATE productos SET stock = ?, updated_at = NOW() WHERE id = ?", [$nuevoStock, $id]);
    }
}
