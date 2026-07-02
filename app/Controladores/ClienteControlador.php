<?php
declare(strict_types=1);

namespace App\Controladores;

use App\Nucleo\Controlador;
use App\Nucleo\Sesion;
use App\Modelos\Cliente;

class ClienteControlador extends Controlador
{
    public function index(array $params = []): void
    {
        $this->render('clientes/index', ['pageTitle' => 'Clientes']);
    }

    public function listar(array $params = []): void
    {
        $userId = Sesion::get('user_id');
        $buscar = $_GET['buscar'] ?? '';

        $modelo = new Cliente();
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

        $modelo = new Cliente();
        $item = $modelo->obtenerPorId($id);

        if (!$item) {
            $this->json(['success' => false, 'message' => 'Cliente no encontrado'], 404);
            return;
        }

        $this->json(['success' => true, 'data' => $item]);
    }

    public function guardar(array $params = []): void
    {
        $userId = Sesion::get('user_id');
        $request = $this->getRequest();

        $tipoDoc    = trim($request->input('tipo_doc', '6'));
        $numDoc     = trim($request->input('num_doc', ''));
        $razonSocial = trim($request->input('razon_social', ''));
        $direccion  = trim($request->input('direccion', ''));
        $email      = trim($request->input('email', ''));

        if (!$numDoc || !$razonSocial) {
            $this->json(['success' => false, 'message' => 'Documento y raz\u00f3n social son requeridos'], 400);
            return;
        }

        $modelo = new Cliente();
        $id = (int)($params[0] ?? 0);

        if ($id) {
            $existing = $modelo->obtenerPorId($id);
            if (!$existing || $existing['user_id'] != $userId) {
                $this->json(['success' => false, 'message' => 'Cliente no encontrado'], 404);
                return;
            }
            $modelo->actualizar($id, [
                'tipo_doc'    => $tipoDoc,
                'num_doc'     => $numDoc,
                'razon_social' => $razonSocial,
                'direccion'   => $direccion,
                'email'       => $email,
            ]);
            $this->json(['success' => true, 'message' => 'Cliente actualizado']);
        } else {
            $dup = $modelo->buscarPorDoc((int)$userId, $numDoc);
            if ($dup) {
                $this->json(['success' => false, 'message' => 'Ya existe un cliente con ese documento'], 400);
                return;
            }
            $modelo->crear([
                'user_id'     => $userId,
                'tipo_doc'    => $tipoDoc,
                'num_doc'     => $numDoc,
                'razon_social' => $razonSocial,
                'direccion'   => $direccion,
                'email'       => $email,
            ]);
            $this->json(['success' => true, 'message' => 'Cliente creado']);
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

        $modelo = new Cliente();
        $item = $modelo->obtenerPorId($id);

        if (!$item || $item['user_id'] != $userId) {
            $this->json(['success' => false, 'message' => 'Cliente no encontrado'], 404);
            return;
        }

        $modelo->eliminar($id);
        $this->json(['success' => true, 'message' => 'Cliente eliminado']);
    }

    public function buscarDocumento(array $params = []): void
    {
        $api = new \App\Servicios\ServicioApiSunat();
        $tipo   = $this->getRequest()->get('tipo', '6');
        $numero = $this->getRequest()->get('numero', '');
        $result = $api->get('/buscar-documento?tipo=' . urlencode((string)$tipo) . '&numero=' . urlencode((string)$numero));
        $this->json($result);
    }
}
