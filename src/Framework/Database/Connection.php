<?php
declare(strict_types=1);

namespace App\Framework\Database;

use PDO;
use PDOException;
use RuntimeException;

class Connection
{
    private static ?PDO $pdo = null;

    public static function get(): PDO
    {
        if (self::$pdo === null) {
            $configFile = __DIR__ . '/../../../config/database.php';
            if (!file_exists($configFile)) {
                $configFile = __DIR__ . '/../../../config/database.example.php';
            }
            $config = require $configFile;

            if (empty($config['host']) || $config['host'] === 'localhost') {
                $config['host'] = 'localhost';
            }

            try {
                $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
                self::$pdo = new PDO($dsn, $config['username'], $config['password'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                self::log("DB_ERROR", $e->getMessage());
                throw new RuntimeException("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    public static function test(): array
    {
        try {
            $pdo = self::get();
            $pdo->query("SELECT 1");
            return ['success' => true, 'message' => 'Conexión OK'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public static function disconnect(): void
    {
        self::$pdo = null;
    }

    private static function log(string $event, string $detail): void
    {
        $logDir = __DIR__ . '/../../../storage/logs';
        if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
        $line = sprintf("[%s] %s | %s\n", date('Y-m-d H:i:s'), $event, $detail);
        @file_put_contents($logDir . '/db.log', $line, FILE_APPEND | LOCK_EX);
    }
}
