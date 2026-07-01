<?php
declare(strict_types=1);

namespace App\DTOs;

class UsuarioDTO
{
    private int $id;
    private string $username;
    private string $nombre;

    public function __construct(int $id, string $username, string $nombre)
    {
        $this->id = $id;
        $this->username = $username;
        $this->nombre = $nombre;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function toArray(): array
    {
        return [
            'id'       => $this->id,
            'usuario'  => $this->username,
            'nombre'   => $this->nombre,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (int)($data['id'] ?? 0),
            $data['usuario'] ?? '',
            $data['nombre'] ?? ''
        );
    }
}
