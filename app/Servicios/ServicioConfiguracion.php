<?php
declare(strict_types=1);

namespace App\Servicios;

use App\Nucleo\Sesion;

class ServicioConfiguracion
{
    private const SESSION_KEY = 'api_config';

    public function getBaseUrl(): string
    {
        $config = $this->getAll();
        return $config['base_url'] ?? API_DEFAULT_BASE_URL;
    }

    public function getApiKey(): string
    {
        $config = $this->getAll();
        return $config['api_key'] ?? '';
    }

    public function getApiSecret(): string
    {
        $config = $this->getAll();
        return $config['api_secret'] ?? '';
    }

    public function getAll(): array
    {
        return Sesion::get(self::SESSION_KEY, []);
    }

    public function hasConfig(): bool
    {
        $config = $this->getAll();
        return !empty($config['base_url']) && !empty($config['api_key']);
    }

    public function setConfig(array $config): void
    {
        Sesion::set(self::SESSION_KEY, $config);
    }

    public function getSunatApiConfig(): array
    {
        return [
            'base_url'   => $this->getBaseUrl(),
            'api_key'    => $this->getApiKey(),
            'api_secret' => $this->getApiSecret(),
        ];
    }
}
