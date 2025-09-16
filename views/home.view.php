<?php require 'partials/head.php'; ?>
<?php require 'partials/header.php'; ?> 
<?php require 'partials/nav.php'; ?>
<?php require 'partials/chat.php'; ?>

<!-- Contenedor principal con margen suficiente para evitar que el navbar cubra el contenido -->
<div class="flex justify-center items-start h-full pt-24"> <!-- pt-24 da más espacio debajo del navbar -->

    <div class="max-w-4xl w-full">
        
        <!-- Formulario de Publicación -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Crear publicación</h2>
            <?php require base_path('views/crear.view.php'); ?>
        </div>

        <!-- Feed de Publicaciones -->
        <div class="bg-white p-8 rounded-lg shadow-xl">
        <?php require base_path('views/post.view.php'); ?>
           
        </div>

    </div>
</div>

