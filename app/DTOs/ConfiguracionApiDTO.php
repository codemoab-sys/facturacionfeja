<?php
declare(strict_types=1);

namespace App\DTOs;

class ConfiguracionApiDTO
{
    private string $baseUrl;
    private string $apiKey;
    private string $apiSecret;

    public function __construct(string $baseUrl, string $apiKey, string $apiSecret)
    {
        $this->baseUrl = $baseUrl;
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getApiSecret(): string
    {
        return $this->apiSecret;
    }

    public function toArray(): array
    {
        return [
            'base_url'   => $this->baseUrl,
            'api_key'    => $this->apiKey,
            'api_secret' => $this->apiSecret,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['base_url'] ?? '',
            $data['api_key'] ?? '',
            $data['api_secret'] ?? ''
        );
    }
}
