<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\SunatApiService;

class NotaCreditoController extends Controller
{
    public function create($params = [])
    {
        $this->render('credit-notes/create', ['pageTitle' => 'Nueva Nota de Crédito']);
    }

    public function guardar($params = [])
    {
        $api = new SunatApiService();
        $request = \App\Core\App::getInstance()->getRequest();
        $result = $api->post('/notas-credito', $request->all());
        $this->json($result);
    }

    public function index($params = [])
    {
        $api = new SunatApiService();
        $request = \App\Core\App::getInstance()->getRequest();
        $query = '';
        if ($request->get('estado')) $query .= '?estado=' . urlencode($request->get('estado'));
        if ($request->get('buscar')) $query .= (strpos($query, '?') === false ? '?' : '&') . 'buscar=' . urlencode($request->get('buscar'));
        $result = $api->get('/notas-credito' . $query);
        $this->json($result);
    }
}
