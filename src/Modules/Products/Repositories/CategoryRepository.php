<?php
declare(strict_types=1);

namespace App\Modules\Products\Repositories;

use App\Framework\Database\Repository;

class CategoryRepository extends Repository
{
    private string $tabla = 'categorias';

    public function listarTodas(): array
    {
        return $this->findAll($this->tabla, 'nombre ASC');
    }
}
