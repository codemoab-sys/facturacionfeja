<?php
declare(strict_types=1);

namespace App\Controladores;

use App\Nucleo\Controlador;
use App\Nucleo\Sesion;
use App\Repositorios\RepositorioConfiguracionUsuario;
use App\Servicios\ServicioConfiguracion;
use App\Servicios\ServicioApiSunat;
use App\Validacion\SolicitudConfiguracion;

class ConfiguracionApiControlador extends Controlador
{
    private ServicioConfiguracion $configService;
    private RepositorioConfiguracionUsuario $userConfigRepository;

    public function __construct(
        ?ServicioConfiguracion $configService = null,
        ?RepositorioConfiguracionUsuario $userConfigRepository = null
    ) {
        $this->configService = $configService ?? new ServicioConfiguracion();
        $this->userConfigRepository = $userConfigRepository ?? new RepositorioConfiguracionUsuario();
    }

    public function mostrar(): void
    {
        $cfg = $this->configService->getAll();
        $this->json([
            'success' => true,
            'data'    => [
                'base_url'   => $cfg['base_url'] ?? '',
                'api_key'    => $cfg['api_key'] ?? '',
                'api_secret' => $cfg['api_secret'] ?? '',
            ],
        ]);
    }

    public function actualizar(): void
    {
        $request = \App\Nucleo\App::getInstance()->getRequest();
        $baseUrl   = $request->input('base_url', API_DEFAULT_BASE_URL);
        $apiKey    = $request->input('api_key', '');
        $apiSecret = $request->input('api_secret', '');

        $errors = SolicitudConfiguracion::validate([
            'base_url'   => $baseUrl,
            'api_key'    => $apiKey,
            'api_secret' => $apiSecret,
        ]);

        if ($errors) {
            $this->json(['success' => false, 'message' => 'Datos inválidos.', 'errors' => $errors], 422);
            return;
        }

        $config = [
            'base_url'   => $baseUrl,
            'api_key'    => $apiKey,
            'api_secret' => $apiSecret,
        ];
        $this->configService->setConfig($config);

        $userId = Sesion::get('user_id');
        if ($userId) {
            try {
                $this->userConfigRepository->upsert((int)$userId, $config);
            } catch (\Exception $e) {}
        }

        $this->json(['success' => true, 'message' => 'Configuración guardada']);
    }

    public function probarConexion(): void
    {
        $request = \App\Nucleo\App::getInstance()->getRequest();
        $baseUrl   = $request->input('base_url', '');
        $apiKey    = $request->input('api_key', '');
        $apiSecret = $request->input('api_secret', '');

        if (!$baseUrl || !$apiKey) {
            $this->json(['success' => false, 'message' => 'Completa URL Base y API Key'], 400);
            return;
        }

        $api = new ServicioApiSunat([
            'base_url'   => $baseUrl,
            'api_key'    => $apiKey,
            'api_secret' => $apiSecret,
        ]);

        $result = $api->get('/empresa');
        $this->json($result);
    }
}
