<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\ConfiguracionUsuario;

class ConfiguracionController extends Controller
{
    public function index($params = [])
    {
        $cfg = Session::get('api_config', []);
        $this->render('settings', [
            'pageTitle' => 'Configuración',
            'cfg' => $cfg,
        ]);
    }

    public function mostrar($params = [])
    {
        $cfg = Session::get('api_config', []);
        $this->json([
            'success' => true,
            'data'    => [
                'base_url'   => $cfg['base_url'] ?? '',
                'api_key'    => $cfg['api_key'] ?? '',
                'api_secret' => $cfg['api_secret'] ?? '',
            ],
        ]);
    }

    public function actualizar($params = [])
    {
        $request = \App\Core\App::getInstance()->getRequest();
        $baseUrl   = $request->input('base_url', API_DEFAULT_BASE_URL);
        $apiKey    = $request->input('api_key', '');
        $apiSecret = $request->input('api_secret', '');

        $config = [
            'base_url'   => $baseUrl,
            'api_key'    => $apiKey,
            'api_secret' => $apiSecret,
        ];
        Session::set('api_config', $config);

        $userId = Session::get('user_id');
        if ($userId) {
            try {
                $cfgModel = new ConfiguracionUsuario();
                $existing = $cfgModel->findByUserId($userId);
                if ($existing) {
                    $cfgModel->update($existing['id'], [
                        'base_url'   => $baseUrl,
                        'api_key'    => $apiKey,
                        'api_secret' => $apiSecret,
                    ]);
                } else {
                    $cfgModel->create([
                        'user_id'    => $userId,
                        'base_url'   => $baseUrl,
                        'api_key'    => $apiKey,
                        'api_secret' => $apiSecret,
                    ]);
                }
            } catch (\Exception $e) {
                // Config guardada en sesión aunque la BD falle
            }
        }

        $this->json(['success' => true, 'message' => 'Configuración guardada']);
    }

    public function probarConexion($params = [])
    {
        $request = \App\Core\App::getInstance()->getRequest();
        $baseUrl   = $request->input('base_url', '');
        $apiKey    = $request->input('api_key', '');
        $apiSecret = $request->input('api_secret', '');

        if (!$baseUrl || !$apiKey) {
            return $this->json(['success' => false, 'message' => 'Completa URL Base y API Key'], 400);
        }

        $api = new \App\Services\SunatApiService([
            'base_url'   => $baseUrl,
            'api_key'    => $apiKey,
            'api_secret' => $apiSecret,
        ]);

        $result = $api->get('/empresa');
        $this->json($result);
    }

    public function subirCertificado($params = [])
    {
        if (!isset($_FILES['certificado']) || $_FILES['certificado']['error'] !== UPLOAD_ERR_OK) {
            return $this->json(['success' => false, 'message' => 'No se recibió el archivo de certificado.'], 400);
        }

        $certDir = __DIR__ . '/../../storage/certificates';
        if (!is_dir($certDir)) {
            @mkdir($certDir, 0755, true);
        }

        $ext = strtolower(pathinfo($_FILES['certificado']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['p12', 'pfx', 'pem'])) {
            return $this->json(['success' => false, 'message' => 'Formato no válido. Solo .p12, .pfx o .pem.'], 400);
        }

        $userId = Session::get('user_id');
        $destino = $certDir . '/cert_' . $userId . '.' . $ext;
        move_uploaded_file($_FILES['certificado']['tmp_name'], $destino);

        $contrasena = \App\Core\App::getInstance()->getRequest()->post('contrasena_certificado', '');

        Session::set('certificado', [
            'ruta'       => $destino,
            'password'   => $contrasena,
            'tiene_cert' => true,
        ]);

        $apiConfig = Session::get('api_config', []);
        if (!empty($apiConfig['base_url']) && !empty($apiConfig['api_key'])) {
            $api = new \App\Services\SunatApiService($apiConfig);
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

    public function eliminarCertificado($params = [])
    {
        $cert = Session::get('certificado', []);
        if (!empty($cert['ruta']) && file_exists($cert['ruta'])) {
            @unlink($cert['ruta']);
        }
        Session::remove('certificado');

        $apiConfig = Session::get('api_config', []);
        if (!empty($apiConfig['base_url']) && !empty($apiConfig['api_key'])) {
            $api = new \App\Services\SunatApiService($apiConfig);
            try {
                $result = $api->delete('/empresa/certificado');
                $this->json($result);
                return;
            } catch (\Exception $e) {}
        }

        $this->json(['success' => true, 'message' => 'Certificado eliminado.']);
    }

    public function estadoCertificado($params = [])
    {
        $cert = Session::get('certificado', []);
        $tiene = !empty($cert['tiene_cert']) && !empty($cert['ruta']) && file_exists($cert['ruta']);
        $this->json([
            'success' => true,
            'data'    => [
                'tiene_certificado' => $tiene,
                'archivo'           => $tiene ? basename($cert['ruta']) : null,
            ],
        ]);
    }

    public function subirLogo($params = [])
    {
        if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
            return $this->json(['success' => false, 'message' => 'No se recibi\u00f3 el archivo de logo.'], 400);
        }

        $logoDir = __DIR__ . '/../../storage/logos';
        if (!is_dir($logoDir)) {
            @mkdir($logoDir, 0755, true);
        }

        $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'svg'])) {
            return $this->json(['success' => false, 'message' => 'Formato no v\u00e1lido. Solo .png, .jpg, .gif, .svg.'], 400);
        }

        $userId = Session::get('user_id');
        $destino = $logoDir . '/logo_' . $userId . '.' . $ext;
        move_uploaded_file($_FILES['logo']['tmp_name'], $destino);

        Session::set('logo', [
            'ruta'      => $destino,
            'tiene_logo' => true,
            'ext'       => $ext,
        ]);

        $apiConfig = Session::get('api_config', []);
        if (!empty($apiConfig['base_url']) && !empty($apiConfig['api_key'])) {
            $api = new \App\Services\SunatApiService($apiConfig);
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

    public function eliminarLogo($params = [])
    {
        $logo = Session::get('logo', []);
        if (!empty($logo['ruta']) && file_exists($logo['ruta'])) {
            @unlink($logo['ruta']);
        }
        Session::remove('logo');

        $apiConfig = Session::get('api_config', []);
        if (!empty($apiConfig['base_url']) && !empty($apiConfig['api_key'])) {
            $api = new \App\Services\SunatApiService($apiConfig);
            try {
                $api->delete('/empresa/logo');
            } catch (\Exception $e) {}
        }

        $this->json(['success' => true, 'message' => 'Logo eliminado.']);
    }

    public function estadoLogo($params = [])
    {
        $logo = Session::get('logo', []);
        $tiene = !empty($logo['tiene_logo']) && !empty($logo['ruta']) && file_exists($logo['ruta']);
        $this->json([
            'success' => true,
            'data'    => [
                'tiene_logo' => $tiene,
                'ext'        => $tiene ? $logo['ext'] : null,
            ],
        ]);
    }

    public function imagenLogo($params = [])
    {
        $logo = Session::has('user_id') ? Session::get('logo', []) : [];
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

        // Devolver logo por defecto
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
