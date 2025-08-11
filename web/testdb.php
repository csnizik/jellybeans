<?php
// filepath: web/test-db.php

// Prevent access in production
if ($_SERVER['HTTP_HOST'] !== 'localhost' && !isset($_GET['allow'])) {
    die('Access denied. Add ?allow=1 to URL if you need to test on non-localhost');
}

echo "<h1>Database Connection Test</h1>";

// Test 1: Direct PDO connection (mimics Drupal's connection)
echo "<h2>Test 1: Direct PDO Connection</h2>";

$host = getenv('DB_HOST');
$username = getenv('DB_USER');
$dbname = getenv('DB_DATABASE');
$password = getenv('DB_PASSWORD');

try {
    // SSL options for Azure MySQL
    $options = [
        \PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true,
        \PDO::MYSQL_ATTR_SSL_CA => '/etc/ssl/certs/ca-certificates.crt',
        \PDO::MYSQL_ATTR_SSL_CAPATH => '/etc/ssl/certs',
        \PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    $dsn = "mysql:host={$host};port=3306;dbname={$dbname};charset=utf8mb4";

    echo "<p>Attempting connection to: {$host}</p>";
    echo "<p>Database: {$dbname}</p>";
    echo "<p>Username: {$username}</p>";

    $pdo = new PDO($dsn, $username, $password, $options);

    echo "<p style='color: green;'>✅ Connection successful!</p>";

    // Test query
    $stmt = $pdo->query("SELECT VERSION() as version, NOW() as current_time");
    $result = $stmt->fetch();

    echo "<p>MySQL Version: " . $result['version'] . "</p>";
    echo "<p>Current Time: " . $result['current_time'] . "</p>";

    // Test SSL status
    try {
      $stmt = $pdo->query("SELECT @@session.ssl_cipher AS ssl_ciper");
      $ssl = $stmt->fetch();
      $cipher = $ssl && !empty($ssl['ssl_cipher']) ? $ssl['ssl_cipher'] : '';
      echo "<p>SSL Status: " . ($cipher ? "✅ Enabled ({$cipher})" : "❌ Disabled/Unknown") . "</p>";
    } catch (Throwable $e) {
      echo "<p> SSL Status: Unknown (".$e->getMessage().")</p>";
    }

    $stmt = $pdo->query("SHOW STATUS LIKE 'Ssl_cipher'");
    $ssl = $stmt->fetch();
    echo "<p>SSL Status: " . ($ssl['Value'] ? "✅ Enabled ({$ssl['Value']})" : "❌ Disabled") . "</p>";

} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Connection failed: " . $e->getMessage() . "</p>";
    echo "<p>Error Code: " . $e->getCode() . "</p>";
}

// Test 2: Using Drupal's database service (if available)
echo "<h2>Test 2: Drupal Database Service</h2>";

try {
    // Try to bootstrap Drupal
    $autoloader = require_once 'autoload.php';

    $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
    $kernel = \Drupal\Core\DrupalKernel::createFromRequest($request, $autoloader, 'prod');
    $kernel->boot();

    $database = \Drupal::database();

    echo "<p style='color: green;'>✅ Drupal database service loaded!</p>";

    // Test query through Drupal
    $result = $database->query("SELECT VERSION() as version")->fetchAssoc();
    echo "<p>MySQL Version (via Drupal): " . $result['version'] . "</p>";

    // Check if we can access a Drupal table
    $tables = $database->schema()->findTables('%');
    echo "<p>Found " . count($tables) . " database tables</p>";

} catch (Exception $e) {
    echo "<p style='color: orange;'>⚠️ Drupal bootstrap failed: " . $e->getMessage() . "</p>";
    echo "<p>This might be normal if Drupal isn't fully installed yet.</p>";
}

// Test 3: Environment variables (if used)
echo "<h2>Test 3: Environment Variables</h2>";

$env_vars = ['DB_HOST', 'DB_DATABASE', 'DB_USER', 'DB_PASSWORD'];
foreach ($env_vars as $var) {
    $value = $_ENV[$var] ?? getenv($var) ?? 'Not set';
    if ($var === 'DB_PASSWORD') {
        $value = $value !== 'Not set' ? '***hidden***' : 'Not set';
    }
    echo "<p>{$var}: {$value}</p>";
}

echo "<hr>";
echo "<p><small>Remove this file after testing!</small></p>";
?>
