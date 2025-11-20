<?php
// 1. Desactivar salida de errores HTML para no romper el JSON
ini_set('display_errors', 0); 
ini_set('log_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

$response = ['success' => false, 'message' => 'Error desconocido'];

try {
    // Validar que existe config.php
    if (!file_exists('config.php')) {
        throw new Exception("No se encuentra el archivo config.php");
    }
    include("config.php");

    // Verificar conexión
    if ($conn->connect_error) {
        throw new Exception("Error de conexión BD: " . $conn->connect_error);
    }

    // Validar entrada
    if (!isset($_POST['id_usuario'])) {
        throw new Exception('Usuario no identificado (Falta id_usuario).');
    }

    $id_usuario = intval($_POST['id_usuario']);

    // 2. Iniciar Transacción (Asegúrate de que esta línea tenga punto y coma al final)
    $conn->begin_transaction(); 

    // A. Obtener items del carrito
    $sql_carrito = "SELECT c.ID_PRODUCTO, c.CANTIDAD, p.PRECIO 
                    FROM carrito c 
                    INNER JOIN producto p ON c.ID_PRODUCTO = p.ID_PRODUCTO 
                    WHERE c.ID_USUARIO = ?";
    
    $stmt = $conn->prepare($sql_carrito);
    if (!$stmt) {
        throw new Exception("Error SQL Carrito: " . $conn->error);
    }
    
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultado_carrito = $stmt->get_result();

    $items = [];
    $total_compra = 0;

    while ($row = $resultado_carrito->fetch_assoc()) {
        $items[] = $row;
        $total_compra += ($row['PRECIO'] * $row['CANTIDAD']);
    }

    if (empty($items)) {
        throw new Exception("El carrito está vacío o los productos no existen.");
    }

    // B. Insertar Compra
    $sql_compra = "INSERT INTO compra (ID_USUARIO, TOTAL, METODO_PAGO, ESTADO) VALUES (?, ?, 'Tarjeta', 'Completado')";
    $stmt_compra = $conn->prepare($sql_compra);
    if (!$stmt_compra) {
        throw new Exception("Error SQL Compra: " . $conn->error);
    }

    $stmt_compra->bind_param("id", $id_usuario, $total_compra);
    
    if (!$stmt_compra->execute()) {
        throw new Exception("Error al guardar compra: " . $stmt_compra->error);
    }
    
    $id_compra = $conn->insert_id;

    // C. Insertar Detalles
    $sql_detalle = "INSERT INTO detalle_compra (ID_COMPRA, ID_PRODUCTO, CANTIDAD, PRECIO_UNITARIO) VALUES (?, ?, ?, ?)";
    $stmt_detalle = $conn->prepare($sql_detalle);
    if (!$stmt_detalle) {
        throw new Exception("Error SQL Detalle: " . $conn->error);
    }

    foreach ($items as $item) {
        $stmt_detalle->bind_param("iiid", $id_compra, $item['ID_PRODUCTO'], $item['CANTIDAD'], $item['PRECIO']);
        if (!$stmt_detalle->execute()) {
            throw new Exception("Error al guardar detalle item ID " . $item['ID_PRODUCTO']);
        }
    }

    // D. Vaciar Carrito
    $sql_borrar = "DELETE FROM carrito WHERE ID_USUARIO = ?";
    $stmt_borrar = $conn->prepare($sql_borrar);
    $stmt_borrar->bind_param("i", $id_usuario);
    $stmt_borrar->execute();

    // Confirmar todo
    $conn->commit();

    $response['success'] = true;
    $response['message'] = 'Compra procesada correctamente';
    $response['total'] = $total_compra;

} catch (Throwable $e) {
    // Captura cualquier error y revierte cambios
    if (isset($conn)) {
        $conn->rollback();
    }
    $response['message'] = 'Error del servidor: ' . $e->getMessage();
}

// Devolver respuesta JSON limpia
echo json_encode($response);

// Cerrar conexión
if (isset($conn)) {
    $conn->close();
}
?>