<?php
// Simple database connection check script
try {
    $host = getenv('DB_HOST') ?: 'mysql';
    $port = getenv('DB_PORT') ?: '3306';
    $dbname = getenv('DB_DATABASE') ?: 'laravel';
    $username = getenv('DB_USERNAME') ?: 'laravel';
    $password = getenv('DB_PASSWORD') ?: 'secret';
    $connection = getenv('DB_CONNECTION') ?: 'mysql';
    
    if ($connection === 'mysql') {
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname";
    } else {
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    }
    
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_TIMEOUT => 2,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    exit(0); // Success
} catch (Exception $e) {
    exit(1); // Failure
}
