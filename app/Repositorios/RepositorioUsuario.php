<?php
declare(strict_types=1);

namespace App\Repositorios;

class RepositorioUsuario extends RepositorioBase
{
    private string $table = 'users';

    public function findByUsername(string $username): ?array
    {
        return $this->findOneBy($this->table, 'usuario', $username);
    }

    public function findById(int $id): ?array
    {
        return $this->find($this->table, $id);
    }

    public function createUser(array $data): int
    {
        return $this->create($this->table, $data);
    }
}
