<?php
declare(strict_types=1);

namespace App\Modules\Settings\Controllers;

use App\Framework\ApiController;
use App\Framework\Session;
use App\Modules\Settings\Services\ConfigService;
use App\Modules\Documents\Services\SunatApiService;

class CertificateController extends ApiController
{
    private ConfigService $configService;

    public function __construct(?ConfigService $configService = null)
    {
        parent::__construct();
        $this->configService = $configService ?? new ConfigService();
    }

    public function subir(): void
    {
        if (!isset($_FILES['certificado']) || $_FILES['certificado']['error'] !== UPLOAD_ERR_OK) {
            $this->error('No se recibió el archivo de certificado.', 400);
        }

        $certDir = __DIR__ . '/../../../../storage/certificates';
        if (!is_dir($certDir)) @mkdir($certDir, 0755, true);

        $ext = strtolower(pathinfo($_FILES['certificado']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['p12', 'pfx', 'pem'])) {
            $this->error('Formato no válido. Solo .p12, .pfx o .pem.', 400);
        }

        $destino = $certDir . '/cert_' . $this->userId . '.' . $ext;
        move_uploaded_file($_FILES['certificado']['tmp_name'], $destino);

        $contrasena = $this->input('contrasena_certificado', '');
        Session::set('certificado', [
            'ruta'       => $destino,
            'password'   => $contrasena,
            'tiene_cert' => true,
        ]);

        if ($this->configService->hasConfig()) {
            $api = new SunatApiService($this->configService->getSunatApiConfig());
            try {
                $result = $api->uploadCertificado($destino, $contrasena);
                $this->json(array_merge(['debug_local' => 'reenviado a API remota'], $result));
                return;
            } catch (\Exception $e) {
                $this->error('Error al enviar a la API remota: ' . $e->getMessage(), 500, ['debug' => $e->getMessage()]);
                return;
            }
        }

        $this->success(null, 'Certificado guardado localmente.');
    }

    public function eliminar(): void
    {
        $cert = Session::get('certificado', []);
        if (!empty($cert['ruta']) && file_exists($cert['ruta'])) {
            @unlink($cert['ruta']);
        }
        Session::remove('certificado');

        if ($this->configService->hasConfig()) {
            $api = new SunatApiService($this->configService->getSunatApiConfig());
            try { $api->delete('/empresa/certificado'); } catch (\Exception $e) {}
        }

        $this->success(null, 'Certificado eliminado.');
    }

    public function estado(): void
    {
        $cert = Session::get('certificado', []);
        $tiene = !empty($cert['tiene_cert']) && !empty($cert['ruta']) && file_exists($cert['ruta']);
        $this->success([
            'tiene_certificado' => $tiene,
            'archivo'           => $tiene ? basename($cert['ruta']) : null,
        ]);
    }
}
