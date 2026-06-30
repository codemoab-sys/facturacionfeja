<?php
namespace App\Core;

use PDO;
use PDOException;

class Model
{
    protected static $pdo = null;
    protected $table;
    protected $primaryKey = 'id';

    protected static function getPDO()
    {
        if (self::$pdo === null) {
            $config = require __DIR__ . '/../../config/database.php';
            try {
                $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
                self::$pdo = new PDO($dsn, $config['username'], $config['password'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                throw new \Exception("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    public function findAll($orderBy = null)
    {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        $stmt = self::getPDO()->query($sql);
        return $stmt->fetchAll();
    }

    public function findById($id)
    {
        $stmt = self::getPDO()->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findBy($field, $value)
    {
        $stmt = self::getPDO()->prepare("SELECT * FROM {$this->table} WHERE {$field} = ?");
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }

    public function findOneBy($field, $value)
    {
        $stmt = self::getPDO()->prepare("SELECT * FROM {$this->table} WHERE {$field} = ? LIMIT 1");
        $stmt->execute([$value]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $stmt = self::getPDO()->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})");
        $stmt->execute(array_values($data));
        return self::getPDO()->lastInsertId();
    }

    public function update($id, $data)
    {
        $sets = implode(', ', array_map(function ($col) {
            return "{$col} = ?";
        }, array_keys($data)));
        $stmt = self::getPDO()->prepare("UPDATE {$this->table} SET {$sets} WHERE {$this->primaryKey} = ?");
        $values = array_values($data);
        $values[] = $id;
        return $stmt->execute($values);
    }

    public function delete($id)
    {
        $stmt = self::getPDO()->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }

    public function raw($sql, $params = [])
    {
        $stmt = self::getPDO()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
