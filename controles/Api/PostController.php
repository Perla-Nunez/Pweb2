<?php

namespace controles\Api;

use Core\App;

class PostController
{
    public function index()
    {
        $db = App::resolve('Core\Database');
        $userId = $_GET['user'] ?? null;

        if ($userId) {
            $posts = $db->query("SELECT * FROM publicaciones WHERE idUsuario = :id ORDER BY postdate DESC", [
                'id' => $userId
            ])->get();
        } else {
            $posts = $db->query("SELECT p.*, u.nombre AS username, u.fotoPerfil FROM publicaciones p
            JOIN users u ON p.idUsuario = u.idUsuario ORDER BY p.postdate DESC")->get();

        }

        echo json_encode($posts);
    }

    public function store()
    {
        $db = App::resolve('Core\Database');
        $contenido = $_POST['contenido'] ?? '';
        $tipo = $_POST['tipo'] ?? null;
        $idUsuario = $_SESSION['idUsuario'] ?? null;
        $fecha = date('Y-m-d');
        $archivoRuta = null;

        // Validar contenido (único campo obligatorio)
        if (empty($contenido)) {
            http_response_code(400);
            echo json_encode(['error' => 'Contenido es obligatorio']);
            return;
        }

        

        // Si se sube un archivo, validar tipo y archivo
        if ($tipo === 'imagen' && isset($_FILES['imagen'])) {
            $archivo = $_FILES['imagen'];
        
            if ($archivo['size'] > 5 * 1024 * 1024) {
                http_response_code(400);
                echo json_encode(['error' => 'El archivo es demasiado grande']);
                return;
            }
        
            if (!in_array(mime_content_type($archivo['tmp_name']), ['image/jpeg', 'image/png'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Tipo de archivo no permitido']);
                return;
            }
        
            if ($archivo['error'] === 0) {
                $nombre = time() . '_' . basename($archivo['name']);
                $uploadDir = __DIR__ . '/../../public/uploads/'; // Ruta absoluta al directorio
        
                // Verifica que exista el directorio o lo crea
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
        
                $rutaCompleta = $uploadDir . $nombre;
        
                if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
                    $archivoRuta = 'public/uploads/' . $nombre; // Ruta relativa para guardar en DB
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Error al guardar el archivo']);
                    return;
                }
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error en la carga del archivo']);
                return;
            }
        }
         elseif ($tipo === 'video' && isset($_FILES['video'])) {
            $archivo = $_FILES['video'];

            if ($archivo['size'] > 50 * 1024 * 1024) {
                http_response_code(400);
                echo json_encode(['error' => 'El archivo es demasiado grande']);
                return;
            }

            if (!in_array(mime_content_type($archivo['tmp_name']), ['video/mp4', 'video/avi'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Tipo de archivo no permitido']);
                return;
            }

            if ($archivo['error'] === 0) {
                $nombre = time() . '_' . $archivo['name'];
                if (move_uploaded_file($archivo['tmp_name'], 'public/uploads/' . $nombre)) {
                    $archivoRuta = 'public/uploads/' . $nombre;
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Error al guardar el archivo']);
                    return;
                }
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error en la carga del archivo']);
                return;
            }
        }

        // Insertar publicación
        $db->query("INSERT INTO publicaciones (idUsuario, texto, tipoContenido, rutamulti, postdate) 
                    VALUES (:id, :texto, :tipo, :archivo, :fecha)", [
            'id' => $idUsuario,
            'texto' => htmlspecialchars($contenido, ENT_QUOTES, 'UTF-8'),
            'tipo' => $tipo,
            'archivo' => $archivoRuta,
            'fecha' => $fecha
        ]);

        echo json_encode(['success' => true]);
    }
}
