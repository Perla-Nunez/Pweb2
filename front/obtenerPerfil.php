<?php
session_start();
include("config.php"); 

header('Content-Type: application/json; charset=utf-8');

$response = ["success" => false, "message" => ""];

// Verificar si hay una sesión activa y un ID
if (!isset($_SESSION['id_usuario']) || empty($_SESSION['id_usuario'])) {
    $response['message'] = 'No hay sesión activa.';
    echo json_encode($response);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Consulta a la base de datos (SELECT)
$stmt = $conn->prepare("SELECT nombre_completo, correo, nacionalidad, AVATAR_URL, rol FROM usuario WHERE id_usuario = ? LIMIT 1");

if (!$stmt) {
    $response['message'] = 'Error en la preparación de la consulta: ' . $conn->error;
    echo json_encode($response);
    exit;
}

$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado && $resultado->num_rows === 1) {
    $usuario = $resultado->fetch_assoc();
    
    //  Devolver los datos del usuario
    $response['success'] = true;
    $response['message'] = 'Datos de perfil obtenidos con éxito.';
    // Devolvemos solo los datos necesarios
    $response['data'] = [
    'nombre_completo' => htmlspecialchars($usuario['nombre_completo']),
    'correo' => htmlspecialchars($usuario['correo']),
    'nacionalidad' => htmlspecialchars($usuario['nacionalidad']),
    'rol' => htmlspecialchars($usuario['rol']),
    'AVATAR_URL' => $usuario['AVATAR_URL'] ? htmlspecialchars($usuario['AVATAR_URL']) : null
];

} else {
    $response['message'] = 'Usuario no encontrado en la base de datos.';
}

$stmt->close();
$conn->close();
echo json_encode($response);
?>