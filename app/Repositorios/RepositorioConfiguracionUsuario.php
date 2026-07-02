<?php
declare(strict_types=1);

namespace App\Repositorios;

class RepositorioConfiguracionUsuario extends RepositorioBase
{
    private string $tabla = 'user_configs';

    public function findByUserId(int $userId): ?array
    {
        return $this->findOneBy($this->tabla, 'user_id', $userId);
    }

    public function upsert(int $userId, array $data): void
    {
        $existing = $this->findByUserId($userId);
        if ($existing) {
            $this->update($this->tabla, (int)$existing['id'], $data);
        } else {
            $data['user_id'] = $userId;
            $this->create($this->tabla, $data);
        }
    }
}
