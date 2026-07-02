<?php
declare(strict_types=1);

namespace App\Repositorios;

class RepositorioCompraDetalle extends RepositorioBase
{
    public function listarPorCompra(int $compraId): array
    {
        return $this->query(
            "SELECT cd.*, p.codigo, p.descripcion AS producto
             FROM compra_detalles cd
             JOIN productos p ON cd.producto_id = p.id
             WHERE cd.compra_id = ?
             ORDER BY cd.id ASC",
            [$compraId]
        );
    }

    public function insertarMultiples(int $compraId, array $detalles): void
    {
        foreach ($detalles as $d) {
            $this->create('compra_detalles', [
                'compra_id'       => $compraId,
                'producto_id'     => (int)$d['producto_id'],
                'cantidad'        => (float)$d['cantidad'],
                'precio_unitario' => (float)$d['precio_unitario'],
                'subtotal'        => (float)$d['subtotal'],
            ]);
        }
    }

    public function eliminarPorCompra(int $compraId): void
    {
        $this->execute("DELETE FROM compra_detalles WHERE compra_id = ?", [$compraId]);
    }
}
