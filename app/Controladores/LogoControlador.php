<?php
declare(strict_types=1);

namespace App\Controladores;

use App\Nucleo\Controlador;
use App\Nucleo\Sesion;
use App\Servicios\ServicioConfiguracion;
use App\Servicios\ServicioApiSunat;

class LogoControlador extends Controlador
{
    private ServicioConfiguracion $configService;

    public function __construct(?ServicioConfiguracion $configService = null)
    {
        $this->configService = $configService ?? new ServicioConfiguracion();
    }

    public function subir(): void
    {
        if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
            $this->json(['success' => false, 'message' => 'No se recibió el archivo de logo.'], 400);
            return;
        }

        $logoDir = __DIR__ . '/../../storage/logos';
        if (!is_dir($logoDir)) {
            @mkdir($logoDir, 0755, true);
        }

        $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'svg'])) {
            $this->json(['success' => false, 'message' => 'Formato no válido. Solo .png, .jpg, .gif, .svg.'], 400);
            return;
        }

        $userId = Sesion::get('user_id');
        $destino = $logoDir . '/logo_' . $userId . '.' . $ext;
        move_uploaded_file($_FILES['logo']['tmp_name'], $destino);

        Sesion::set('logo', [
            'ruta'      => $destino,
            'tiene_logo' => true,
            'ext'       => $ext,
        ]);

        if ($this->configService->hasConfig()) {
            $api = new ServicioApiSunat($this->configService->getSunatApiConfig());
            try {
                $result = $api->uploadLogo($destino);
                $this->json(array_merge(['success' => true, 'message' => 'Logo subido correctamente'], $result));
                return;
            } catch (\Exception $e) {
                $this->json([
                    'success' => false,
                    'message' => 'Error al enviar logo a la API remota: ' . $e->getMessage(),
                    'debug'   => $e->getMessage(),
                ]);
                return;
            }
        }

        $this->json(['success' => true, 'message' => 'Logo guardado localmente.']);
    }

    public function eliminar(): void
    {
        $logo = Sesion::get('logo', []);
        if (!empty($logo['ruta']) && file_exists($logo['ruta'])) {
            @unlink($logo['ruta']);
        }
        Sesion::remove('logo');

        if ($this->configService->hasConfig()) {
            $api = new ServicioApiSunat($this->configService->getSunatApiConfig());
            try {
                $api->delete('/empresa/logo');
            } catch (\Exception $e) {}
        }

        $this->json(['success' => true, 'message' => 'Logo eliminado.']);
    }

    public function estado(): void
    {
        $logo = Sesion::get('logo', []);
        $tiene = !empty($logo['tiene_logo']) && !empty($logo['ruta']) && file_exists($logo['ruta']);
        $this->json([
            'success' => true,
            'data'    => [
                'tiene_logo' => $tiene,
                'ext'        => $tiene ? $logo['ext'] : null,
            ],
        ]);
    }

    public function imagen(): void
    {
        $logo = Sesion::has('user_id') ? Sesion::get('logo', []) : [];
        if (!empty($logo['tiene_logo']) && !empty($logo['ruta']) && file_exists($logo['ruta'])) {
            $mime = [
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'gif' => 'image/gif',
                'svg' => 'image/svg+xml',
            ];
            $ext = $logo['ext'];
            header('Content-Type: ' . ($mime[$ext] ?? 'application/octet-stream'));
            header('Cache-Control: public, max-age=86400');
            readfile($logo['ruta']);
            exit;
        }

        $default = __DIR__ . '/../../public/img/logogeneral.png';
        if (file_exists($default)) {
            header('Content-Type: image/png');
            header('Cache-Control: public, max-age=86400');
            readfile($default);
            exit;
        }

        http_response_code(404);
        exit;
    }
}
