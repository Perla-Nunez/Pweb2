<?php
include 'config.php';

header('Content-Type: application/json');

// 1. Verificar que los datos necesarios estén presentes
if (!isset($_POST['id_publicacion']) || !isset($_POST['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Faltan parámetros necesarios.']);
    exit;
}

$id_publicacion = $_POST['id_publicacion'];
$id_usuario = $_POST['id_usuario'];
$conn->begin_transaction(); 

try {
    // 2. Verificar si el usuario ya dio like a esta publicación
    $sql_check = "SELECT id_like FROM likes WHERE id_publicacion = ? AND id_usuario = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ii", $id_publicacion, $id_usuario);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    $action = '';

    if ($result_check->num_rows > 0) {
        // 3. El like existe: Eliminar (quitar like)
        $sql_delete = "DELETE FROM likes WHERE id_publicacion = ? AND id_usuario = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("ii", $id_publicacion, $id_usuario);
        $stmt_delete->execute();
        $action = 'unliked';
    } else {
        // 4. El like no existe: Insertar (dar like)
        $sql_insert = "INSERT INTO likes (id_publicacion, id_usuario) VALUES (?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ii", $id_publicacion, $id_usuario);
        $stmt_insert->execute();
        $action = 'liked';
    }

    // 5. Contar el total de likes para la publicación (sin importar la acción)
    $sql_count = "SELECT COUNT(*) as total FROM likes WHERE id_publicacion = ?";
    $stmt_count = $conn->prepare($sql_count);
    $stmt_count->bind_param("i", $id_publicacion);
    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    $total_likes = $result_count->fetch_assoc()['total'];

    $conn->commit(); 
    
    // 6. Devolver la respuesta al cliente
    echo json_encode([
        'success' => true, 
        'action' => $action,
        'total_likes' => (int)$total_likes, 
        'message' => 'Like procesado con éxito.'
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()]);
}

$conn->close();
?>