<?php
declare(strict_types=1);

namespace App\Servicios;

use App\Repositorios\RepositorioCompra;
use App\Repositorios\RepositorioCompraDetalle;
use App\Repositorios\RepositorioProducto;
use App\Repositorios\RepositorioMovimientoInventario;

class ServicioCompra
{
    private RepositorioCompra $repoCompra;
    private RepositorioCompraDetalle $repoDetalle;
    private RepositorioProducto $repoProducto;
    private RepositorioMovimientoInventario $repoMovimiento;

    public function __construct(
        ?RepositorioCompra $repoCompra = null,
        ?RepositorioCompraDetalle $repoDetalle = null,
        ?RepositorioProducto $repoProducto = null,
        ?RepositorioMovimientoInventario $repoMovimiento = null
    ) {
        $this->repoCompra = $repoCompra ?? new RepositorioCompra();
        $this->repoDetalle = $repoDetalle ?? new RepositorioCompraDetalle();
        $this->repoProducto = $repoProducto ?? new RepositorioProducto();
        $this->repoMovimiento = $repoMovimiento ?? new RepositorioMovimientoInventario();
    }

    public function listar(int $userId, string $buscar = ''): array
    {
        return $this->repoCompra->listar($userId, $buscar);
    }

    public function obtener(int $id, int $userId): ?array
    {
        return $this->repoCompra->obtenerConDetalles($id, $userId);
    }

    public function crear(int $userId, array $data): array
    {
        $detalles = $data['detalles'] ?? [];
        if (empty($detalles)) {
            return ['success' => false, 'message' => 'Agrega al menos un producto'];
        }

        $subtotal = (float)($data['subtotal'] ?? 0);
        $igv = (float)($data['igv'] ?? 0);
        $total = (float)($data['total'] ?? 0);

        $compraId = $this->repoCompra->create('compras', [
            'user_id'          => $userId,
            'proveedor'        => trim($data['proveedor'] ?? ''),
            'numero_documento' => trim($data['numero_documento'] ?? ''),
            'tipo_documento'   => trim($data['tipo_documento'] ?? 'FACTURA'),
            'fecha_emision'    => $data['fecha_emision'] ?? date('Y-m-d'),
            'observaciones'    => trim($data['observaciones'] ?? ''),
            'subtotal'         => $subtotal,
            'igv'              => $igv,
            'total'            => $total,
        ]);

        // Insertar detalles y actualizar stock
        foreach ($detalles as $d) {
            $productoId = (int)($d['producto_id'] ?? 0);
            $cantidad = (float)($d['cantidad'] ?? 0);
            $precioUnit = (float)($d['precio_unitario'] ?? 0);
            $subtotalDetalle = (float)($d['subtotal'] ?? ($cantidad * $precioUnit));

            $this->repoDetalle->create('compra_detalles', [
                'compra_id'       => $compraId,
                'producto_id'     => $productoId,
                'cantidad'        => $cantidad,
                'precio_unitario' => $precioUnit,
                'subtotal'        => $subtotalDetalle,
            ]);

            // Actualizar stock + precio_compra ponderado
            $producto = $this->repoProducto->find('productos', $productoId);
            if ($producto) {
                $stockActual = (float)$producto['stock'];
                $precioCompraActual = (float)$producto['precio_compra'];
                $nuevoStock = $stockActual + $cantidad;

                // Promedio ponderado
                $costoTotalActual = $stockActual * $precioCompraActual;
                $costoTotalNuevo = $cantidad * $precioUnit;
                $precioPromedio = $nuevoStock > 0
                    ? ($costoTotalActual + $costoTotalNuevo) / $nuevoStock
                    : 0;

                $this->repoProducto->execute(
                    "UPDATE productos SET stock = ?, precio_compra = ?, updated_at = NOW() WHERE id = ?",
                    [$nuevoStock, round($precioPromedio, 2), $productoId]
                );

                // Registrar movimiento de entrada
                $this->repoMovimiento->registrar(
                    $userId, $productoId, 'entrada', $cantidad,
                    $stockActual, $nuevoStock,
                    'Compra #' . $compraId,
                    'compra', $compraId
                );
            }
        }

        return ['success' => true, 'message' => 'Compra registrada', 'id' => $compraId];
    }

    public function eliminar(int $id, int $userId): array
    {
        $compra = $this->repoCompra->find('compras', $id);
        if (!$compra || $compra['user_id'] != $userId) {
            return ['success' => false, 'message' => 'Compra no encontrada'];
        }

        // Revertir stock
        $detalles = $this->repoDetalle->listarPorCompra($id);
        foreach ($detalles as $d) {
            $productoId = (int)$d['producto_id'];
            $cantidad = (float)$d['cantidad'];
            $producto = $this->repoProducto->find('productos', $productoId);
            if ($producto) {
                $stockActual = (float)$producto['stock'];
                $nuevoStock = max(0, $stockActual - $cantidad);
                $this->repoProducto->actualizarStock($productoId, $nuevoStock);
            }
        }

        $this->repoDetalle->eliminarPorCompra($id);
        $this->repoCompra->delete('compras', $id);

        return ['success' => true, 'message' => 'Compra eliminada y stock revertido'];
    }
}
