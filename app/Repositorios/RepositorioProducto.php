<?php
declare(strict_types=1);

namespace App\Repositorios;

class RepositorioProducto extends RepositorioBase
{
    public function listarPorUsuario(int $userId, string $buscar = ''): array
    {
        $sql = "SELECT p.*, c.nombre AS categoria
                FROM productos p
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

    public function buscarPorCodigo(int $userId, string $codigo): ?array
    {
        $rows = $this->query(
            "SELECT * FROM productos WHERE user_id = ? AND codigo = ?",
            [$userId, $codigo]
        );
        return $rows[0] ?? null;
    }

    public function conStockBajo(int $userId, ?int $limite = null): array
    {
        $sql = "SELECT * FROM productos WHERE user_id = ? AND stock <= stock_minimo AND stock_minimo > 0";
        $params = [$userId];
        if ($limite) {
            $sql .= " LIMIT ?";
            $params[] = $limite;
        }
        return $this->query($sql, $params);
    }

    public function actualizarStock(int $id, float $nuevoStock): void
    {
        $this->execute(
            "UPDATE productos SET stock = ?, updated_at = NOW() WHERE id = ?",
            [$nuevoStock, $id]
        );
    }
}
