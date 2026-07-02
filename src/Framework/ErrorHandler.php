<?php
declare(strict_types=1);

namespace App\Framework;

use Throwable;

class ErrorHandler
{
    private bool $debug;

    public function __construct(bool $debug = false)
    {
        $this->debug = $debug;
    }

    public function register(): void
    {
        set_exception_handler([$this, 'handleException']);
        set_error_handler([$this, 'handleError']);
    }

    public function handleException(Throwable $e): void
    {
        $this->logError($e);
        $isApi = $this->isJsonRequest();

        if ($isApi) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $this->debug ? $e->getMessage() : 'Error interno del servidor',
                'errors'  => $this->debug ? [
                    'file'  => $e->getFile(),
                    'line'  => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ] : null,
            ], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            $message = $this->debug ? $e->getMessage() : 'Error interno del servidor';
            echo '<!DOCTYPE html><html><head><title>Error</title></head><body>';
            echo '<h1>500 - ' . htmlspecialchars($message) . '</h1>';
            if ($this->debug) {
                echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            }
            echo '</body></html>';
        }
        exit;
    }

    public function handleError(int $severity, string $message, string $file, int $line): bool
    {
        if (!(error_reporting() & $severity)) {
            return false;
        }
        throw new \ErrorException($message, 0, $severity, $file, $line);
    }

    private function isJsonRequest(): bool
    {
        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            return true;
        }
        if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
            return true;
        }
        if (strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') === 0) {
            return true;
        }
        return false;
    }

    private function logError(Throwable $e): void
    {
        $logDir = __DIR__ . '/../../storage/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        $line = sprintf(
            "[%s] %s in %s:%d\n%s\n\n",
            date('Y-m-d H:i:s'),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );
        @file_put_contents($logDir . '/error.log', $line, FILE_APPEND | LOCK_EX);
    }
}
