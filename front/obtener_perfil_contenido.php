<?php
// 1. Evitar que los errores de PHP se impriman como HTML y rompan el JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

include("config.php");
session_start();

// 2. Asegurar que siempre devolvemos JSON
header('Content-Type: application/json; charset=utf-8');

$response = ["success" => false, "posts" => [], "productos" => [], "message" => "Error desconocido"];

try {
    // Verificar conexión a BD
    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }

    // Verificar sesión
    if (!isset($_SESSION['id_usuario'])) {
        throw new Exception("Usuario no autenticado.");
    }

    $id_usuario = $_SESSION['id_usuario'];

    // -------------------------------------------------------
    // 3. Obtener Publicaciones (Tabla: publicacion - minúsculas)
    // -------------------------------------------------------
    $sql_posts = "SELECT 
                    id_publicacion, titulo, descripcion, tipo, url_contenido, fecha_elaboracion 
                  FROM publicacion 
                  WHERE id_usuario = ? 
                  ORDER BY fecha_elaboracion DESC";

    $stmt = $conn->prepare($sql_posts);
    if (!$stmt) {
        throw new Exception("Error SQL Publicaciones: " . $conn->error);
    }
    
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        // Normalizamos las claves para que JS no se confunda
        $response['posts'][] = $row;
    }
    $stmt->close();

    // -------------------------------------------------------
    // 4. Obtener Productos (Tabla: producto - MAYÚSCULAS)
    // -------------------------------------------------------
    // Según tu BD.txt, la tabla producto usa mayúsculas para las columnas
    $sql_prod = "SELECT 
                    ID_PRODUCTO, TITULO, PRECIO, DESCRIPCION, tipo, url_contenido, fecha_elaboracion 
                 FROM producto 
                 WHERE ID_USUARIO = ? 
                 ORDER BY fecha_elaboracion DESC";

    $stmt = $conn->prepare($sql_prod);
    if (!$stmt) {
        throw new Exception("Error SQL Productos: " . $conn->error);
    }

    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $response['productos'][] = $row;
    }
    $stmt->close();

    $response['success'] = true;

} catch (Exception $e) {
    // Capturamos cualquier error y lo enviamos como mensaje JSON válido
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
?>