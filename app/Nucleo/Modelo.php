<?php
declare(strict_types=1);

namespace App\Nucleo;

use App\Repositorios\RepositorioBase;

class Modelo
{
    protected static ?RepositorioBase $repo = null;
    protected string $tabla;
    protected string $primaryKey = 'id';

    protected static function getRepo(): RepositorioBase
    {
        if (self::$repo === null) {
            self::$repo = new RepositorioBase();
        }
        return self::$repo;
    }

    public function findAll(?string $orderBy = null): array
    {
        return self::getRepo()->findAll($this->tabla, $orderBy);
    }

    public function findById(int|string $id): ?array
    {
        return self::getRepo()->find($this->tabla, (int)$id, $this->primaryKey);
    }

    public function findBy(string $field, mixed $value): array
    {
        return self::getRepo()->findBy($this->tabla, $field, $value);
    }

    public function findOneBy(string $field, mixed $value): ?array
    {
        return self::getRepo()->findOneBy($this->tabla, $field, $value);
    }

    public function create(array $data): int
    {
        return self::getRepo()->create($this->tabla, $data);
    }

    public function update(int|string $id, array $data): bool
    {
        return self::getRepo()->update($this->tabla, (int)$id, $data, $this->primaryKey);
    }

    public function delete(int|string $id): bool
    {
        return self::getRepo()->delete($this->tabla, (int)$id, $this->primaryKey);
    }

    public function raw(string $sql, array $params = []): array
    {
        return self::getRepo()->query($sql, $params);
    }
}
