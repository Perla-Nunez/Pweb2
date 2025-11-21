<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);
session_start();
header('Content-Type: application/json; charset=utf-8');
$response = ["success" => false, "message" => "Error desconocido"];

try {
    include("config.php"); 
    if ($conn->connect_error) throw new Exception('Error BD: ' . $conn->connect_error);

    // Datos básicos
    $id_usuario = intval($_POST['id_usuario'] ?? 0);
    $post_origen = $_POST['post_origen'] ?? ''; 
    $titulo = trim($_POST['titulo'] ?? '');        
    $descripcion = trim($_POST['descripcion'] ?? ''); 
    $id_edicion = isset($_POST['id_edicion']) && !empty($_POST['id_edicion']) ? intval($_POST['id_edicion']) : null;
    $fecha_actual = date('Y-m-d H:i:s');

    if ($id_usuario <= 0 || empty($titulo) || empty($descripcion)) {
        throw new Exception('Faltan datos obligatorios.');
    }

    // Manejo de Archivos (Solo si se suben nuevos)
    $tipo_contenido = null; 
    $url_contenido = null;
    $hay_archivo_nuevo = false;

    $target_dir = "uploads/";
    if (!empty($_FILES['video']['name'])) {
    $tipo_contenido = 'video';

    $nombre = is_array($_FILES['video']['name']) ? $_FILES['video']['name'][0] : $_FILES['video']['name'];
    $url_contenido = $target_dir . basename($nombre);

    // === Mover archivo al servidor ===
    if (!move_uploaded_file($_FILES['video']['tmp_name'], $url_contenido)) {
        throw new Exception("Error al guardar el video en el servidor");
    }
    $hay_archivo_nuevo = true;
    
    } elseif (!empty($_FILES['imagen']['name'])) {
        $tipo_contenido = 'imagen';

        $nombre = is_array($_FILES['imagen']['name']) ? $_FILES['imagen']['name'][0] : $_FILES['imagen']['name'];
        $url_contenido = $target_dir . basename($nombre);

        // === Mover archivo al servidor ===
        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $url_contenido)) {
            throw new Exception("Error al guardar la imagen en el servidor");
        }

        $hay_archivo_nuevo = true;
    }


    // --- LÓGICA SQL DINÁMICA ---
    $sql = "";
    $params = [];
    $types = "";

    if ($id_edicion) {
        // === UPDATE (MODIFICAR) ===
        if ($post_origen === 'producto') {
            $precio = floatval($_POST['precio'] ?? 0);
            
            if ($hay_archivo_nuevo) {
                $sql = "UPDATE producto SET TITULO=?, DESCRIPCION=?, PRECIO=?, tipo=?, url_contenido=? WHERE ID_PRODUCTO=? AND ID_USUARIO=?";
                $types = "ssdssii";
                $params = [$titulo, $descripcion, $precio, $tipo_contenido, $url_contenido, $id_edicion, $id_usuario];
            } else {
                // No tocamos la imagen/video si no subieron uno nuevo
                $sql = "UPDATE producto SET TITULO=?, DESCRIPCION=?, PRECIO=? WHERE ID_PRODUCTO=? AND ID_USUARIO=?";
                $types = "ssdii";
                $params = [$titulo, $descripcion, $precio, $id_edicion, $id_usuario];
            }
        } else {
            // Publicación
            if ($hay_archivo_nuevo) {
                $sql = "UPDATE publicacion SET titulo=?, descripcion=?, tipo=?, url_contenido=? WHERE id_publicacion=? AND id_usuario=?";
                $types = "ssssii";
                $params = [$titulo, $descripcion, $tipo_contenido, $url_contenido, $id_edicion, $id_usuario];
            } else {
                $sql = "UPDATE publicacion SET titulo=?, descripcion=? WHERE id_publicacion=? AND id_usuario=?";
                $types = "ssii";
                $params = [$titulo, $descripcion, $id_edicion, $id_usuario];
            }
        }
        $action_msg = "actualizada";

    } else {
        // === INSERT (CREAR NUEVO) - Lógica Original ===
        // Forzamos valores por defecto para archivos si es nuevo
        if (!$hay_archivo_nuevo) {
            $tipo_contenido = 'imagen'; 
            $url_contenido = 'no_media_url';
        }

        if ($post_origen === 'producto') {
            $precio = floatval($_POST['precio'] ?? 0);
            $sql = "INSERT INTO producto (ID_USUARIO, TITULO, PRECIO, DESCRIPCION, tipo, url_contenido, fecha_elaboracion) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $types = "isdssss";
            $params = [$id_usuario, $titulo, $precio, $descripcion, $tipo_contenido, $url_contenido, $fecha_actual];
        } else {
            $id_mundial = intval($_POST['idMundial'] ?? 1);
            $sql = "INSERT INTO publicacion (id_usuario, id_mundial, titulo, descripcion, tipo, url_contenido, fecha_elaboracion) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $types = "iisssss";
            $params = [$id_usuario, $id_mundial, $titulo, $descripcion, $tipo_contenido, $url_contenido, $fecha_actual];
        }
        $action_msg = "creada";
    }

    // Ejecución
    $stmt = $conn->prepare($sql);
    if (!$stmt) throw new Exception("Error Prepare: " . $conn->error);
    
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = "Publicación $action_msg con éxito.";
        $response['destino'] = $post_origen;
    } else {
        throw new Exception("Error Execute: " . $stmt->error);
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>