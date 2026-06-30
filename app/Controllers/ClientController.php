<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\SunatApiService;

class ClientController extends Controller
{
    public function list($params = [])
    {
        $api = new SunatApiService();
        $request = \App\Core\App::getInstance()->getRequest();
        $buscar = $request->get('buscar', '');
        $result = $api->get('/clientes?buscar=' . urlencode($buscar));
        $this->json($result);
    }

    public function buscarDocumento($params = [])
    {
        $api = new SunatApiService();
        $request = \App\Core\App::getInstance()->getRequest();
        $tipo   = $request->get('tipo', '6');
        $numero = $request->get('numero', '');
        $result = $api->get('/buscar-documento?tipo=' . urlencode($tipo) . '&numero=' . urlencode($numero));
        $this->json($result);
    }

    public function demoList($params = [])
    {
        $clientes = [
            ['tipo_doc' => '6', 'num_doc' => '20555666777', 'razon_social' => 'ACME CORPORATION SAC', 'direccion' => 'AV. LARCO 1234 - MIRAFLORES', 'email' => 'facturas@acme.com'],
            ['tipo_doc' => '6', 'num_doc' => '20111222333', 'razon_social' => 'DISTRIBUIDORA LIMA EIRL', 'direccion' => 'JR. COMERCIO 456 - LIMA', 'email' => 'compras@distribuidoralima.pe'],
            ['tipo_doc' => '6', 'num_doc' => '20444555666', 'razon_social' => 'TECNOLOGIA ANDINA SA', 'direccion' => 'AV. PRINCIPAL 789 - SAN ISIDRO', 'email' => 'facturacion@tecnologiaandina.pe'],
            ['tipo_doc' => '6', 'num_doc' => '20333777888', 'razon_social' => 'CONSTRUCTORA NORTE SAC', 'direccion' => 'AV. DEL NORTE 321 - TRUJILLO', 'email' => 'admin@constructoranorte.pe'],
            ['tipo_doc' => '6', 'num_doc' => '20666999000', 'razon_social' => 'LOGISTICA PERU EIRL', 'direccion' => 'CALLE LOS OLIVOS 555 - CALLAO', 'email' => 'logistica@logisticaperu.pe'],
            ['tipo_doc' => '6', 'num_doc' => '20123456789', 'razon_social' => 'INVERSIONES DEL SUR SAC', 'direccion' => 'AV. SOL 1000 - AREQUIPA', 'email' => 'contacto@inversionessur.pe'],
            ['tipo_doc' => '1', 'num_doc' => '12345678', 'razon_social' => 'JUAN PEREZ GARCIA', 'direccion' => 'JR. UNION 200 - LIMA', 'email' => 'jperez@email.com'],
            ['tipo_doc' => '1', 'num_doc' => '87654321', 'razon_social' => 'MARIA LOPEZ HUAMAN', 'direccion' => 'AV. BOLIVAR 456 - CALLAO', 'email' => 'mlopez@email.com'],
        ];
        $this->json(['success' => true, 'data' => $clientes]);
    }
}
