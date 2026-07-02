<?php
declare(strict_types=1);

namespace App\Repositorios;

class RepositorioCompra extends RepositorioBase
{
    public function listar(int $userId, string $buscar = ''): array
    {
        $sql = "SELECT * FROM compras WHERE user_id = ?";
        $params = [$userId];

        if ($buscar) {
            $sql .= " AND (proveedor LIKE ? OR numero_documento LIKE ?)";
            $like = '%' . $buscar . '%';
            $params[] = $like;
            $params[] = $like;
        }

        $sql .= " ORDER BY created_at DESC";
        return $this->query($sql, $params);
    }

    public function obtenerConDetalles(int $id, int $userId): ?array
    {
        $compra = $this->find('compras', $id);
        if (!$compra || $compra['user_id'] != $userId) {
            return null;
        }

        $detalles = $this->query(
            "SELECT cd.*, p.codigo, p.descripcion AS producto
             FROM compra_detalles cd
             JOIN productos p ON cd.producto_id = p.id
             WHERE cd.compra_id = ?
             ORDER BY cd.id ASC",
            [$id]
        );

        $compra['detalles'] = $detalles;
        return $compra;
    }
}
