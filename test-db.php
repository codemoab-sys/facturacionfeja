<?php
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$name = getenv('DB_NAME') ?: 'facturacionfeja';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

echo "<h3>🔍 Prueba de conexión MySQL</h3>";
echo "<pre>";
echo "Host: $host\n";
echo "Puerto: $port\n";
echo "BD: $name\n";
echo "Usuario: $user\n";
echo "Password: " . str_repeat('*', strlen($pass)) . "\n\n";

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "✅ Conexión exitosa a MySQL\n\n";

    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM users");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Usuarios en BD: {$row['total']}\n";

    $stmt = $pdo->query("SELECT id, usuario, LEFT(password, 30) AS hash_preview FROM users");
    while ($u = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  ID {$u['id']}: {$u['usuario']} — hash: {$u['hash_preview']}...\n";
    }

} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "\n💡 Posibles causas:\n";
    echo "  • Host o puerto incorrecto\n";
    echo "  • Base de datos '$name' no existe\n";
    echo "  • Usuario/contraseña inválidos\n";
    echo "  • MySQL no está corriendo\n";
}
echo "</pre>";
