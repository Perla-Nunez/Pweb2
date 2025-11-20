<?php
include("config.php"); 
session_start(); // Necesario para saber si el usuario actual dio like

// 1. Configuración de errores para JSON (Evita que errores HTML rompan el JSON)
ini_set('display_errors', 0);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

$response = ["success" => false, "data" => null, "message" => "Error desconocido"];

try {
    // 2. Validar ID del post
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception('No se proporcionó un ID de publicación.');
    }
    $id_publicacion = intval($_GET['id']);

    // 3. Obtener ID del usuario que está viendo (para saber si dio like y botón editar)
    // Si no hay sesión, asumimos ID 0 (invitado)
    $id_usuario_visor = isset($_SESSION['id_usuario']) ? intval($_SESSION['id_usuario']) : 0;

    // 4. Consulta SQL corregida
    // ? #1 -> id_usuario_visor (dentro del SELECT de likes)
    // ? #2 -> id_publicacion (en el WHERE final)
    $sql = "SELECT 
        p.id_publicacion, 
        p.id_usuario, 
        p.titulo, 
        p.descripcion, 
        p.tipo, 
        p.url_contenido, 
        p.fecha_elaboracion,
        u.nombre_completo AS autor,
        COALESCE(l.total_likes, 0) AS total_likes,
        (SELECT COUNT(*) FROM likes WHERE id_publicacion = p.id_publicacion AND id_usuario = ?) as ya_dio_like
    FROM 
        publicacion p
    INNER JOIN 
        usuario u ON p.id_usuario = u.id_usuario
    LEFT JOIN (
        SELECT id_publicacion, COUNT(*) AS total_likes 
        FROM likes 
        GROUP BY id_publicacion
    ) l ON p.id_publicacion = l.id_publicacion
    WHERE 
        p.id_publicacion = ?"; 

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error SQL: ' . $conn->error);
    }

    
    $stmt->bind_param("ii", $id_usuario_visor, $id_publicacion);
    
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($fila = $resultado->fetch_assoc()) {
        $response['success'] = true;
        $response['data'] = $fila;
    } else {
        $response['message'] = 'Publicación no encontrada.';
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>