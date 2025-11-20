<?php
include("config.php"); 

header('Content-Type: application/json; charset=utf-8');
$response = ["success" => false, "data" => [], "message" => "Error desconocido"];

// Capturar término de búsqueda si existe
$termino_busqueda = $_GET['q'] ?? '';
$filtro_sql = '';
$publicaciones = [];

// Filtro de búsqueda por título o descripción
if (!empty($termino_busqueda)) {
    $filtro_sql = " WHERE (p.titulo LIKE ? OR p.descripcion LIKE ?) ";
}

// Consulta SQL: Unimos publicacion con usuario para saber quién publicó
$sql = "SELECT 
            p.id_publicacion, 
            p.titulo, 
            p.descripcion, 
            p.tipo, 
            p.url_contenido, 
            p.fecha_elaboracion,
            u.nombre_completo AS autor 
        FROM 
            publicacion p
        JOIN 
            usuario u 
        ON 
            p.id_usuario = u.id_usuario" 
        . $filtro_sql . " 
        ORDER BY 
            p.fecha_elaboracion DESC";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    $response['message'] = 'Error en la preparación: ' . $conn->error;
    echo json_encode($response);
    exit;
}

// Bind de parámetros si hay búsqueda
if (!empty($termino_busqueda)) {
    $param_busqueda = "%" . $termino_busqueda . "%";
    $stmt->bind_param("ss", $param_busqueda, $param_busqueda);
}

$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado) {
    while($fila = $resultado->fetch_assoc()) {
        $publicaciones[] = $fila;
    }
    $response['success'] = true;
    $response['data'] = $publicaciones;
} else {
    $response['message'] = 'Error SQL: ' . $conn->error;
}

$stmt->close();
$conn->close();
echo json_encode($response);
?>