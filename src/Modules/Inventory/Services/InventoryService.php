<?php
declare(strict_types=1);

namespace App\Modules\Inventory\Services;

use App\Modules\Inventory\Repositories\MovementRepository;
use App\Modules\Products\Repositories\ProductRepository;

class InventoryService
{
    private MovementRepository $repoMovimiento;
    private ProductRepository $repoProducto;

    public function __construct(
        ?MovementRepository $repoMovimiento = null,
        ?ProductRepository $repoProducto = null
    ) {
        $this->repoMovimiento = $repoMovimiento ?? new MovementRepository();
        $this->repoProducto = $repoProducto ?? new ProductRepository();
    }

    public function listarProductosConStock(int $userId, string $buscar = ''): array
    {
        $items = $this->repoProducto->listarPorUsuario($userId, $buscar);
        foreach ($items as &$item) {
            $item['valorizado'] = (float)$item['stock'] * (float)$item['precio_compra'];
        }
        return $items;
    }

    public function listarMovimientos(int $userId, array $filtros): array
    {
        return $this->repoMovimiento->listar($userId, $filtros);
    }

    public function registrarMovimiento(int $userId, int $productoId, string $tipo, float $cantidad, ?string $motivo = null): array
    {
        if (!in_array($tipo, ['entrada', 'salida', 'ajuste'])) {
            return ['success' => false, 'message' => 'Tipo de movimiento invalido'];
        }

        if ($cantidad <= 0) {
            return ['success' => false, 'message' => 'La cantidad debe ser mayor a cero'];
        }

        $producto = $this->repoProducto->find('productos', $productoId);
        if (!$producto || $producto['user_id'] != $userId) {
            return ['success' => false, 'message' => 'Producto no encontrado'];
        }

        $stockActual = (float)$producto['stock'];

        if ($tipo === 'salida' && $cantidad > $stockActual) {
            return ['success' => false, 'message' => "Stock insuficiente. Stock actual: {$stockActual}"];
        }

        $nuevoStock = $tipo === 'salida'
            ? $stockActual - $cantidad
            : ($tipo === 'entrada' ? $stockActual + $cantidad : $cantidad);

        $this->repoProducto->actualizarStock($productoId, $nuevoStock);

        $this->repoMovimiento->registrar($userId, $productoId, $tipo, $cantidad, $stockActual, $nuevoStock, $motivo);

        return [
            'success' => true,
            'message' => 'Movimiento registrado',
            'data'    => ['stock_anterior' => $stockActual, 'stock_nuevo' => $nuevoStock],
        ];
    }
}
