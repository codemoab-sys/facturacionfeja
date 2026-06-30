<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\SunatApiService;

class DocumentController extends Controller
{
    private $tipoMap = [
        'facturas'        => 'facturas',
        'boletas'         => 'boletas',
        'notas-credito'   => 'notas-credito',
        'notas-debito'    => 'notas-debito',
        'guias-remision'  => 'guias-remision',
    ];

    public function index($params = [])
    {
        $tipo = $params['tipo'] ?? 'facturas';
        $this->render('documents/list', [
            'pageTitle' => 'Documentos',
            'tipo'      => $tipo,
        ]);
    }

    public function proxySeries($params = [])
    {
        $api = new SunatApiService();
        $request = \App\Core\App::getInstance()->getRequest();
        $query = $request->get('tipo') ? '?tipo=' . urlencode($request->get('tipo')) : '';
        $result = $api->get('/series' . $query);
        $this->json($result);
    }

    public function downloadPdf($params)
    {
        $tipo = $params['tipo'] ?? '';
        $id   = $params['id'] ?? '';
        $format = $_GET['format'] ?? 'a4';
        $this->download($tipo, $id, 'pdf', $format);
    }

    public function downloadXml($params)
    {
        $tipo = $params['tipo'] ?? '';
        $id   = $params['id'] ?? '';
        $this->download($tipo, $id, 'xml');
    }

    public function downloadCdr($params)
    {
        $tipo = $params['tipo'] ?? '';
        $id   = $params['id'] ?? '';
        $this->download($tipo, $id, 'cdr');
    }

    private function download($tipo, $id, $kind, $format = null)
    {
        $apiType = $this->tipoMap[$tipo] ?? 'facturas';
        $path = '/' . $apiType . '/' . $id . '/' . $kind;
        if ($kind === 'pdf' && $format) {
            $path .= '?format=' . urlencode($format);
        }

        $api = new SunatApiService();
        $result = $api->get($path);

        if (isset($result['isBinary']) && $result['isBinary']) {
            $mimeTypes = [
                'pdf' => 'application/pdf',
                'xml' => 'application/xml',
                'cdr' => 'application/zip',
            ];
            $extensions = [
                'pdf' => 'pdf',
                'xml' => 'xml',
                'cdr' => 'zip',
            ];
            header('Content-Type: ' . ($mimeTypes[$kind] ?? 'application/octet-stream'));
            header('Content-Disposition: attachment; filename="' . $apiType . '-' . $id . '.' . ($extensions[$kind] ?? 'bin') . '"');
            header('Content-Length: ' . strlen($result['data']));
            echo $result['data'];
            exit;
        }

        $this->json($result);
    }
}
