<?php
declare(strict_types=1);

namespace App\Modules\Inventory\Repositories;

use App\Framework\Database\Repository;

class MovementRepository extends Repository
{
    public function listar(int $userId, array $filtros = []): array
    {
        $sql = "SELECT m.*, p.codigo, p.descripcion AS producto
                FROM inventario_movimientos m
                JOIN productos p ON m.producto_id = p.id
                WHERE m.user_id = ?";
        $params = [$userId];

        if (!empty($filtros['tipo'])) {
            $sql .= " AND m.tipo = ?";
            $params[] = $filtros['tipo'];
        }

        if (!empty($filtros['producto_id'])) {
            $sql .= " AND m.producto_id = ?";
            $params[] = (int)$filtros['producto_id'];
        }

        if (!empty($filtros['desde'])) {
            $sql .= " AND m.created_at >= ?";
            $params[] = $filtros['desde'] . ' 00:00:00';
        }

        if (!empty($filtros['hasta'])) {
            $sql .= " AND m.created_at <= ?";
            $params[] = $filtros['hasta'] . ' 23:59:59';
        }

        $sql .= " ORDER BY m.created_at DESC LIMIT 500";
        return $this->query($sql, $params);
    }

    public function registrar(int $userId, int $productoId, string $tipo, float $cantidad, float $stockAnterior, float $stockNuevo, ?string $motivo = null, ?string $refTipo = null, ?int $refId = null): int
    {
        return $this->create('inventario_movimientos', [
            'user_id'         => $userId,
            'producto_id'     => $productoId,
            'tipo'            => $tipo,
            'cantidad'        => $cantidad,
            'stock_anterior'  => $stockAnterior,
            'stock_nuevo'     => $stockNuevo,
            'motivo'          => $motivo ?? '',
            'referencia_tipo' => $refTipo,
            'referencia_id'   => $refId,
        ]);
    }
}
