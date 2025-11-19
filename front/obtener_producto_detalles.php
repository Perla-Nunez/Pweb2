<?php
// obtener_producto_detalles.php

// Asegúrate de incluir la conexión a la base de datos ($conn)
include("config.php"); 

header('Content-Type: application/json; charset=utf-8');
$response = ["success" => false, "data" => null, "message" => "Error desconocido"];

// 1. Obtener el ID del producto de la URL (GET)
$id_producto = intval($_GET['id'] ?? 0);

if ($id_producto <= 0) {
    $response['message'] = 'Error: ID de producto no proporcionado o no válido.';
    echo json_encode($response);
    exit;
}

// 2. Consulta JOIN para obtener producto y nombre del usuario (usando PreparedStatement)
$sql = "SELECT 
            p.ID_PRODUCTO, 
            p.TITULO, 
            p.PRECIO, 
            p.DESCRIPCION, 
            p.tipo,
            p.url_contenido, 
            u.nombre_completo AS nombre_vendedor 
        FROM 
            producto p
        JOIN 
            usuario u 
        ON 
            p.ID_USUARIO = u.id_usuario
        WHERE
            p.ID_PRODUCTO = ? 
        LIMIT 1";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    $response['message'] = 'Error en la preparación de la consulta: ' . $conn->error;
    echo json_encode($response);
    exit;
}

// Enlazar el parámetro (el ID)
$stmt->bind_param("i", $id_producto);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado && $resultado->num_rows === 1) {
    $producto = $resultado->fetch_assoc();
    $response['success'] = true;
    $response['data'] = $producto;
    $response['message'] = 'Producto obtenido con éxito.';
} else {
    $response['success'] = false;
    $response['message'] = 'Producto no encontrado.';
}

$stmt->close();
$conn->close();
echo json_encode($response);
?>