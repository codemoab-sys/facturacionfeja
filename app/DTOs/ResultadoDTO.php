<?php
declare(strict_types=1);

namespace App\DTOs;

class ResultadoDTO
{
    private bool $success;
    private ?string $message;
    private mixed $data;
    private mixed $errors;
    private int $statusCode;

    public function __construct(
        bool $success,
        ?string $message = null,
        mixed $data = null,
        mixed $errors = null,
        int $statusCode = 200
    ) {
        $this->success = $success;
        $this->message = $message;
        $this->data = $data;
        $this->errors = $errors;
        $this->statusCode = $statusCode;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function getErrors(): mixed
    {
        return $this->errors;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function toArray(): array
    {
        $result = [
            'success' => $this->success,
            'message' => $this->message,
        ];
        if ($this->data !== null) {
            $result['data'] = $this->data;
        }
        if ($this->errors !== null) {
            $result['errors'] = $this->errors;
        }
        return $result;
    }

    public static function success(string $message, mixed $data = null): self
    {
        return new self(true, $message, $data);
    }

    public static function error(string $message, mixed $errors = null, int $statusCode = 400): self
    {
        return new self(false, $message, null, $errors, $statusCode);
    }
}
