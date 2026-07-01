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
        if (!in_array($ext, ['p12', 'pfx'])) {
            return $this->json(['success' => false, 'message' => 'Formato no válido. Solo .p12 o .pfx.'], 400);
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
                $this->json($result);
                return;
            } catch (\Exception $e) {}
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
}
