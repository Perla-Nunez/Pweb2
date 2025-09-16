<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>MundialFan - Todo sobre el Mundial de Fútbol</title>

  <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/styles.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body>
  <!-- HEADER -->
  <header>
    <div class="contenedor header-contenido">
      <div class="logo">
        <i class="fas fa-futbol"></i>
        <h1>MundialFan</h1>
      </div>
    </div>
  </header>

  <!-- NAV -->
  <nav>
    <div class="contenedor nav-wrap">
      <ul class="navbar">
        <li><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
        <li><a href="#"><i class="fas fa-trophy"></i> Campeonatos</a></li>
        <li><a href='/equipos'><i class="fas fa-users"></i> Equipos</a></li>
        <li><a href="#"><i class="fas fa-calendar-alt"></i> Partidos</a></li>
        <li><a href="#"><i class="fas fa-store"></i> Tienda</a></li>
        <li><a href="#"><i class="fas fa-envelope"></i> Contacto</a></li>
      </ul>
      <button class="toggle-btn" id="toggle-mode" aria-label="Cambiar modo">
        <i class="fas fa-moon"></i>
      </button>
    </div>
  </nav>

  <!-- HERO (imagen conservada) -->
  <section class="hero">
    <div class="hero-contenido">
      <h2>Vive la Emoción del Mundial</h2>
      <p>Todo lo que necesitas saber sobre el mayor evento de fútbol del planeta. Noticias, estadísticas, resultados y mucho más.</p>
      <a href="#" class="boton">Ver Partidos</a>
      <a href="#" class="boton">Comprar Entradas</a>
    </div>
  </section>

  <!-- CONTENIDO -->
  <section class="caracteristicas">
    <div class="contenedor">
      <h2 class="titulo-seccion">Todo sobre el Mundial</h2>
      <div class="caracteristicas-grid">
        <div class="caracteristica">
          <i class="fas fa-trophy"></i>
          <h3>Historia de Campeones</h3>
          <p>Conoce todos los equipos que han levantado la copa a lo largo de la historia del campeonato.</p>
        </div>
        <div class="caracteristica">
          <i class="fas fa-star"></i>
          <h3>Estadísticas de Jugadores</h3>
          <p>Accede a las estadísticas completas de todos los jugadores participantes en el torneo.</p>
        </div>
        <div class="caracteristica">
          <i class="fas fa-video"></i>
          <h3>Resúmenes de Partidos</h3>
          <p>Disfruta de los mejores momentos de cada encuentro con nuestros resúmenes exclusivos.</p>
        </div>
        <div class="caracteristica">
          <i class="fas fa-table"></i>
          <h3>Tablas de Posiciones</h3>
          <p>Sigue de cerca la clasificación de todos los grupos con nuestras tablas actualizadas.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="formularios">
    <div class="contenedor">
      <h2 class="titulo-seccion">Únete a la Comunidad</h2>
      <div class="formularios-contenedor">
        <div class="formulario">
          <h3><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</h3>
          <form id="login-form">
            <input type="email" placeholder="Correo electrónico" required>
            <input type="password" placeholder="Contraseña" required>
            <button type="submit">Ingresar</button>
          </form>
        </div>
        <div class="formulario">
          <h3><i class="fas fa-user-plus"></i> Registrarse</h3>
          <form id="register-form">
            <input type="text" placeholder="Nombre completo" required>
            <input type="email" placeholder="Correo electrónico" required>
            <input type="password" placeholder="Contraseña" required>
            <input type="password" placeholder="Confirmar contraseña" required>
            <button type="submit">Crear Cuenta</button>
          </form>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer>
    <div class="contenedor">
      <div class="footer-contenido">
        <div class="footer-seccion">
          <h4>MundialFan</h4>
          <p>Tu destino para todo lo relacionado con el Mundial de Fútbol. Ofrecemos las últimas noticias, estadísticas y contenido exclusivo.</p>
        </div>
        <div class="footer-seccion">
          <h4>Enlaces Rápidos</h4>
          <ul>
            <li><a href="#">Inicio</a></li>
            <li><a href="#">Calendario de Partidos</a></li>
            <li><a href="#">Resultados</a></li>
            <li><a href="#">Noticias</a></li>
          </ul>
        </div>
        <div class="footer-seccion">
          <h4>Contacto</h4>
          <p><i class="fas fa-map-marker-alt"></i> Av. del Fútbol 123, Ciudad Deportiva</p>
          <p><i class="fas fa-phone"></i> +1 234 567 8900</p>
          <p><i class="fas fa-envelope"></i> info@mundialfan.com</p>
        </div>
        <div class="footer-seccion">
          <h4>Síguenos</h4>
          <p>No te pierdas ninguna actualización sobre el mundial</p>
          <div class="redes-sociales">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-youtube"></i></a>
          </div>
        </div>
      </div>
      <div class="copyright">
        <p>&copy; 2023 MundialFan. Todos los derechos reservados.</p>
      </div>
    </div>
  </footer>

  <script>
    // === Estado persistente de tema ===
    const body = document.body;
    const toggleBtn = document.getElementById('toggle-mode');
    const icon = toggleBtn.querySelector('i');

    // aplica preferencia guardada
    const saved = localStorage.getItem('theme');
    if (saved === 'dark') {
      body.classList.add('dark-mode');
      icon.classList.replace('fa-moon','fa-sun');
    }

    toggleBtn.addEventListener('click', () => {
      body.classList.toggle('dark-mode');
      const dark = body.classList.contains('dark-mode');
      icon.classList.toggle('fa-sun', dark);
      icon.classList.toggle('fa-moon', !dark);
      localStorage.setItem('theme', dark ? 'dark' : 'light');
    });
  </script>
</body>
</html>
