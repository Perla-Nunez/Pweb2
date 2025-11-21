<?php
header("Content-Type: application/json; charset=utf-8");
include("config.php");
session_start();

// --------------------------------------------
// 2. VERIFICAR QUE EL USUARIO ESTÁ LOGEADO
// --------------------------------------------
if (!isset($_SESSION["id_usuario"])) {
    echo json_encode(["success" => false, "message" => "Usuario no autenticado"]);
    exit;
}

$id_usuario = $_SESSION["id_usuario"];

// --------------------------------------------
// 3. RECIBIR DATOS DEL FORMULARIO
// --------------------------------------------
$nombre      = $_POST["nombre"]      ?? null;
$apellido    = $_POST["apellido"]    ?? null;
$email       = $_POST["email"]       ?? null;
$contra      = $_POST["contra"]      ?? null;
$ubicacion   = $_POST["ubicacion"]   ?? null;

// Combinar nombre + apellido
$nombre_completo = trim($nombre . " " . $apellido);

// --------------------------------------------
// 4. PROCESAR AVATAR SI SE ENVIÓ
// --------------------------------------------
$avatar_url = null;

if (!empty($_FILES["avatar"]["name"])) {

    $nombreArchivo = $_FILES["avatar"]["name"];
    $tmp           = $_FILES["avatar"]["tmp_name"];

    $carpeta = "avatars/";

    // Crear carpeta si no existe
    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0777, true);
    }

    // Crear nombre único para la imagen
    $nuevoNombre = $id_usuario . "_" . time() . "_" . $nombreArchivo;

    $rutaDestino = $carpeta . $nuevoNombre;

    // Mover archivo
    if (move_uploaded_file($tmp, $rutaDestino)) {
        $avatar_url = $rutaDestino;
    }
}

// --------------------------------------------
// 5. PREPARAR SENTENCIA SEGÚN SI HUBO O NO AVATAR NUEVO
// --------------------------------------------
if ($avatar_url !== null) {
    // Con avatar
    $sql = "UPDATE usuario SET 
                nombre_completo = ?, 
                correo = ?, 
                password = ?, 
                nacionalidad = ?, 
                AVATAR_URL = ?
            WHERE id_usuario = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssi",
        $nombre_completo,
        $email,
        $contra,
        $ubicacion,
        $avatar_url,
        $id_usuario
    );
} else {
    // Sin avatar nuevo (no tocar url_contenido)
    $sql = "UPDATE usuario SET 
                nombre_completo = ?, 
                correo = ?, 
                password = ?, 
                nacionalidad = ?
            WHERE id_usuario = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssi",
        $nombre_completo,
        $email,
        $contra,
        $ubicacion,
        $id_usuario
    );
}

// --------------------------------------------
// 6. EJECUTAR Y RESPONDER
// --------------------------------------------
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Perfil actualizado correctamente"]);
} else {
    echo json_encode(["success" => false, "message" => "Error al actualizar: " . $conn->error]);
}

$stmt->close();
$conn->close();
