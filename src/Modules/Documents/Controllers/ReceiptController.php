<?php
declare(strict_types=1);

namespace App\Modules\Documents\Controllers;

use App\Framework\Controller;
use App\Modules\Documents\Services\SunatApiService;

class ReceiptController extends Controller
{
    public function create(array $params = []): void
    {
        $this->render('boletas/create', ['pageTitle' => 'Nueva Boleta']);
    }

    public function guardar(array $params = []): void
    {
        $api = new SunatApiService();
        $result = $api->post('/boletas', $this->request->all());
        $this->json($result);
    }

    public function index(array $params = []): void
    {
        $api = new SunatApiService();
        $query = '';
        if ($this->request->get('estado')) $query .= '?estado=' . urlencode((string)$this->request->get('estado'));
        if ($this->request->get('buscar')) $query .= (strpos($query, '?') === false ? '?' : '&') . 'buscar=' . urlencode((string)$this->request->get('buscar'));
        $result = $api->get('/boletas' . $query);
        $this->json($result);
    }

    public function mostrar(array $params): void
    {
        $api = new SunatApiService();
        $result = $api->get('/boletas/' . ($params['id'] ?? ''));
        $this->json($result);
    }
}
