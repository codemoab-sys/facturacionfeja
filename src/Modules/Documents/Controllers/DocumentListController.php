<?php
declare(strict_types=1);

namespace App\Modules\Documents\Controllers;

use App\Framework\Controller;
use App\Modules\Documents\Services\SunatApiService;

class DocumentListController extends Controller
{
    private array $tipoMap = [
        'facturas'        => 'facturas',
        'boletas'         => 'boletas',
        'notas-credito'   => 'notas-credito',
        'notas-debito'    => 'notas-debito',
        'guias-remision'  => 'guias-remision',
    ];

    public function index(array $params = []): void
    {
        $tipo = $params['tipo'] ?? 'facturas';
        $this->render('documents/list', [
            'pageTitle' => 'Documentos',
            'tipo'      => $tipo,
        ]);
    }

    public function procesarSeries(array $params = []): void
    {
        $api = new SunatApiService();
        $query = $this->request->get('tipo') ? '?tipo=' . urlencode((string)$this->request->get('tipo')) : '';
        $result = $api->get('/series' . $query);
        $this->json($result);
    }

    public function descargarPdf(array $params): void
    {
        $this->download($params['tipo'] ?? '', $params['id'] ?? '', 'pdf', $_GET['format'] ?? 'a4');
    }

    public function descargarXml(array $params): void
    {
        $this->download($params['tipo'] ?? '', $params['id'] ?? '', 'xml');
    }

    public function descargarCdr(array $params): void
    {
        $this->download($params['tipo'] ?? '', $params['id'] ?? '', 'cdr');
    }

    private function download(string $tipo, string $id, string $kind, ?string $format = null): void
    {
        $apiType = $this->tipoMap[$tipo] ?? 'facturas';
        $path = '/' . $apiType . '/' . $id . '/' . $kind;
        if ($kind === 'pdf' && $format) $path .= '?format=' . urlencode($format);

        $api = new SunatApiService();
        $result = $api->get($path);

        if (isset($result['isBinary']) && $result['isBinary']) {
            $mimeTypes = ['pdf' => 'application/pdf', 'xml' => 'application/xml', 'cdr' => 'application/zip'];
            $extensions = ['pdf' => 'pdf', 'xml' => 'xml', 'cdr' => 'zip'];
            header('Content-Type: ' . ($mimeTypes[$kind] ?? 'application/octet-stream'));
            header('Content-Disposition: attachment; filename="' . $apiType . '-' . $id . '.' . ($extensions[$kind] ?? 'bin') . '"');
            header('Content-Length: ' . strlen((string)$result['data']));
            echo $result['data'];
            exit;
        }

        $this->json($result);
    }
}
