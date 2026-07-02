<?php
declare(strict_types=1);

namespace App\Modelos;

use App\Nucleo\Modelo;

class Categoria extends Modelo
{
    protected string $tabla = 'categorias';

    public function obtenerPorId(int $id): ?array
    {
        return $this->findById($id);
    }

    public function listarTodas(): array
    {
        return $this->findAll();
    }

    public function crear(array $data): int
    {
        return $this->create($data);
    }

    public function actualizar(int $id, array $data): void
    {
        $this->update($id, $data);
    }

    public function eliminar(int $id): void
    {
        $this->delete($id);
    }
}
