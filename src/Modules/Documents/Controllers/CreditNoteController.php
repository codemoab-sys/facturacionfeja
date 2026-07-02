<?php
declare(strict_types=1);

namespace App\Modules\Documents\Controllers;

use App\Framework\Controller;
use App\Modules\Documents\Services\SunatApiService;

class CreditNoteController extends Controller
{
    public function create(array $params = []): void
    {
        $this->render('credit-notes/create', ['pageTitle' => 'Nueva Nota de Crédito']);
    }

    public function guardar(array $params = []): void
    {
        $api = new SunatApiService();
        $result = $api->post('/notas-credito', $this->request->all());
        $this->json($result);
    }

    public function index(array $params = []): void
    {
        $api = new SunatApiService();
        $query = '';
        if ($this->request->get('estado')) $query .= '?estado=' . urlencode((string)$this->request->get('estado'));
        if ($this->request->get('buscar')) $query .= (strpos($query, '?') === false ? '?' : '&') . 'buscar=' . urlencode((string)$this->request->get('buscar'));
        $result = $api->get('/notas-credito' . $query);
        $this->json($result);
    }
}
