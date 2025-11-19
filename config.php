<?php
$host = getenv("DB_HOST");
$port = getenv("DB_PORT");
$user = getenv("DB_USER");
$password = getenv("DB_PASSWORD");
$db = getenv("DB_NAME");

$conn = new mysqli($host, $user, $password, $db, $port);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>