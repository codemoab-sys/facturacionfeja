<?php
namespace App\Services;

class SunatApiService
{
    private $baseUrl;
    private $apiKey;
    private $apiSecret;

    public function __construct($config = null)
    {
        if ($config === null) {
            $config = \App\Core\Session::get('api_config', []);
        }
        $this->baseUrl   = rtrim($config['base_url'] ?? API_DEFAULT_BASE_URL, '/');
        $this->apiKey    = $config['api_key'] ?? '';
        $this->apiSecret = $config['api_secret'] ?? '';
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function request($method, $path, $body = null)
    {
        $url = $this->baseUrl . $path;
        $headers = [
            'Accept: application/json',
            'X-Api-Key: ' . $this->apiKey,
            'X-Api-Secret: ' . $this->apiSecret,
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
        ]);

        if ($body !== null) {
            $encoded = json_encode($body, JSON_UNESCAPED_UNICODE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE) ?: '';
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return [
                'success' => false,
                'message' => 'Error de conexión: ' . $error,
                'data'    => null,
                'errors'  => null,
            ];
        }

        $isJson = strpos($contentType, 'application/json') !== false
               || ($response && $response[0] === '{' || $response[0] === '[');

        if ($isJson) {
            $raw = json_decode($response, true);
            if ($raw === null) {
                $raw = json_decode(json_encode(simplexml_load_string($response)), true) ?? [];
            }
            $normalized = [
                'success' => isset($raw['success']) ? (bool)$raw['success']
                           : (($raw['estado'] ?? '') === 'exito'),
                'message' => $raw['message'] ?? $raw['mensaje'] ?? null,
                'data'    => $raw['data'] ?? $raw['datos'] ?? $raw,
                'errors'  => $raw['errors'] ?? $raw['errores'] ?? null,
            ];
            return $normalized;
        }

        if ($httpCode >= 400) {
            return [
                'success' => false,
                'message' => 'Error ' . $httpCode . ': ' . substr($response, 0, 200),
                'data'    => null,
                'errors'  => null,
            ];
        }

        return [
            'success'  => true,
            'message'  => null,
            'data'     => $response,
            'errors'   => null,
            'isBinary' => true,
            'mime'     => $contentType ?: 'application/octet-stream',
        ];
    }

    public function get($path)
    {
        return $this->request('GET', $path);
    }

    public function post($path, $body)
    {
        return $this->request('POST', $path, $body);
    }
}
