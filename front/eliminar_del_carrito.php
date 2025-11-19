<?php
include("config.php"); 

header('Content-Type: application/json; charset=utf-8');
$response = ["success" => false, "message" => "Error desconocido"];

// El ID_CARRITO se envía por POST desde el frontend
$id_carrito = intval($_POST['id_carrito'] ?? 0);

if ($id_carrito <= 0) {
    $response['message'] = 'Error: ID de carrito no proporcionado o no válido.';
    echo json_encode($response);
    exit;
}

// Consulta DELETE para eliminar el producto del carrito
$sql = "DELETE FROM carrito WHERE ID_CARRITO = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    $response['message'] = 'Error en la preparación de la consulta: ' . $conn->error;
    echo json_encode($response);
    exit;
}

$stmt->bind_param("i", $id_carrito);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $response['success'] = true;
        $response['message'] = "Producto eliminado del carrito con éxito.";
    } else {
        $response['message'] = "No se encontró el producto con ID_CARRITO = $id_carrito en el carrito.";
    }
} else {
    $response['message'] = 'Error al ejecutar la eliminación: ' . $stmt->error;
}

$stmt->close();
$conn->close();
echo json_encode($response);
?>