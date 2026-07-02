<?php
declare(strict_types=1);

namespace App\Modules\Purchases\Repositories;

use App\Framework\Database\Repository;

class PurchaseDetailRepository extends Repository
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

    public function eliminarPorCompra(int $compraId): void
    {
        $this->execute("DELETE FROM compra_detalles WHERE compra_id = ?", [$compraId]);
    }
}
