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
}
