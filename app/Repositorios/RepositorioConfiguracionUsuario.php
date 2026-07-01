<?php
declare(strict_types=1);

namespace App\Repositorios;

class RepositorioConfiguracionUsuario extends RepositorioBase
{
    private string $table = 'user_configs';

    public function findByUserId(int $userId): ?array
    {
        return $this->findOneBy($this->table, 'user_id', $userId);
    }

    public function upsert(int $userId, array $data): void
    {
        $existing = $this->findByUserId($userId);
        if ($existing) {
            $this->update($this->table, (int)$existing['id'], $data);
        } else {
            $data['user_id'] = $userId;
            $this->create($this->table, $data);
        }
    }
}
