<?php
session_start();
// Asegúrate de que config.php incluye la conexión a la base de datos ($conn)
include("config.php"); 

header('Content-Type: application/json; charset=utf-8');
$response = ["success" => false, "message" => "Error desconocido"];

// 1. Obtener y validar datos del POST
$id_usuario = intval($_POST['id_usuario'] ?? 0);
$post_origen = $_POST['post_origen'] ?? ''; 
$titulo = trim($_POST['titulo'] ?? '');        
$descripcion = trim($_POST['descripcion'] ?? ''); 
$fecha_actual = date('Y-m-d H:i:s');

// Manejar contenido multimedia opcional (valores por defecto para cumplir NOT NULL)
// El ENUM solo permite 'imagen' o 'video'
$tipo_contenido = 'imagen'; 
$url_contenido = 'no_media_url'; 

// Validación de campos obligatorios
if ($id_usuario <= 0) {
    $response['message'] = 'Error: ID de usuario no válido. Vuelva a iniciar sesión.';
    echo json_encode($response);
    exit;
}
if (empty($titulo) || empty($descripcion) ) {
    $response['message'] = 'Error: Faltan campos obligatorios (Título y Descripción).';
    echo json_encode($response);
    exit;
}

// 2. Lógica de Inserción Condicional
$sql = '';
$tipos = '';
$valores = [];
$tabla_destino = '';

// *** BLOQUE PARA PUBLICAR PRODUCTOS ***
if ($post_origen === 'producto') {
    $tabla_destino = 'producto';
    $precio = floatval($_POST['precio'] ?? 0.0);

    if ($precio <= 0) {
        $response['message'] = 'Error: El producto debe tener un precio válido.';
        echo json_encode($response);
        exit;
    }
    
    // Consulta para la tabla 'producto' (7 columnas)
    // CORRECCIÓN: Se incluye TITULO que es NOT NULL en la BD
    $sql = "INSERT INTO producto (ID_USUARIO, TITULO, PRECIO, DESCRIPCION, tipo, url_contenido, fecha_elaboracion) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    // Tipos: i (ID_USUARIO), s (TITULO), d (PRECIO - DECIMAL), s*4 (el resto de campos de texto/fecha)
    $tipos = 'isdssss';
    $valores = [$id_usuario, $titulo, $precio, $descripcion, $tipo_contenido, $url_contenido, $fecha_actual];

// *** BLOQUE PARA PUBLICAR EN LA COMUNIDAD ***
} elseif ($post_origen === 'publicacion') {
    $tabla_destino = 'publicacion';
    $id_mundial = intval($_POST['idMundial'] ?? 1); // ID_MUNDIAL por defecto (1)
    
    // Consulta para la tabla 'publicacion' (7 columnas)
    $sql = "INSERT INTO publicacion (id_usuario, id_mundial, titulo, descripcion, tipo, url_contenido, fecha_elaboracion) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    // Tipos: i (id_usuario), i (id_mundial), s*5 (el resto de campos de texto/fecha)
    // La cadena 'iisssss' es la correcta para 2 enteros y 5 strings
    $tipos = 'iisssss';
    $valores = [$id_usuario, $id_mundial, $titulo, $descripcion, $tipo_contenido, $url_contenido, $fecha_actual];

} else {
    $response['message'] = 'Tipo de publicación no reconocido.';
    echo json_encode($response);
    exit;
}

// 3. Ejecutar la Inserción
$stmt = $conn->prepare($sql);

if (!$stmt) {
    $response['message'] = 'Error en la preparación de la consulta: ' . $conn->error;
    echo json_encode($response);
    exit;
}

// Enlazar los parámetros dinámicamente:
// Esto es CRUCIAL para que bind_param funcione con un array de variables
$bind_params = array($tipos);
foreach ($valores as $key => $value) {
    // Es obligatorio pasar las variables por REFERENCIA para bind_param
    $bind_params[] = &$valores[$key]; 
}

// Intentar el bind_param y verificar si falla (soluciona el error de "red" en publicaciones)
// phpcs:ignore
$bind_success = call_user_func_array(array($stmt, 'bind_param'), $bind_params);

if (!$bind_success) {
    // Si falla el bind_param, capturamos el error de forma limpia
    $response['message'] = 'Error de enlace de parámetros. Tipos esperados: ' . $tipos . ' | Campos: ' . count($valores);
    echo json_encode($response);
    exit;
}

// 4. Ejecutar la sentencia
if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = 'Publicación creada con éxito en ' . ($post_origen === 'producto' ? 'la Tienda' : 'la Comunidad') . '.';
    $response['destino'] = $post_origen; 
} else {
    // Capturar errores de ejecución (ej. Foreign Key, restricción NOT NULL)
    $response['message'] = 'Error al guardar la publicación: ' . $stmt->error;
}

$stmt->close();
$conn->close();
echo json_encode($response);

?>