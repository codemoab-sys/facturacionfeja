<?php
declare(strict_types=1);

namespace App\Modules\Products\Services;

use App\Modules\Products\Repositories\ProductRepository;
use App\Modules\Products\Repositories\CategoryRepository;

class ProductService
{
    private ProductRepository $productRepo;
    private CategoryRepository $categoryRepo;

    public function __construct(
        ?ProductRepository $productRepo = null,
        ?CategoryRepository $categoryRepo = null
    ) {
        $this->productRepo = $productRepo ?? new ProductRepository();
        $this->categoryRepo = $categoryRepo ?? new CategoryRepository();
    }

    public function listar(int $userId, string $buscar = ''): array
    {
        return $this->productRepo->listarPorUsuario($userId, $buscar);
    }

    public function obtener(int $id, ?int $userId = null): ?array
    {
        $item = $this->productRepo->findById($id);
        if ($item && $userId !== null && $item['user_id'] != $userId) {
            return null;
        }
        return $item;
    }

    public function guardar(int $userId, int $id, array $data): array
    {
        $codigo = trim($data['codigo'] ?? '');
        $descripcion = trim($data['descripcion'] ?? '');

        if (!$codigo || !$descripcion) {
            return ['success' => false, 'message' => 'Código y descripción son requeridos'];
        }

        $fields = [
            'codigo'             => $codigo,
            'cod_producto_sunat' => trim($data['cod_producto_sunat'] ?? ''),
            'descripcion'        => $descripcion,
            'unidad'             => trim($data['unidad'] ?? 'NIU'),
            'precio_unitario'    => (float)($data['precio_unitario'] ?? 0),
            'tip_afe_igv'        => trim($data['tip_afe_igv'] ?? '10'),
            'icbper'             => isset($data['icbper']) ? (float)$data['icbper'] : null,
            'factor_icbper'      => isset($data['factor_icbper']) ? (float)$data['factor_icbper'] : null,
            'categoria_id'       => isset($data['categoria_id']) ? (int)$data['categoria_id'] : null,
        ];

        if ($id) {
            $existing = $this->productRepo->findById($id);
            if (!$existing || $existing['user_id'] != $userId) {
                return ['success' => false, 'message' => 'Producto no encontrado'];
            }
            $fields['updated_at'] = date('Y-m-d H:i:s');
            $this->productRepo->updateProduct($id, $fields);
            return ['success' => true, 'message' => 'Producto actualizado'];
        }

        $dup = $this->productRepo->buscarPorCodigo($userId, $codigo);
        if ($dup) {
            return ['success' => false, 'message' => 'Ya existe un producto con ese código'];
        }

        $fields['user_id'] = $userId;
        $fields['created_at'] = date('Y-m-d H:i:s');
        $this->productRepo->createProduct($fields);
        return ['success' => true, 'message' => 'Producto creado'];
    }

    public function eliminar(int $id, int $userId): array
    {
        $item = $this->productRepo->findById($id);
        if (!$item || $item['user_id'] != $userId) {
            return ['success' => false, 'message' => 'Producto no encontrado'];
        }
        $this->productRepo->deleteProduct($id);
        return ['success' => true, 'message' => 'Producto eliminado'];
    }

    public function listarCategorias(): array
    {
        return $this->categoryRepo->listarTodas();
    }
}
