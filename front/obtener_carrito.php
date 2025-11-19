<?php
include("config.php"); 

header('Content-Type: application/json; charset=utf-8');
$response = ["success" => false, "data" => [], "message" => "Error desconocido"];

// Por simplicidad, asumiremos que se envía por GET (ej: obtener_carrito.php?id_usuario=X)
$id_usuario = intval($_GET['id_usuario'] ?? 0);

if ($id_usuario <= 0) {
    $response['message'] = 'Error: ID de usuario no válido. Debe iniciar sesión.';
    echo json_encode($response);
    exit;
}

// Consulta JOIN para obtener detalles del producto y la cantidad en el carrito
$sql = "SELECT 
            c.ID_CARRITO,
            c.CANTIDAD, 
            p.ID_PRODUCTO, 
            p.TITULO, 
            p.PRECIO, 
            p.url_contenido 
        FROM 
            carrito c
        JOIN 
            producto p 
        ON 
            c.ID_PRODUCTO = p.ID_PRODUCTO
        WHERE
            c.ID_USUARIO = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    $response['message'] = 'Error en la preparación de la consulta: ' . $conn->error;
    echo json_encode($response);
    exit;
}

$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

$productos_carrito = [];
if ($resultado) {
    while ($producto = $resultado->fetch_assoc()) {
        $productos_carrito[] = $producto;
    }
    
    $response['success'] = true;
    $response['data'] = $productos_carrito;
    $response['message'] = count($productos_carrito) > 0 ? 'Productos en carrito obtenidos.' : 'El carrito está vacío.';
} else {
    $response['message'] = 'Error al ejecutar la consulta: ' . $conn->error;
}

$stmt->close();
$conn->close();
echo json_encode($response);
?>