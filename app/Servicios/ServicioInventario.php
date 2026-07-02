<?php
declare(strict_types=1);

namespace App\Servicios;

use App\Repositorios\RepositorioMovimientoInventario;
use App\Repositorios\RepositorioProducto;

class ServicioInventario
{
    private RepositorioMovimientoInventario $repoMovimiento;
    private RepositorioProducto $repoProducto;

    public function __construct(
        ?RepositorioMovimientoInventario $repoMovimiento = null,
        ?RepositorioProducto $repoProducto = null
    ) {
        $this->repoMovimiento = $repoMovimiento ?? new RepositorioMovimientoInventario();
        $this->repoProducto = $repoProducto ?? new RepositorioProducto();
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

        $this->repoMovimiento->registrar(
            $userId, $productoId, $tipo, $cantidad,
            $stockActual, $nuevoStock, $motivo
        );

        return [
            'success' => true,
            'message' => 'Movimiento registrado',
            'data'    => [
                'stock_anterior' => $stockActual,
                'stock_nuevo'    => $nuevoStock,
            ],
        ];
    }

    public function obtenerMovimientosProducto(int $productoId, int $userId): array
    {
        $producto = $this->repoProducto->find('productos', $productoId);
        if (!$producto || $producto['user_id'] != $userId) {
            return ['success' => false, 'message' => 'Producto no encontrado'];
        }

        $movimientos = $this->repoMovimiento->listarPorProducto($productoId, $userId);

        return [
            'success'     => true,
            'producto'    => $producto,
            'movimientos' => $movimientos,
        ];
    }
}
