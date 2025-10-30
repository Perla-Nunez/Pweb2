<?php
// inicioSesion.php (modo depuración — eliminar cuando se arregle todo)
session_start();
include("config.php");

// Para evitar que warnings se impriman ANTES del JSON, capturamos salida
ob_start();
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

$response = [
    "success" => false,
    "message" => "Error desconocido"
];

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $response['message'] = 'Método no permitido';
    $response['debug_output'] = ob_get_clean();
    echo json_encode($response);
    exit;
}

$input = trim($_POST['correo'] ?? '');
$contrasena = $_POST['contrasena'] ?? '';

if ($input === '' || $contrasena === '') {
    $response['message'] = 'Llene todos los campos.';
    $response['debug_output'] = ob_get_clean();
    echo json_encode($response);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM usuario WHERE correo = ? LIMIT 1");
if (!$stmt) {
    $response['message'] = 'Error en la preparación de la consulta: ' . $conn->error;
    $response['debug_output'] = ob_get_clean();
    echo json_encode($response);
    exit;
}

$stmt->bind_param("s", $input);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado && $resultado->num_rows === 1) {
    $usuario = $resultado->fetch_assoc();

    // Normalizamos las claves a minúsculas para evitar problemas de mayúsculas
    $usuario = array_change_key_case($usuario, CASE_LOWER);

    $hash = $usuario['password'] ?? null;

    // Información de depuración
    $response['debug'] = [
        'found_user' => true,
        'db_keys' => array_keys($usuario),
        'hash_exists' => $hash !== null,
        'hash_length' => $hash ? strlen($hash) : 0,
        'hash_preview' => $hash ? substr($hash, 0, 25) : null, // solo preview
        'password_input_length' => strlen($contrasena)
    ];

    // Verificamos la contraseña (si hay hash)
    $verify = false;
    if ($hash !== null) {
        $verify = password_verify($contrasena, $hash);
        $response['debug']['password_verify'] = $verify;
        $response['debug']['password_get_info'] = password_get_info($hash);
    }

    if ($verify) {
        // Login exitoso
        $_SESSION['id_usuario'] = $usuario['id_usuario'] ?? null;
        $_SESSION['nombre_completo'] = $usuario['nombre_completo'] ?? null;
        $_SESSION['correo'] = $usuario['correo'] ?? null;
        $_SESSION['rol'] = $usuario['rol'] ?? null;

        $response['success'] = true;
        $response['message'] = 'Login exitoso';
        // Añadir el nombre a la respuesta para usarlo inmediatamente en el cliente
        $response['id_usuario'] = $usuario['id_usuario'] ?? null; // <--- NUEVO
        $response['nombre_completo'] = $usuario['nombre_completo'] ?? 'Usuario';
    } else {

        $response['success'] = false;
        $response['message'] = 'Correo o contraseña incorrecta';
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Correo o contraseña incorrecta';
    // si no se encontró, indicarlo en debug
    $response['debug'] = ['found_user' => false, 'rows' => $resultado ? $resultado->num_rows : 0];
}

$output = ob_get_clean();
if (!empty($output)) {
    // Incluimos cualquier salida inesperada para identificarlo
    $response['debug_output'] = $output;
}

echo json_encode($response);

$stmt->close();
$conn->close();
exit;
?>