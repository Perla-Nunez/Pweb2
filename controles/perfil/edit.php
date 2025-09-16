<?php
use Core\App;
use Core\Database;

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user'])) {
    header("Location: /");
    exit();
}

// Obtener el ID del usuario desde la sesión
$userId = $_SESSION['user']['id'];

// Conectar con la base de datos
$db = App::resolve(Database::class);

// Obtener los datos actuales del usuario
$user = $db->query("SELECT * FROM users WHERE idUsuario = :id", [
    'id' => $userId
])->find();

// Verificar si el usuario existe
if (!$user) {
    echo "Usuario no encontrado.";
    exit();
}

// Cargar la vista de edición con los datos actuales del usuario
view('edit.view.php', ['user' => $user]);
?>
