<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\SunatApiService;

class BoletaController extends Controller
{
    public function create($params = [])
    {
        $this->render('boletas/create', ['pageTitle' => 'Nueva Boleta']);
    }

    public function store($params = [])
    {
        $api = new SunatApiService();
        $request = \App\Core\App::getInstance()->getRequest();
        $result = $api->post('/boletas', $request->all());
        $this->json($result);
    }

    public function index($params = [])
    {
        $api = new SunatApiService();
        $request = \App\Core\App::getInstance()->getRequest();
        $query = '';
        if ($request->get('estado')) $query .= '?estado=' . urlencode($request->get('estado'));
        if ($request->get('buscar')) $query .= (strpos($query, '?') === false ? '?' : '&') . 'buscar=' . urlencode($request->get('buscar'));
        $result = $api->get('/boletas' . $query);
        $this->json($result);
    }

    public function show($params)
    {
        $api = new SunatApiService();
        $result = $api->get('/boletas/' . ($params['id'] ?? ''));
        $this->json($result);
    }
}
