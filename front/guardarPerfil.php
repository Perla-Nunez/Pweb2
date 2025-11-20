<?php
include("config.php");
session_start();

header('Content-Type: application/json; charset=utf-8');
$response = ["success" => false, "message" => "Error desconocido"];

// 1. Verificar sesión
if (!isset($_SESSION['id_usuario'])) {
    $response['message'] = 'Sesión no iniciada.';
    echo json_encode($response);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// 2. Recibir datos del formulario (JSON o POST normal)
$input = json_decode(file_get_contents('php://input'), true);

$nombre = $input['nombre'] ?? $_POST['nombre'] ?? '';
$apellido = $input['apellido'] ?? $_POST['apellido'] ?? '';
$correo = $input['email'] ?? $_POST['email'] ?? '';
$password = $input['contra'] ?? $_POST['contra'] ?? '';
$nacionalidad = $input['ubicacion'] ?? $_POST['ubicacion'] ?? '';

// Validaciones básicas
if (empty($nombre) || empty($correo) || empty($password)) {
    $response['message'] = 'Nombre, correo y contraseña son obligatorios.';
    echo json_encode($response);
    exit;
}

// Unir nombre y apellido para el campo 'nombre_completo' de la BD
$nombre_completo = trim("$nombre $apellido");

// 3. Actualizar en Base de Datos
$sql = "UPDATE usuario SET nombre_completo = ?, correo = ?, password = ?, nacionalidad = ? WHERE id_usuario = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    $response['message'] = 'Error SQL: ' . $conn->error;
    echo json_encode($response);
    exit;
}

$stmt->bind_param("ssssi", $nombre_completo, $correo, $password, $nacionalidad, $id_usuario);

if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = 'Perfil actualizado correctamente.';
} else {
    $response['message'] = 'Error al actualizar: ' . $stmt->error;
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>