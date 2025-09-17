<?php
include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_completo = $_POST['nombreCom'];
    $correo = $_POST['correo'];
    $password = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    $nacionalidad = $_POST['nacionalidad'];
    //$rol = $_POST['rol'];

    $sql = "INSERT INTO usuario (NOMBRE_COMPLETO, CORREO, PASSWORD, NACIONALIDAD)
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nombre_completo, $correo, $password, $nacionalidad);

    if ($stmt->execute()) {
        echo "alert('Se ha registrado correctamente. Bienvenid@');";
        echo "<script>window.location.href='index.html';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>