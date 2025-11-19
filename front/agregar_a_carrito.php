<?php

include("config.php"); 

header('Content-Type: application/json; charset=utf-8');
$response = ["success" => false, "message" => "Error desconocido."];


$id_usuario = intval($_POST['id_usuario'] ?? 0); 
$id_producto = intval($_POST['id_producto'] ?? 0);
$cantidad = intval($_POST['cantidad'] ?? 1); // Asumimos cantidad 1 por defecto

if ($id_usuario <= 0) {
    $response['message'] = 'Error: ID de usuario no proporcionado. Debe iniciar sesión.';
    echo json_encode($response);
    exit;
}

if ($id_producto <= 0) {
    $response['message'] = 'Error: ID de producto no válido.';
    echo json_encode($response);
    exit;
}

if ($cantidad <= 0) {
    $cantidad = 1;
}

$sql_check = "SELECT ID_CARRITO, CANTIDAD FROM carrito WHERE ID_USUARIO = ? AND ID_PRODUCTO = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $id_usuario, $id_producto);
$stmt_check->execute();
$resultado_check = $stmt_check->get_result();

if ($resultado_check->num_rows > 0) {
    $item = $resultado_check->fetch_assoc();
    $nueva_cantidad = $item['CANTIDAD'] + $cantidad;
    
    $sql_update = "UPDATE carrito SET CANTIDAD = ? WHERE ID_CARRITO = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ii", $nueva_cantidad, $item['ID_CARRITO']);
    
    if ($stmt_update->execute()) {
        $response['success'] = true;
        $response['message'] = "Cantidad del producto actualizada en el carrito. Nueva cantidad: " . $nueva_cantidad;
    } else {
        $response['message'] = 'Error al actualizar la cantidad: ' . $conn->error;
    }
    $stmt_update->close();
} else {
    $sql_insert = "INSERT INTO carrito (ID_USUARIO, ID_PRODUCTO, CANTIDAD) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("iii", $id_usuario, $id_producto, $cantidad);

    if ($stmt_insert->execute()) {
        $response['success'] = true;
        $response['message'] = "Producto agregado al carrito con éxito.";
    } else {
        $response['message'] = 'Error al insertar producto: ' . $conn->error;
    }
    $stmt_insert->close();
}

$stmt_check->close();
$conn->close();
echo json_encode($response);
?>