<?php
declare(strict_types=1);

namespace App\Controladores;

use App\Nucleo\Controlador;
use App\Servicios\ServicioApiSunat;

class DocumentoControlador extends Controlador
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
        $api = new ServicioApiSunat();
        $query = $this->request->get('tipo') ? '?tipo=' . urlencode((string)$this->request->get('tipo')) : '';
        $result = $api->get('/series' . $query);
        $this->json($result);
    }

    public function descargarPdf(array $params): void
    {
        $tipo = $params['tipo'] ?? '';
        $id   = $params['id'] ?? '';
        $format = $_GET['format'] ?? 'a4';
        $this->download($tipo, $id, 'pdf', $format);
    }

    public function descargarXml(array $params): void
    {
        $tipo = $params['tipo'] ?? '';
        $id   = $params['id'] ?? '';
        $this->download($tipo, $id, 'xml');
    }

    public function descargarCdr(array $params): void
    {
        $tipo = $params['tipo'] ?? '';
        $id   = $params['id'] ?? '';
        $this->download($tipo, $id, 'cdr');
    }

    private function download(string $tipo, string $id, string $kind, ?string $format = null): void
    {
        $apiType = $this->tipoMap[$tipo] ?? 'facturas';
        $path = '/' . $apiType . '/' . $id . '/' . $kind;
        if ($kind === 'pdf' && $format) {
            $path .= '?format=' . urlencode($format);
        }

        $api = new ServicioApiSunat();
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
            header('Content-Length: ' . strlen((string)$result['data']));
            echo $result['data'];
            exit;
        }

        $this->json($result);
    }
}
