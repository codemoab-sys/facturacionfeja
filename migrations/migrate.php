<?php
declare(strict_types=1);

/**
 * Migration runner — CLI script
 * Usage: php migrations/migrate.php
 */

$configFile = __DIR__ . '/../config/database.php';
if (!file_exists($configFile)) {
    $configFile = __DIR__ . '/../config/database.example.php';
}
$config = require $configFile;

try {
    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage() . "\n");
}

// Crear tabla de tracking si no existe
$pdo->exec("
    CREATE TABLE IF NOT EXISTS _migrations (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        migration   VARCHAR(255) NOT NULL UNIQUE,
        executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

$executed = $pdo->query("SELECT migration FROM _migrations")->fetchAll(PDO::FETCH_COLUMN);

$migrationsDir = __DIR__;
$files = glob($migrationsDir . '/*.sql');
sort($files);

$count = 0;

foreach ($files as $file) {
    $filename = basename($file);
    if (in_array($filename, $executed, true)) {
        echo "  [SKIP] {$filename} (ya ejecutada)\n";
        continue;
    }

    echo "  [RUN]  {$filename}... ";

    $sql = file_get_contents($file);

    // Remove USE/CREATE DATABASE statements if any
    $lines = explode("\n", $sql);
    $cleanLines = [];
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if (preg_match('/^(CREATE\s+DATABASE|USE\s+)/i', $trimmed)) {
            continue;
        }
        $cleanLines[] = $line;
    }
    $sql = implode("\n", $cleanLines);

    // Split by semicolons for multi-statement
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        fn(string $s): bool => $s !== ''
    );

    try {
        $pdo->beginTransaction();
        foreach ($statements as $stmt) {
            if (!empty($stmt)) {
                $pdo->exec($stmt);
            }
        }
        $insert = $pdo->prepare("INSERT INTO _migrations (migration) VALUES (?)");
        $insert->execute([$filename]);
        $pdo->commit();
        echo "OK\n";
        $count++;
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "ERROR: " . $e->getMessage() . "\n";
        exit(1);
    }
}

echo "\n";
if ($count === 0) {
    echo "No hay migraciones pendientes.\n";
} else {
    echo "Se ejecutaron {$count} migración(es) correctamente.\n";
}
