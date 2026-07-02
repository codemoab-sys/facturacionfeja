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

    public function listarDemo(array $params = []): void
    {
        $userId = Sesion::get('user_id');
        if ($userId) {
            $modelo = new Cliente();
            $items = $modelo->listarPorUsuario($userId);
            if (!empty($items)) {
                $this->json(['success' => true, 'data' => $items]);
                return;
            }
        }
        // Fallback si no hay clientes en BD
        $clientes = $this->demoData();
        $this->json(['success' => true, 'data' => $clientes]);
    }

    public function buscarDocumento(array $params = []): void
    {
        $api = new \App\Servicios\ServicioApiSunat();
        $tipo   = $this->getRequest()->get('tipo', '6');
        $numero = $this->getRequest()->get('numero', '');
        $result = $api->get('/buscar-documento?tipo=' . urlencode((string)$tipo) . '&numero=' . urlencode((string)$numero));
        $this->json($result);
    }

    private function demoData(): array
    {
        return [
            ['tipo_doc' => '6', 'num_doc' => '20555666777', 'razon_social' => 'ACME CORPORATION SAC', 'direccion' => 'AV. LARCO 1234 - MIRAFLORES', 'email' => 'facturas@acme.com'],
            ['tipo_doc' => '6', 'num_doc' => '20111222333', 'razon_social' => 'DISTRIBUIDORA LIMA EIRL', 'direccion' => 'JR. COMERCIO 456 - LIMA', 'email' => 'compras@distribuidoralima.pe'],
            ['tipo_doc' => '6', 'num_doc' => '20444555666', 'razon_social' => 'TECNOLOGIA ANDINA SA', 'direccion' => 'AV. PRINCIPAL 789 - SAN ISIDRO', 'email' => 'facturacion@tecnologiaandina.pe'],
            ['tipo_doc' => '6', 'num_doc' => '20333777888', 'razon_social' => 'CONSTRUCTORA NORTE SAC', 'direccion' => 'AV. DEL NORTE 321 - TRUJILLO', 'email' => 'admin@constructoranorte.pe'],
            ['tipo_doc' => '6', 'num_doc' => '20666999000', 'razon_social' => 'LOGISTICA PERU EIRL', 'direccion' => 'CALLE LOS OLIVOS 555 - CALLAO', 'email' => 'logistica@logisticaperu.pe'],
            ['tipo_doc' => '6', 'num_doc' => '20123456789', 'razon_social' => 'INVERSIONES DEL SUR SAC', 'direccion' => 'AV. SOL 1000 - AREQUIPA', 'email' => 'contacto@inversionessur.pe'],
            ['tipo_doc' => '1', 'num_doc' => '12345678', 'razon_social' => 'JUAN PEREZ GARCIA', 'direccion' => 'JR. UNION 200 - LIMA', 'email' => 'jperez@email.com'],
            ['tipo_doc' => '1', 'num_doc' => '87654321', 'razon_social' => 'MARIA LOPEZ HUAMAN', 'direccion' => 'AV. BOLIVAR 456 - CALLAO', 'email' => 'mlopez@email.com'],
        ];
    }
}
