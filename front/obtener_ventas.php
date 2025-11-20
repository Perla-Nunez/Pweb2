<?php
include("config.php");
session_start();

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
error_reporting(E_ALL);

$response = ["success" => false, "data" => [], "total_general" => 0];

// 1. Verificar sesión (o usar ID fijo para pruebas si no tienes login activo)
if (!isset($_SESSION['id_usuario'])) {
    // PARA PRUEBAS RÁPIDAS: Descomenta la siguiente línea si no estás logueado
    // $_SESSION['id_usuario'] = 1; 
    
    if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(["success" => false, "message" => "No hay sesión PHP activa. ID Usuario desconocido."]);
    exit;
    }
}

$id_usuario = $_SESSION['id_usuario'];

try {
    // 2. Consulta SQL: Une productos con sus detalles de compra
    // Sumamos la cantidad vendida y multiplicamos precio por cantidad para el total
    $sql = "SELECT 
                p.TITULO, 
                p.url_contenido,
                COALESCE(SUM(dc.CANTIDAD), 0) as cantidad_vendida,
                COALESCE(SUM(dc.CANTIDAD * dc.PRECIO_UNITARIO), 0) as total_ganado
            FROM producto p
            LEFT JOIN detalle_compra dc ON p.ID_PRODUCTO = dc.ID_PRODUCTO
            WHERE p.ID_USUARIO = ?
            GROUP BY p.ID_PRODUCTO
            HAVING cantidad_vendida > 0"; // Opcional: solo mostrar productos que han vendido algo

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    $total_acumulado = 0;
    $ventas = [];

    while ($row = $result->fetch_assoc()) {
        $ventas[] = $row;
        $total_acumulado += $row['total_ganado'];
    }

    $response['success'] = true;
    $response['data'] = $ventas;
    $response['total_general'] = $total_acumulado;

} catch (Exception $e) {
    $response['message'] = "Error en BD: " . $e->getMessage();
}

echo json_encode($response);
$conn->close();
?>