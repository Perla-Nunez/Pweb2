<?php

namespace Core\Middleware;

class Authenticated
{
    public function handle()
    {
       

        // Verifica si hay sesión iniciada y si existe el ID del usuario
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            // Si no está autenticado, redirige al inicio de sesión
            header('Location: /');
            exit();
        }

        // (Opcional) Validación extra: podrías verificar que el ID sea un número válido
        if (!is_numeric($_SESSION['user']['id'])) {
            // Si algo no cuadra, mejor destruir la sesión y redirigir
            session_destroy();
            header('Location: /');
            exit();
        }

        // Si todo está bien, sigue la ejecución normalmente
    }
}
