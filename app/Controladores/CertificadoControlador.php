<?php
declare(strict_types=1);

namespace App\Controladores;

use App\Nucleo\Controlador;
use App\Nucleo\Sesion;
use App\Servicios\ServicioConfiguracion;
use App\Servicios\ServicioApiSunat;

class CertificadoControlador extends Controlador
{
    private ServicioConfiguracion $configService;

    public function __construct(?ServicioConfiguracion $configService = null)
    {
        $this->configService = $configService ?? new ServicioConfiguracion();
    }

    public function subir(): void
    {
        if (!isset($_FILES['certificado']) || $_FILES['certificado']['error'] !== UPLOAD_ERR_OK) {
            $this->json(['success' => false, 'message' => 'No se recibió el archivo de certificado.'], 400);
            return;
        }

        $certDir = __DIR__ . '/../../storage/certificates';
        if (!is_dir($certDir)) {
            @mkdir($certDir, 0755, true);
        }

        $ext = strtolower(pathinfo($_FILES['certificado']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['p12', 'pfx', 'pem'])) {
            $this->json(['success' => false, 'message' => 'Formato no válido. Solo .p12, .pfx o .pem.'], 400);
            return;
        }

        $userId = Sesion::get('user_id');
        $destino = $certDir . '/cert_' . $userId . '.' . $ext;
        move_uploaded_file($_FILES['certificado']['tmp_name'], $destino);

        $contrasena = \App\Nucleo\App::getInstance()->getRequest()->post('contrasena_certificado', '');

        Sesion::set('certificado', [
            'ruta'       => $destino,
            'password'   => $contrasena,
            'tiene_cert' => true,
        ]);

        if ($this->configService->hasConfig()) {
            $api = new ServicioApiSunat($this->configService->getSunatApiConfig());
            try {
                $result = $api->uploadCertificado($destino, $contrasena);
                $this->json(array_merge(['debug_local' => 'reenviado a API remota'], $result));
                return;
            } catch (\Exception $e) {
                $this->json([
                    'success' => false,
                    'message' => 'Error al enviar a la API remota: ' . $e->getMessage(),
                    'debug'   => $e->getMessage(),
                ]);
                return;
            }
        }

        $this->json(['success' => true, 'message' => 'Certificado guardado localmente.']);
    }

    public function eliminar(): void
    {
        $cert = Sesion::get('certificado', []);
        if (!empty($cert['ruta']) && file_exists($cert['ruta'])) {
            @unlink($cert['ruta']);
        }
        Sesion::remove('certificado');

        if ($this->configService->hasConfig()) {
            $api = new ServicioApiSunat($this->configService->getSunatApiConfig());
            try {
                $result = $api->delete('/empresa/certificado');
                $this->json($result);
                return;
            } catch (\Exception $e) {}
        }

        $this->json(['success' => true, 'message' => 'Certificado eliminado.']);
    }

    public function estado(): void
    {
        $cert = Sesion::get('certificado', []);
        $tiene = !empty($cert['tiene_cert']) && !empty($cert['ruta']) && file_exists($cert['ruta']);
        $this->json([
            'success' => true,
            'data'    => [
                'tiene_certificado' => $tiene,
                'archivo'           => $tiene ? basename($cert['ruta']) : null,
            ],
        ]);
    }
}
