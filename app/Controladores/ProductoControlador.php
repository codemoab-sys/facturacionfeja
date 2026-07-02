<?php
declare(strict_types=1);

namespace App\Controladores;

use App\Nucleo\Controlador;
use App\Nucleo\Sesion;
use App\Modelos\Producto;
use App\Modelos\Categoria;

class ProductoControlador extends Controlador
{
    public function index(array $params = []): void
    {
        $this->render('productos/index', ['pageTitle' => 'Productos']);
    }

    public function listar(array $params = []): void
    {
        $userId = Sesion::get('user_id');
        $buscar = $_GET['buscar'] ?? '';

        $modelo = new Producto();
        $items = $modelo->listarPorUsuario((int)$userId, $buscar);

        $this->json(['success' => true, 'data' => $items]);
    }

    public function obtener(array $params = []): void
    {
        $id = (int)($params[0] ?? 0);
        if (!$id) {
            $this->json(['success' => false, 'message' => 'ID requerido'], 400);
            return;
        }

        $modelo = new Producto();
        $item = $modelo->obtenerPorId($id);

        if (!$item) {
            $this->json(['success' => false, 'message' => 'Producto no encontrado'], 404);
            return;
        }

        $catModelo = new Categoria();
        $categorias = $catModelo->listarTodas();

        $this->json([
            'success' => true,
            'data'    => $item,
            'extra'   => ['categorias' => $categorias],
        ]);
    }

    public function guardar(array $params = []): void
    {
        $userId = Sesion::get('user_id');
        $request = $this->getRequest();

        $codigo       = trim($request->input('codigo', ''));
        $descripcion  = trim($request->input('descripcion', ''));
        $precio       = (float)($request->input('precio_unitario', 0));
        $unidad       = trim($request->input('unidad', 'NIU'));
        $tipAfeIgv    = trim($request->input('tip_afe_igv', '10'));
        $codSunat     = trim($request->input('cod_producto_sunat', ''));
        $categoriaId  = $request->input('categoria_id') ? (int)$request->input('categoria_id') : null;
        $icbper       = $request->input('icbper') ? (float)$request->input('icbper') : null;
        $factorIcbper = $request->input('factor_icbper') ? (float)$request->input('factor_icbper') : null;

        if (!$codigo || !$descripcion) {
            $this->json(['success' => false, 'message' => 'Código y descripción son requeridos'], 400);
            return;
        }

        $modelo = new Producto();
        $id = (int)($params[0] ?? 0);

        if ($id) {
            $existing = $modelo->obtenerPorId($id);
            if (!$existing || $existing['user_id'] != $userId) {
                $this->json(['success' => false, 'message' => 'Producto no encontrado'], 404);
                return;
            }
            $modelo->actualizar($id, [
                'codigo'             => $codigo,
                'cod_producto_sunat' => $codSunat,
                'descripcion'        => $descripcion,
                'unidad'             => $unidad,
                'precio_unitario'    => $precio,
                'tip_afe_igv'        => $tipAfeIgv,
                'icbper'             => $icbper,
                'factor_icbper'      => $factorIcbper,
                'categoria_id'       => $categoriaId,
            ]);
            $this->json(['success' => true, 'message' => 'Producto actualizado']);
        } else {
            $dup = $modelo->buscarPorCodigo((int)$userId, $codigo);
            if ($dup) {
                $this->json(['success' => false, 'message' => 'Ya existe un producto con ese código'], 400);
                return;
            }
            $modelo->crear([
                'user_id'            => $userId,
                'codigo'             => $codigo,
                'cod_producto_sunat' => $codSunat,
                'descripcion'        => $descripcion,
                'unidad'             => $unidad,
                'precio_unitario'    => $precio,
                'tip_afe_igv'        => $tipAfeIgv,
                'icbper'             => $icbper,
                'factor_icbper'      => $factorIcbper,
                'categoria_id'       => $categoriaId,
            ]);
            $this->json(['success' => true, 'message' => 'Producto creado']);
        }
    }

    public function eliminar(array $params = []): void
    {
        $userId = Sesion::get('user_id');
        $id = (int)($params[0] ?? 0);

        if (!$id) {
            $this->json(['success' => false, 'message' => 'ID requerido'], 400);
            return;
        }

        $modelo = new Producto();
        $item = $modelo->obtenerPorId($id);

        if (!$item || $item['user_id'] != $userId) {
            $this->json(['success' => false, 'message' => 'Producto no encontrado'], 404);
            return;
        }

        $modelo->eliminar($id);
        $this->json(['success' => true, 'message' => 'Producto eliminado']);
    }

    public function listarCategorias(array $params = []): void
    {
        $modelo = new Categoria();
        $items = $modelo->listarTodas();
        $this->json(['success' => true, 'data' => $items]);
    }
}
