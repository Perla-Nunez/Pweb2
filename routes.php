<?php

$router->get('/', 'controles/inicio.php')->only('guest');                // Muestra el formulario de inicio
$router->post('/login', 'controles/log/login.php')->only('guest');     // Login
$router->get('/logout', 'controles/log/logout.php')->only('auth');     // Logout
$router->get('/equipos', 'controles/equipos.php');
$router->get('/styles', 'css/styles.css');  


// Dashboard principal
$router->get('/home', 'controles/home.php')->only('auth');             // Vista principal, lista publicaciones

$router->get('/sign', 'controles/sign.php');                            // Vista de registro/signin

// Rutas de la API para publicaciones
$router->get('/api/posts', 'controles/Api/PostController@index');
$router->post('/api/posts/store', 'controles/Api/PostController@store');

