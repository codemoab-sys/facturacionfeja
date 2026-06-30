<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\SunatApiService;

class PanelController extends Controller
{
    public function index($params = [])
    {
        $this->render('dashboard', ['pageTitle' => 'Inicio']);
    }

    public function procesar($method, $ignored)
    {
        $api = new SunatApiService();
        $request = \App\Core\App::getInstance()->getRequest();
        $query = '';

        if ($method === 'listSucursales') {
            $result = $api->get('/sucursales');
        } elseif ($method === 'getEmpresa') {
            $result = $api->get('/empresa');
        } elseif ($method === 'panelIndicadores') {
            $result = $api->get('/panel/indicadores');
        } elseif ($method === 'panelDocumentosRecientes') {
            $result = $api->get('/panel/documentos-recientes');
        } elseif ($method === 'panelVentasMensuales') {
            $result = $api->get('/panel/ventas-mensuales');
        } elseif ($method === 'panelEstadoSunat') {
            $result = $api->get('/panel/estado-sunat');
        } elseif ($method === 'panelPorMoneda') {
            $result = $api->get('/panel/por-moneda');
        } else {
            $this->json(['success' => false, 'message' => 'Unknown method'], 400);
            return;
        }

        $this->json($result);
    }
}
