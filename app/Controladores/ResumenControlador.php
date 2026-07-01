<?php
declare(strict_types=1);

namespace App\Controladores;

use App\Nucleo\Controlador;
use App\Servicios\ServicioApiSunat;

class ResumenControlador extends Controlador
{
    public function index(array $params = []): void
    {
        $this->render('summaries/index', ['pageTitle' => 'Resúmenes Diarios']);
    }

    public function guardar(array $params = []): void
    {
        $api = new ServicioApiSunat();
        $result = $api->post('/resumenes', $this->request->all());
        $this->json($result);
    }

    public function indexApi(array $params = []): void
    {
        $api = new ServicioApiSunat();
        $query = '';
        if ($this->request->get('estado')) $query .= '?estado=' . urlencode((string)$this->request->get('estado'));
        if ($this->request->get('buscar')) $query .= (strpos($query, '?') === false ? '?' : '&') . 'buscar=' . urlencode((string)$this->request->get('buscar'));
        $result = $api->get('/resumenes' . $query);
        $this->json($result);
    }

    public function estado(array $params): void
    {
        $api = new ServicioApiSunat();
        $result = $api->get('/resumenes/' . ($params['id'] ?? '') . '/estado');
        $this->json($result);
    }
}
