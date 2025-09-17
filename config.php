<?php
$host = "localhost";
$user = "root";
$password = "";
$db = "mundiales_redsocial";

// Crear conexión
$conn = new mysqli($host, $user, $password, $db);

// Verifica conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>