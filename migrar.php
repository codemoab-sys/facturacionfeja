<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Solo definimos BASE_PATH si no est� definido
if (!defined('BASE_PATH')) {
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    define('BASE_PATH', ($scriptDir === '/' || $scriptDir === '\\') ? '' : rtrim($scriptDir, '/'));
}

require_once __DIR__ . '/app/Helpers.php';

$configFile = __DIR__ . '/config/database.php';
if (!file_exists($configFile)) {
    echo "<p style='color:orange'>No se encontr� config/database.php, usando database.example.php</p>";
    $configFile = __DIR__ . '/config/database.example.php';
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
    die("<h2 style='color:red'>Error de conexi&oacute;n: " . htmlspecialchars($e->getMessage()) . "</h2>");
}

$pdo->exec("CREATE TABLE IF NOT EXISTS _migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL UNIQUE,
    executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$archivos = glob(__DIR__ . '/migrations/*.sql');
sort($archivos);

echo "<h2>Migraciones ejecutadas:</h2><ul>";

foreach ($archivos as $archivo) {
    $nombre = basename($archivo);

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM _migrations WHERE migration = ?");
    $stmt->execute([$nombre]);
    $yaCorrio = $stmt->fetchColumn() > 0;

    if ($yaCorrio) {
        echo "<li style='color:gray;'>{$nombre} — ya ejecutada</li>";
        continue;
    }

    $sql = file_get_contents($archivo);
    $sentencias = array_filter(array_map('trim', explode(';', $sql)));

    try {
        $pdo->beginTransaction();
        foreach ($sentencias as $sentencia) {
            if (!empty($sentencia) && stripos($sentencia, 'INSERT') !== 0 && stripos($sentencia, 'SELECT') !== 0) {
                $pdo->exec($sentencia);
            }
        }
        // Ejecutar INSERTs y SELECTs aparte
        foreach ($sentencias as $sentencia) {
            if (!empty($sentencia) && (stripos($sentencia, 'INSERT') === 0 || stripos($sentencia, 'SELECT') === 0)) {
                $pdo->exec($sentencia);
            }
        }
        $stmt = $pdo->prepare("INSERT INTO _migrations (migration) VALUES (?)");
        $stmt->execute([$nombre]);
        $pdo->commit();
        echo "<li style='color:green; font-weight:bold;'>{$nombre} — ejecutada correctamente</li>";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<li style='color:red;'>{$nombre} — ERROR: " . htmlspecialchars($e->getMessage()) . "</li>";
    }
}

echo "</ul><p><a href='" . (BASE_PATH ?: '') . "/'>Volver al inicio</a></p>";
