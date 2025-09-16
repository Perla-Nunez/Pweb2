<?php

use Core\App;
use Core\Database;
use Core\Validator;



$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

error_log("Login recibido - Usuario: $username, Contraseña: " . str_repeat('*', strlen($password)));

if (!Validator::string($username, 3, 255) || !Validator::string($password, 3, 255)) {
    error_log("Validación fallida: campos vacíos o muy cortos.");
    return view('inicio.view.php', ['error' => 'Usuario y/o contraseña inválidos.']);
}

try {
    $db = App::resolve(Database::class);
    error_log(" Conexión a la base de datos exitosa.");
} catch (Exception $e) {
    error_log("Error de conexión: " . $e->getMessage());
    return view('inicio.view.php', ['error' => 'Error al conectar con la base de datos.']);
}

// Buscar por el campo username (no "Nombre")
$user = $db->query("SELECT * FROM users WHERE username = :username", [
    'username' => $username
])->find();

if (!$user) {
    error_log(" Usuario no encontrado: $username");
    return view('inicio.view.php', [
        'error' => 'Usuario no encontrado.',
        'username' => $username
    ]);
}

// Verificar contraseña
if (!password_verify($password, $user['contraseña'])) {
    error_log("Contraseña incorrecta para el usuario: $username");
    return view('inicio.view.php', [
        'error' => 'Contraseña incorrecta.',
        'username' => $username
    ]);
}

// Inicio de sesión exitoso
$_SESSION['user'] = [
    'id' => $user['idUsuario'],
    'username' => $user['Nombre'],
    'email' => $user['email'] ?? null
];

error_log("Inicio de sesión exitoso para $username");
// Verifica si ya hay una sesión activa
if (isset($_SESSION['user'])) {
    header("Location: /home");
    exit();
}
