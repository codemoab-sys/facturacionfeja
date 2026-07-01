<?php
declare(strict_types=1);

namespace App\Controladores;

use App\Nucleo\Controlador;
use App\Servicios\ServicioApiSunat;

class ClienteControlador extends Controlador
{
    public function listar(array $params = []): void
    {
        $api = new ServicioApiSunat();
        $buscar = $this->request->get('buscar', '');
        $result = $api->get('/clientes?buscar=' . urlencode((string)$buscar));
        $this->json($result);
    }

    public function buscarDocumento(array $params = []): void
    {
        $api = new ServicioApiSunat();
        $tipo   = $this->request->get('tipo', '6');
        $numero = $this->request->get('numero', '');
        $result = $api->get('/buscar-documento?tipo=' . urlencode((string)$tipo) . '&numero=' . urlencode((string)$numero));
        $this->json($result);
    }

    public function listarDemo(array $params = []): void
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
