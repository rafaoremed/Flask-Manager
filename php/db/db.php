<?php
$config = parse_ini_file(__DIR__ . '/../.env');

$host = $config['DB_HOST'];
$dbname = $config['DB_NAME'];
$user = $config['DB_USER'];
$pass = $config['DB_PASS'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
