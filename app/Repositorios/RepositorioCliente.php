<?php
declare(strict_types=1);

namespace App\Repositorios;

class RepositorioCliente extends RepositorioBase
{
    public function listarPorUsuario(int $userId, string $buscar = ''): array
    {
        $sql = "SELECT * FROM clientes WHERE user_id = ?";
        $params = [$userId];

        if ($buscar) {
            $sql .= " AND (razon_social LIKE ? OR num_doc LIKE ?)";
            $like = '%' . $buscar . '%';
            $params[] = $like;
            $params[] = $like;
        }

        $sql .= " ORDER BY razon_social ASC";
        return $this->query($sql, $params);
    }

    public function buscarPorDoc(int $userId, string $numDoc): ?array
    {
        $rows = $this->query(
            "SELECT * FROM clientes WHERE user_id = ? AND num_doc = ?",
            [$userId, $numDoc]
        );
        return $rows[0] ?? null;
    }
}
