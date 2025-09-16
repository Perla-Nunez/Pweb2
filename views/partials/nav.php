<!-- Navbar.php -->
<nav class="bg-blue-950 text-white fixed w-full top-0 left-0 shadow-md z-50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="/home" class="flex items-center space-x-2 hover:text-gray-300">
                    <img src="recurso/imagen/logo.png" alt="Logo" class="w-10 h-10">
                    <span class="text-xl font-bold">Oasis</span>
                </a>
            </div>

            <!-- Enlaces del menú (versión escritorio) -->
            <div class="hidden md:flex space-x-6">
                <a href="/home" class="hover:text-gray-300 text-lg">Home</a>
                <a href="/perfil" class="hover:text-gray-300 text-lg">Perfil</a>
                <a href="/videos" class="hover:text-gray-300 text-lg">Videos</a>
                <a href="/amigos" class="hover:text-gray-300 text-lg">Amigos</a>
            </div>

            <!-- Acciones del usuario -->
            <div class="hidden md:flex items-center space-x-4">
                <!-- Notificaciones -->
                <button type="button" class="relative p-1 text-gray-300 hover:text-white focus:outline-none">
                    <span class="sr-only">Ver notificaciones</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022
                            c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                </button>

                <!-- Perfil -->
                <div class="relative">
                    <?php if ($_SESSION['user'] ?? false) : ?>
                        <button type="button" class="flex items-center space-x-2 focus:outline-none">
                            <img class="h-8 w-8 rounded-full" src="https://i.pravatar.cc/300" alt="Profile">
                        </button>
                    <?php else : ?>
                        <a href="/register" class="text-white hover:text-gray-300">Registro</a>
                    <?php endif; ?>
                </div>

              
                <a href="/logout" class="bg-blue-900 hover:bg-red-700 px-4 py-1 rounded">Salir</a>
            </div>

            <!-- Botón del menú móvil -->
            <div class="md:hidden">
                <button id="mobile-menu-button" type="button" class="text-gray-300 hover:text-white focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Menú móvil -->
    <div id="mobile-menu" class="md:hidden hidden px-4 pt-2 pb-4 space-y-2 bg-blue-950 text-white">
        <a href="/home" class="block px-3 py-2 rounded hover:bg-blue-900">Home</a>
        <a href="/perfil" class="block px-3 py-2 rounded hover:bg-blue-900">Perfil</a>
        <a href="/videos" class="block px-3 py-2 rounded hover:bg-blue-900">Videos</a>
        <a href="/amigos" class="block px-3 py-2 rounded hover:bg-blue-900">Amigos</a>
        <a href="/" class="block px-3 py-2 rounded hover:bg-red-700">Salir</a>
    </div>

    <script>
        // Script para mostrar/ocultar el menú móvil
        document.getElementById('mobile-menu-button').addEventListener('click', function () {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>
</nav>
