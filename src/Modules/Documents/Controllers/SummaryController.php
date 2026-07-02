<?php
declare(strict_types=1);

namespace App\Modules\Documents\Controllers;

use App\Framework\Controller;
use App\Modules\Documents\Services\SunatApiService;

class SummaryController extends Controller
{
    public function index(array $params = []): void
    {
        $this->render('summaries/index', ['pageTitle' => 'Resúmenes Diarios']);
    }

    public function guardar(array $params = []): void
    {
        $api = new SunatApiService();
        $result = $api->post('/resumenes', $this->request->all());
        $this->json($result);
    }

    public function indexApi(array $params = []): void
    {
        $api = new SunatApiService();
        $query = '';
        if ($this->request->get('estado')) $query .= '?estado=' . urlencode((string)$this->request->get('estado'));
        if ($this->request->get('buscar')) $query .= (strpos($query, '?') === false ? '?' : '&') . 'buscar=' . urlencode((string)$this->request->get('buscar'));
        $result = $api->get('/resumenes' . $query);
        $this->json($result);
    }

    public function estado(array $params): void
    {
        $api = new SunatApiService();
        $result = $api->get('/resumenes/' . ($params['id'] ?? '') . '/estado');
        $this->json($result);
    }
}
