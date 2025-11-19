<?php

include("config.php"); 

header('Content-Type: application/json; charset=utf-8');
$response = ["success" => false, "data" => [], "message" => "Error desconocido"];

$termino_busqueda = $_GET['q'] ?? '';
$filtro_sql = '';
$productos = [];

if (!empty($termino_busqueda)) {
    $filtro_sql = " WHERE p.TITULO LIKE ? ";
}

$sql = "SELECT 
            p.ID_PRODUCTO, 
            p.TITULO, 
            p.PRECIO, 
            p.DESCRIPCION, 
            p.url_contenido, 
            u.nombre_completo AS nombre_vendedor 
        FROM 
            producto p
        JOIN 
            usuario u 
        ON 
            p.ID_USUARIO = u.id_usuario" 
        . $filtro_sql . " 
        ORDER BY 
            p.fecha_elaboracion DESC";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    $response['message'] = 'Error en la preparación de la consulta: ' . $conn->error;
    echo json_encode($response);
    exit;
}

if (!empty($termino_busqueda)) {
    $param_busqueda = "%" . $termino_busqueda . "%";
    $stmt->bind_param("s", $param_busqueda);
}

$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado) {
    if ($resultado->num_rows > 0) {
        // Recorrer los resultados
        while($fila = $resultado->fetch_assoc()) {
            $productos[] = $fila;
        }
        $response['success'] = true;
        $response['data'] = $productos;
        $response['message'] = empty($termino_busqueda) ? 'Productos obtenidos con éxito.' : 'Resultados de búsqueda obtenidos.';
    } else {
        $response['success'] = true;
        $response['data'] = []; // Asegura que data es un array vacío
        $response['message'] = empty($termino_busqueda) ? 'No hay productos disponibles.' : 'No se encontraron productos que coincidan con la búsqueda.';
    }
} else {
    $response['message'] = 'Error al ejecutar la consulta: ' . $conn->error;
}

$stmt->close();
$conn->close();
echo json_encode($response);
?>