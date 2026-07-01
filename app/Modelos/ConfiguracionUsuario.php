<?php
declare(strict_types=1);

namespace App\Modelos;

use App\Nucleo\Modelo;

class ConfiguracionUsuario extends Modelo
{
    protected string $table = 'user_configs';

    public function findByUserId(int $userId): ?array
    {
        return $this->findOneBy('user_id', $userId);
    }
}
