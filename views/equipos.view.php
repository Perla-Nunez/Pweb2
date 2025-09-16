<?php
// equipos.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>MundialFan - Equipos</title>

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
        <li><a href="/"><i class="fas fa-home"></i> Inicio</a></li>
        <li><a href="#"><i class="fas fa-trophy"></i> Campeonatos</a></li>
        <li><a href="equipos.php" class="active"><i class="fas fa-users"></i> Equipos</a></li>
        <li><a href="#"><i class="fas fa-calendar-alt"></i> Partidos</a></li>
        <li><a href="#"><i class="fas fa-store"></i> Tienda</a></li>
        <li><a href="#"><i class="fas fa-envelope"></i> Contacto</a></li>
      </ul>
      <button class="toggle-btn" id="toggle-mode" aria-label="Cambiar modo">
        <i class="fas fa-moon"></i>
      </button>
    </div>
  </nav>

  <!-- HERO -->
  <section class="hero" style="background-image: url('https://i.ibb.co/zNnMgfC/equipos.jpg');">
    <div class="hero-contenido">
      <h2>Equipos del Mundial</h2>
      <p>Descubre a las selecciones que compiten por la gloria.</p>
    </div>
  </section>

  <!-- SECCION EQUIPOS -->
  <section class="caracteristicas">
    <div class="contenedor">
      <h2 class="titulo-seccion">Selecciones Nacionales</h2>
      <div class="caracteristicas-grid">
        <?php
        // 🔹 Puedes cambiar esto por datos de BD
        $equipos = [
          ["pais" => "Argentina", "bandera" => "https://flagcdn.com/w320/ar.png"],
          ["pais" => "Brasil", "bandera" => "https://flagcdn.com/w320/br.png"],
          ["pais" => "España", "bandera" => "https://flagcdn.com/w320/es.png"],
          ["pais" => "Francia", "bandera" => "https://flagcdn.com/w320/fr.png"],
          ["pais" => "Alemania", "bandera" => "https://flagcdn.com/w320/de.png"],
          ["pais" => "México", "bandera" => "https://flagcdn.com/w320/mx.png"]
        ];

        foreach ($equipos as $eq) {
          echo "
          <div class='caracteristica'>
            <img src='{$eq['bandera']}' alt='Bandera de {$eq['pais']}' style='width:80px; border-radius:6px; margin-bottom:10px;'>
            <h3>{$eq['pais']}</h3>
            <p>Información, jugadores y estadísticas de la selección de {$eq['pais']}.</p>
            <a href='#' class='boton'>Ver Detalles</a>
          </div>
          ";
        }
        ?>
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
            <li><a href="index.php">Inicio</a></li>
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
