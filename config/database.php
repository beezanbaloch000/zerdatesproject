<?php
declare(strict_types=1);

// XAMPP default MySQL settings. Change these values if your setup differs.
$host = getenv('ZERWAAN_DB_HOST') ?: 'localhost';
$db = getenv('ZERWAAN_DB_NAME') ?: 'zerwaan_dates';
$user = getenv('ZERWAAN_DB_USER') ?: 'root';
$pass = getenv('ZERWAAN_DB_PASS') ?: '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $exception) {
    http_response_code(500);
    exit('The application is temporarily unavailable.');
}
