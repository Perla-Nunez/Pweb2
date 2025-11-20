<?php
include("config.php"); 

// Desactivar la visualización de errores en pantalla para no romper el JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');
$response = ["success" => false, "data" => null, "message" => "Error desconocido"];

// 1. Validar que llegue el ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $response['message'] = 'No se proporcionó un ID de publicación.';
    echo json_encode($response);
    exit;
}

$id_publicacion = intval($_GET['id']);

// 2. Consulta SQL corregida
// Se cambió [ID_POST_A_BUSCAR] por ?
$sql = "SELECT 
    p.id_publicacion, 
    p.titulo, 
    p.descripcion, 
    p.tipo, 
    p.url_contenido, 
    p.fecha_elaboracion,
    u.nombre_completo AS autor,
    COALESCE(l.total_likes, 0) AS total_likes,
    (SELECT COUNT(*) FROM likes WHERE id_publicacion = p.id_publicacion AND id_usuario = 1) as ya_dio_like
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
    p.id_publicacion = ?";  // <--- AQUÍ ESTABA EL ERROR

$stmt = $conn->prepare($sql);

if (!$stmt) {
    // Si falla la preparación, enviamos el error en formato JSON, no HTML
    $response['message'] = 'Error SQL al preparar: ' . $conn->error;
    echo json_encode($response);
    exit;
}

$stmt->bind_param("i", $id_publicacion);
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
echo json_encode($response);
?>