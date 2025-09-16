<?php

use Core\App;
use Core\Database;

if (!isset($_SESSION['user'])) {
    header("Location: /");
    exit();
}

$db = App::resolve(Database::class);

$userId = $_SESSION['user']['id'];

$user = $db->query("SELECT * FROM users WHERE idUsuario = :id", [
    'id' => $userId
])->find();

view("perfil.view.php", [
    'user' => $user
]);
