<?php
declare(strict_types=1);

namespace App\Modules\Auth\Repositories;

use App\Framework\Database\Repository;

class UserRepository extends Repository
{
    private string $tabla = 'users';

    public function findByUsername(string $username): ?array
    {
        return $this->findOneBy($this->tabla, 'usuario', $username);
    }

    public function findById(int $id): ?array
    {
        return $this->find($this->tabla, $id);
    }

    public function findConfigByUserId(int $userId): ?array
    {
        $rows = $this->query("SELECT * FROM user_configs WHERE user_id = ?", [$userId]);
        return $rows[0] ?? null;
    }
}
