<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\SunatApiService;

class DispatchGuideController extends Controller
{
    public function create($params = [])
    {
        $this->render('dispatch-guides/create', ['pageTitle' => 'Nueva Guía de Remisión']);
    }

    public function store($params = [])
    {
        $api = new SunatApiService();
        $request = \App\Core\App::getInstance()->getRequest();
        $result = $api->post('/guias-remision', $request->all());
        $this->json($result);
    }

    public function index($params = [])
    {
        $api = new SunatApiService();
        $request = \App\Core\App::getInstance()->getRequest();
        $query = '';
        if ($request->get('estado')) $query .= '?estado=' . urlencode($request->get('estado'));
        if ($request->get('buscar')) $query .= (strpos($query, '?') === false ? '?' : '&') . 'buscar=' . urlencode($request->get('buscar'));
        $result = $api->get('/guias-remision' . $query);
        $this->json($result);
    }
}
