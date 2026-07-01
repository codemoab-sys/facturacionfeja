<?php
declare(strict_types=1);

namespace App\Repositorios;

use PDO;
use PDOException;
use RuntimeException;

class RepositorioBase
{
    private static ?PDO $pdo = null;

    protected function db(): PDO
    {
        if (self::$pdo === null) {
            $configFile = __DIR__ . '/../../config/database.php';
            if (!file_exists($configFile)) {
                $configFile = __DIR__ . '/../../config/database.example.php';
            }
            $config = require $configFile;
            try {
                $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
                self::$pdo = new PDO($dsn, $config['username'], $config['password'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                throw new RuntimeException("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    public function find(string $table, int $id, string $primaryKey = 'id'): ?array
    {
        $stmt = $this->db()->prepare("SELECT * FROM {$table} WHERE {$primaryKey} = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findAll(string $table, ?string $orderBy = null, string $primaryKey = 'id'): array
    {
        $sql = "SELECT * FROM {$table}";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        $stmt = $this->db()->query($sql);
        return $stmt->fetchAll();
    }

    public function findBy(string $table, string $field, mixed $value): array
    {
        $stmt = $this->db()->prepare("SELECT * FROM {$table} WHERE {$field} = ?");
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }

    public function findOneBy(string $table, string $field, mixed $value): ?array
    {
        $stmt = $this->db()->prepare("SELECT * FROM {$table} WHERE {$field} = ? LIMIT 1");
        $stmt->execute([$value]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $stmt = $this->db()->prepare("INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})");
        $stmt->execute(array_values($data));
        return (int)$this->db()->lastInsertId();
    }

    public function update(string $table, int $id, array $data, string $primaryKey = 'id'): bool
    {
        $sets = implode(', ', array_map(function (string $col): string {
            return "{$col} = ?";
        }, array_keys($data)));
        $stmt = $this->db()->prepare("UPDATE {$table} SET {$sets} WHERE {$primaryKey} = ?");
        $values = array_values($data);
        $values[] = $id;
        return $stmt->execute($values);
    }

    public function delete(string $table, int $id, string $primaryKey = 'id'): bool
    {
        $stmt = $this->db()->prepare("DELETE FROM {$table} WHERE {$primaryKey} = ?");
        return $stmt->execute([$id]);
    }

    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->db()->prepare($sql);
        return $stmt->execute($params);
    }

    public function lastInsertId(): int
    {
        return (int)$this->db()->lastInsertId();
    }
}
