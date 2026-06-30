<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\SunatApiService;

class FacturaController extends Controller
{
    public function create($params = [])
    {
        $this->render('invoices/create', ['pageTitle' => 'Nueva Factura']);
    }

    public function guardar($params = [])
    {
        $api = new SunatApiService();
        $request = \App\Core\App::getInstance()->getRequest();
        $data = $request->all();
        $result = $api->post('/facturas', $data);
        $this->json($result);
    }

    public function index($params = [])
    {
        $api = new SunatApiService();
        $request = \App\Core\App::getInstance()->getRequest();
        $query = '';
        if ($request->get('estado')) $query .= (strpos($query, '?') === false ? '?' : '&') . 'estado=' . urlencode($request->get('estado'));
        if ($request->get('buscar')) $query .= (strpos($query, '?') === false ? '?' : '&') . 'buscar=' . urlencode($request->get('buscar'));
        $result = $api->get('/facturas' . $query);
        $this->json($result);
    }

    public function mostrar($params)
    {
        $api = new SunatApiService();
        $result = $api->get('/facturas/' . ($params['id'] ?? ''));
        $this->json($result);
    }
}
