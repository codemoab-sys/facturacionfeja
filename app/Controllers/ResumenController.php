<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\SunatApiService;

class ResumenController extends Controller
{
    public function index($params = [])
    {
        $this->render('summaries/index', ['pageTitle' => 'Resúmenes Diarios']);
    }

    public function guardar($params = [])
    {
        $api = new SunatApiService();
        $request = \App\Core\App::getInstance()->getRequest();
        $result = $api->post('/resumenes', $request->all());
        $this->json($result);
    }

    public function indexApi($params = [])
    {
        $api = new SunatApiService();
        $request = \App\Core\App::getInstance()->getRequest();
        $query = '';
        if ($request->get('estado')) $query .= '?estado=' . urlencode($request->get('estado'));
        if ($request->get('buscar')) $query .= (strpos($query, '?') === false ? '?' : '&') . 'buscar=' . urlencode($request->get('buscar'));
        $result = $api->get('/resumenes' . $query);
        $this->json($result);
    }

    public function estado($params)
    {
        $api = new SunatApiService();
        $result = $api->get('/resumenes/' . ($params['id'] ?? '') . '/estado');
        $this->json($result);
    }
}
