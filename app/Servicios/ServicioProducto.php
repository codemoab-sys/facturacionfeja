<?php
declare(strict_types=1);

namespace App\Servicios;

use App\Repositorios\RepositorioProducto;
use App\Repositorios\RepositorioCategoria;

class ServicioProducto
{
    private RepositorioProducto $repo;
    private RepositorioCategoria $repoCategoria;

    public function __construct(
        ?RepositorioProducto $repo = null,
        ?RepositorioCategoria $repoCategoria = null
    ) {
        $this->repo = $repo ?? new RepositorioProducto();
        $this->repoCategoria = $repoCategoria ?? new RepositorioCategoria();
    }

    public function listar(int $userId, string $buscar = ''): array
    {
        return $this->repo->listarPorUsuario($userId, $buscar);
    }

    public function obtener(int $id): ?array
    {
        return $this->repo->find('productos', $id);
    }

    public function obtenerConCategorias(int $id): array
    {
        $item = $this->repo->find('productos', $id);
        $categorias = $this->repoCategoria->listarTodas();
        return [
            'item'       => $item,
            'categorias' => $categorias,
        ];
    }

    public function crear(int $userId, array $data): array
    {
        $codigo = trim($data['codigo'] ?? '');
        $descripcion = trim($data['descripcion'] ?? '');
        $precio = (float)($data['precio_unitario'] ?? 0);

        if (!$codigo || !$descripcion) {
            return ['success' => false, 'message' => 'Código y descripción son requeridos'];
        }

        $dup = $this->repo->buscarPorCodigo($userId, $codigo);
        if ($dup) {
            return ['success' => false, 'message' => 'Ya existe un producto con ese código'];
        }

        $id = $this->repo->create('productos', [
            'user_id'            => $userId,
            'codigo'             => $codigo,
            'cod_producto_sunat' => trim($data['cod_producto_sunat'] ?? ''),
            'descripcion'        => $descripcion,
            'unidad'             => trim($data['unidad'] ?? 'NIU'),
            'precio_unitario'    => $precio,
            'precio_compra'      => (float)($data['precio_compra'] ?? 0),
            'tip_afe_igv'        => trim($data['tip_afe_igv'] ?? '10'),
            'icbper'             => isset($data['icbper']) ? (float)$data['icbper'] : null,
            'factor_icbper'      => isset($data['factor_icbper']) ? (float)$data['factor_icbper'] : null,
            'categoria_id'       => !empty($data['categoria_id']) ? (int)$data['categoria_id'] : null,
            'stock'              => (float)($data['stock'] ?? 0),
            'stock_minimo'       => (float)($data['stock_minimo'] ?? 0),
        ]);

        return ['success' => true, 'message' => 'Producto creado', 'id' => $id];
    }

    public function actualizar(int $id, int $userId, array $data): array
    {
        $existing = $this->repo->find('productos', $id);
        if (!$existing || $existing['user_id'] != $userId) {
            return ['success' => false, 'message' => 'Producto no encontrado'];
        }

        $this->repo->update('productos', $id, [
            'codigo'             => trim($data['codigo'] ?? $existing['codigo']),
            'cod_producto_sunat' => trim($data['cod_producto_sunat'] ?? ''),
            'descripcion'        => trim($data['descripcion'] ?? $existing['descripcion']),
            'unidad'             => trim($data['unidad'] ?? 'NIU'),
            'precio_unitario'    => (float)($data['precio_unitario'] ?? $existing['precio_unitario']),
            'precio_compra'      => (float)($data['precio_compra'] ?? 0),
            'tip_afe_igv'        => trim($data['tip_afe_igv'] ?? '10'),
            'icbper'             => isset($data['icbper']) ? (float)$data['icbper'] : null,
            'factor_icbper'      => isset($data['factor_icbper']) ? (float)$data['factor_icbper'] : null,
            'categoria_id'       => !empty($data['categoria_id']) ? (int)$data['categoria_id'] : null,
            'stock_minimo'       => (float)($data['stock_minimo'] ?? 0),
        ]);

        return ['success' => true, 'message' => 'Producto actualizado'];
    }

    public function eliminar(int $id, int $userId): array
    {
        $existing = $this->repo->find('productos', $id);
        if (!$existing || $existing['user_id'] != $userId) {
            return ['success' => false, 'message' => 'Producto no encontrado'];
        }

        $this->repo->delete('productos', $id);
        return ['success' => true, 'message' => 'Producto eliminado'];
    }

    public function listarCategorias(): array
    {
        return $this->repoCategoria->listarTodas();
    }

    public function crearCategoria(string $nombre): array
    {
        if (!trim($nombre)) {
            return ['success' => false, 'message' => 'Nombre de categoría requerido'];
        }
        $id = $this->repoCategoria->crear(['nombre' => trim($nombre)]);
        return ['success' => true, 'message' => 'Categoría creada', 'id' => $id];
    }

    public function conStockBajo(int $userId, ?int $limite = null): array
    {
        return $this->repo->conStockBajo($userId, $limite);
    }
}
