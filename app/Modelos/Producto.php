<?php
declare(strict_types=1);

namespace App\Modelos;

use App\Nucleo\Modelo;

class Producto extends Modelo
{
    protected string $tabla = 'productos';

    public function obtenerPorId(int $id): ?array
    {
        return $this->findById($id);
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

        return $this->raw($sql, $params);
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

    public function buscarPorCodigo(int $userId, string $codigo): ?array
    {
        $rows = $this->raw("SELECT * FROM {$this->tabla} WHERE user_id = ? AND codigo = ?", [$userId, $codigo]);
        return $rows[0] ?? null;
    }
}
