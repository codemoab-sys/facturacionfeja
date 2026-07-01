<?php
declare(strict_types=1);

namespace App\Controladores;

use App\Nucleo\Controlador;
use App\Servicios\ServicioApiSunat;

class FacturaControlador extends Controlador
{
    public function create(array $params = []): void
    {
        $this->render('invoices/create', ['pageTitle' => 'Nueva Factura']);
    }

    public function guardar(array $params = []): void
    {
        $api = new ServicioApiSunat();
        $data = $this->request->all();
        $result = $api->post('/facturas', $data);
        $this->json($result);
    }

    public function index(array $params = []): void
    {
        $api = new ServicioApiSunat();
        $query = '';
        if ($this->request->get('estado')) $query .= (strpos($query, '?') === false ? '?' : '&') . 'estado=' . urlencode((string)$this->request->get('estado'));
        if ($this->request->get('buscar')) $query .= (strpos($query, '?') === false ? '?' : '&') . 'buscar=' . urlencode((string)$this->request->get('buscar'));
        $result = $api->get('/facturas' . $query);
        $this->json($result);
    }

    public function mostrar(array $params): void
    {
        $api = new ServicioApiSunat();
        $result = $api->get('/facturas/' . ($params['id'] ?? ''));
        $this->json($result);
    }
}
