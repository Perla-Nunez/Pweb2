<?php
// Desactivar salida de errores HTML para no romper el JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);
session_start();

header('Content-Type: application/json; charset=utf-8');
$response = ["success" => false, "message" => "Error desconocido"];

try {
    // Asegúrate de que config.php incluye la conexión a la base de datos ($conn)
    include("config.php"); 

    // *** VERIFICACIÓN DE CONEXIÓN ***
    if ($conn->connect_error) {
        throw new Exception('Error de conexión a la base de datos: ' . $conn->connect_error);
    }

    // 1. Obtener y validar datos del POST
    $id_usuario = intval($_POST['id_usuario'] ?? 0);
    $post_origen = $_POST['post_origen'] ?? ''; 
    $titulo = trim($_POST['titulo'] ?? '');        
    $descripcion = trim($_POST['descripcion'] ?? ''); 
    $fecha_actual = date('Y-m-d H:i:s');

    // Manejar contenido multimedia
    $tipo_contenido = 'imagen'; // Valor por defecto
    $url_contenido = 'no_media_url'; 
    $target_dir = "uploads/"; 

    // Lógica segura para archivos
    if (!empty($_FILES['video']['name'])) {
        $tipo_contenido = 'video';
        $nombre_archivo = is_array($_FILES['video']['name']) ? $_FILES['video']['name'][0] : $_FILES['video']['name'];
        $url_contenido = $target_dir . basename($nombre_archivo);
    } elseif (!empty($_FILES['imagen']['name'])) {
        $tipo_contenido = 'imagen';
        // Solución al error fatal: Si llega un array, tomamos el primero
        $nombre_archivo = is_array($_FILES['imagen']['name']) ? $_FILES['imagen']['name'][0] : $_FILES['imagen']['name'];
        $url_contenido = $target_dir . basename($nombre_archivo);
    }

    // Validación de campos obligatorios
    if ($id_usuario <= 0) {
        throw new Exception('ID de usuario no válido. Vuelva a iniciar sesión.');
    }
    if (empty($titulo) || empty($descripcion) ) {
        throw new Exception('Faltan campos obligatorios (Título y Descripción).');
    }

    // 2. Lógica de Inserción Condicional
    $sql = '';
    $tipos = '';
    $valores = [];

    if ($post_origen === 'producto') {
        $precio = floatval($_POST['precio'] ?? 0.0);
        if ($precio <= 0) {
            throw new Exception('El producto debe tener un precio válido.');
        }
        
        $sql = "INSERT INTO producto (ID_USUARIO, TITULO, PRECIO, DESCRIPCION, tipo, url_contenido, fecha_elaboracion) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $tipos = 'isdssss';
        $valores = [$id_usuario, $titulo, $precio, $descripcion, $tipo_contenido, $url_contenido, $fecha_actual];

    } elseif ($post_origen === 'publicacion') {
        $id_mundial = intval($_POST['idMundial'] ?? 1);
        
        $sql = "INSERT INTO publicacion (id_usuario, id_mundial, titulo, descripcion, tipo, url_contenido, fecha_elaboracion) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $tipos = 'iisssss';
        $valores = [$id_usuario, $id_mundial, $titulo, $descripcion, $tipo_contenido, $url_contenido, $fecha_actual];

    } else {
        throw new Exception('Tipo de publicación no reconocido.');
    }

    // 3. Preparar y Ejecutar
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error en prepare(): ' . $conn->error);
    }

    // Enlace dinámico seguro
    $bind_params = array($tipos);
    foreach ($valores as $key => $value) {
        $bind_params[] = &$valores[$key]; 
    }

    $bind_success = call_user_func_array(array($stmt, 'bind_param'), $bind_params);
    if (!$bind_success) {
        throw new Exception('Falló bind_param.');
    }

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Publicación creada con éxito.';
        $response['destino'] = $post_origen; 
    } else {
        throw new Exception('Error al ejecutar: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = 'Error del Servidor: ' . $e->getMessage();
} catch (Error $e) {
    // Captura errores fatales de PHP (como el de basename)
    $response['success'] = false;
    $response['message'] = 'Error Fatal PHP: ' . $e->getMessage();
}

echo json_encode($response);
?>