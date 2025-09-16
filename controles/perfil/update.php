<?php

use Core\App;
use Core\Database;
use Core\Validator;

session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['user'])) {
    header("Location: /");
    exit();
}

$userId = $_SESSION['user']['id'];

// Recoger datos del formulario
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$fechaNacimiento = $_POST['fechaNacimiento'] ?? null;
$ciudad = $_POST['ciudad'] ?? null;
$pais = $_POST['pais'] ?? null;
$biografia = $_POST['biografia'] ?? null;

// Validaciones básicas
if (!Validator::string($username, 3, 50) || !Validator::email($email)) {
    die("Datos inválidos.");
}

// Validación y hash de contraseña si fue enviada
$passwordUpdate = '';
if (!empty($password)) {
    if ($password !== $confirmPassword || !Validator::string($password, 8, 255)) {
        die("Las contraseñas no coinciden o no son válidas.");
    }
    $passwordUpdate = password_hash($password, PASSWORD_DEFAULT);
}

// Manejar la imagen de perfil
$fotoPerfilPath = null;
if (isset($_FILES['fotoPerfil']) && $_FILES['fotoPerfil']['error'] === UPLOAD_ERR_OK) {
    $tmpName = $_FILES['fotoPerfil']['tmp_name'];
    $ext = pathinfo($_FILES['fotoPerfil']['name'], PATHINFO_EXTENSION);
    $fotoPerfilPath = "uploads/" . uniqid('perfil_', true) . "." . $ext;
    move_uploaded_file($tmpName, $fotoPerfilPath);
}

// Conexión con la base de datos
$db = App::resolve(Database::class);

// Construir la consulta SQL de actualización
$sql = "UPDATE users SET
    Nombre = :nombre,
    username = :username,
    email = :email,
    fechaNacimiento = :fechaNacimiento,
    ciudad = :ciudad,
    pais = :pais,
    biografia = :biografia";

$params = [
    'nombre' => $nombre,
    'username' => $username,
    'email' => $email,
    'fechaNacimiento' => $fechaNacimiento,
    'ciudad' => $ciudad,
    'pais' => $pais,
    'biografia' => $biografia,
    'id' => $userId
];

// Solo actualizar contraseña si fue proporcionada
if (!empty($passwordUpdate)) {
    $sql .= ", contraseña = :password";
    $params['password'] = $passwordUpdate;
}

// Solo actualizar la imagen si fue subida
if ($fotoPerfilPath) {
    $sql .= ", fotoPerfil = :fotoPerfil";
    $params['fotoPerfil'] = $fotoPerfilPath;
}

$sql .= " WHERE idUsuario = :id";

// Ejecutar la consulta
$db->query($sql, $params);

// Redireccionar de vuelta al perfil o dashboard
header("Location: /perfil");
exit();
