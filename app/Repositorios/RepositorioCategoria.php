<?php
declare(strict_types=1);

namespace App\Repositorios;

class RepositorioCategoria extends RepositorioBase
{
    public function listarTodas(): array
    {
        return $this->findAll('categorias', 'nombre ASC');
    }

    public function obtenerPorId(int $id): ?array
    {
        return $this->find('categorias', $id);
    }

    public function crear(array $data): int
    {
        return $this->create('categorias', $data);
    }

    public function actualizar(int $id, array $data): bool
    {
        return $this->update('categorias', $id, $data);
    }

    public function eliminar(int $id): bool
    {
        return $this->delete('categorias', $id);
    }
}
