<?php
// Real environment variables win; then the local file; then local defaults
$local = file_exists(__DIR__ . '/config.local.php') ? require __DIR__ . '/config.local.php' : [];

$host = getenv('DB_HOST') ?: ($local['DB_HOST'] ?? 'localhost');
$port = getenv('DB_PORT') ?: ($local['DB_PORT'] ?? '3306');
$name = getenv('DB_NAME') ?: ($local['DB_NAME'] ?? 'netheret');
$user = getenv('DB_USER') ?: ($local['DB_USER'] ?? 'root');
$pass = getenv('DB_PASS') ?: ($local['DB_PASS'] ?? '');

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 5,
        ]
    );
} catch (PDOException $e) {
    error_log($e->getMessage());       // details go to the log, not the screen
    exit('Database connection failed.');
}