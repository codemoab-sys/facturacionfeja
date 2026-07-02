<?php
declare(strict_types=1);

namespace App\Modules\Settings\Controllers;

use App\Framework\ApiController;
use App\Framework\Session;
use App\Modules\Settings\Services\ConfigService;
use App\Modules\Settings\Repositories\ConfigRepository;
use App\Validacion\SolicitudConfiguracion;

class SettingsApiController extends ApiController
{
    private ConfigService $configService;
    private ConfigRepository $configRepo;

    public function __construct(?ConfigService $configService = null, ?ConfigRepository $configRepo = null)
    {
        parent::__construct();
        $this->configService = $configService ?? new ConfigService();
        $this->configRepo = $configRepo ?? new ConfigRepository();
    }

    public function mostrar(): void
    {
        $cfg = $this->configService->getAll();
        $this->success([
            'base_url'   => $cfg['base_url'] ?? '',
            'api_key'    => $cfg['api_key'] ?? '',
            'api_secret' => $cfg['api_secret'] ?? '',
        ]);
    }

    public function actualizar(): void
    {
        $baseUrl   = $this->input('base_url', API_DEFAULT_BASE_URL);
        $apiKey    = $this->input('api_key', '');
        $apiSecret = $this->input('api_secret', '');

        $errors = SolicitudConfiguracion::validate([
            'base_url'   => $baseUrl,
            'api_key'    => $apiKey,
            'api_secret' => $apiSecret,
        ]);

        if ($errors) {
            $this->error('Datos inválidos.', 422, $errors);
        }

        $config = compact('base_url', 'api_key', 'api_secret');
        $this->configService->setConfig($config);

        if ($this->userId) {
            try {
                $this->configRepo->upsert($this->userId, $config);
            } catch (\Exception $e) {}
        }

        $this->success(null, 'Configuración guardada');
    }

    public function probarConexion(): void
    {
        $baseUrl   = $this->input('base_url', '');
        $apiKey    = $this->input('api_key', '');
        $apiSecret = $this->input('api_secret', '');

        if (!$baseUrl || !$apiKey) {
            $this->error('Completa URL Base y API Key', 400);
        }

        $api = new \App\Modules\Documents\Services\SunatApiService(
            compact('base_url', 'api_key', 'api_secret')
        );
        $result = $api->get('/empresa');
        $this->json($result);
    }
}
