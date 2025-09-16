<?php

use Core\App;
use Core\Database;
use Core\Validator;

$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$fechaNacimiento = $_POST['fechaNacimiento'] ?? null;
$genero = $_POST['genero'] ?? null;
$ciudad = $_POST['ciudad'] ?? null;
$pais = $_POST['pais'] ?? null;
$fotoPerfil = $_POST['fotoPerfil'] ?? null;
$biografia = $_POST['biografia'] ?? null;

// Validaciones básicas
if (
    !Validator::string($username, 3, 50) ||
    !Validator::email($email) ||
    !Validator::string($password, 8, 255) ||
    !preg_match('/[A-Z]/', $password) || // al menos una mayúscula
    !preg_match('/[a-z]/', $password) || // al menos una minúscula
    !preg_match('/[0-9]/', $password) || // al menos un número
    !preg_match('/[\W_]/', $password) || // al menos un carácter especial
    $password !== $confirmPassword
) {
    return view('sign.view.php', [
        'error' => 'Contraseña inválida: debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial.',
        'username' => $username,
        'email' => $email,
        'nombre' => $nombre
    ]);
}

try {
    $db = App::resolve(Database::class);

    // Verifica si ya existe el usuario o correo
    $existe = $db->query("SELECT 1 FROM users WHERE username = :username OR email = :email", [
        'username' => $username,
        'email' => $email
    ])->find();

    if ($existe) {
        return view('sign.view.php', [
            'error' => 'El nombre de usuario o correo ya están registrados.',
            'username' => $username,
            'email' => $email
        ]);
    }


    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $fotoPerfilNombre = null;

    if (isset($_FILES['fotoPerfil']) && $_FILES['fotoPerfil']['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = uniqid() . "_" . basename($_FILES['fotoPerfil']['name']);
        $rutaDestino = __DIR__ . '/../public/uploads/' . $nombreArchivo;
    
        if (move_uploaded_file($_FILES['fotoPerfil']['tmp_name'], $rutaDestino)) {
            $fotoPerfilNombre = 'uploads/' . $nombreArchivo; 
        } else {
            return view('sign.view.php', [
                'error' => 'Error al subir la imagen de perfil.'
            ]);
        }
    }
    
    // Inserta el nuevo usuario
    $db->query("INSERT INTO users 
        (Nombre, email, contraseña, username, fechaNacimiento, genero, ciudad, pais, fotoPerfil, biografia) 
        VALUES 
        (:nombre, :email, :password, :username, :fechaNacimiento, :genero, :ciudad, :pais, :fotoPerfil, :biografia)", [
            'nombre' => $nombre,
            'email' => $email,
            'password' => $hashed,
            'username' => $username,
            'fechaNacimiento' => $fechaNacimiento,
            'genero' => $genero,
            'ciudad' => $ciudad,
            'pais' => $pais,
           'fotoPerfil' => $fotoPerfilNombre,
            'biografia' => $biografia
    ]);

    // Redirección al login
    header("Location: /");
    exit();

} catch (Exception $e) {
    return view('sign.view.php', [
        'error' => 'Error al registrar: ' . $e->getMessage()
    ]);
}
?>
