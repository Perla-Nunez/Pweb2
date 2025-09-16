
document.getElementById('login-form').addEventListener('submit', function(e) {
    e.preventDefault();
    alert('¡Inicio de sesión exitoso! Redirigiendo...');
    
});

document.getElementById('register-form').addEventListener('submit', function(e) {
    e.preventDefault();
    alert('¡Registro exitoso! Bienvenido a MundialFan.');
    
});


const titulo = document.querySelector('.hero-contenido h2');
const textoOriginal = titulo.textContent;
let i = 0;


if (titulo) {
    titulo.textContent = '';
    
    function typeWriter() {
        if (i < textoOriginal.length) {
            titulo.textContent += textoOriginal.charAt(i);
            i++;
            setTimeout(typeWriter, 100);
        }
    }
    
    
    window.addEventListener('load', typeWriter);
}


document.addEventListener('DOMContentLoaded', function() {
    
    const currentLocation = location.href;
    const menuItems = document.querySelectorAll('.navbar a');
    const menuLength = menuItems.length;
    
    for (let i = 0; i < menuLength; i++) {
        if (menuItems[i].href === currentLocation) {
            menuItems[i].classList.add('active');
        }
    }
    
    
    const caracteristicas = document.querySelectorAll('.caracteristica');
    
    function checkScroll() {
        const triggerBottom = window.innerHeight * 0.9;
        
        caracteristicas.forEach(caracteristica => {
            const caracteristicaTop = caracteristica.getBoundingClientRect().top;
            
            if (caracteristicaTop < triggerBottom) {
                caracteristica.style.opacity = 1;
                caracteristica.style.transform = 'translateY(0)';
            }
        });
    }
    
    
    caracteristicas.forEach(caracteristica => {
        caracteristica.style.opacity = 0;
        caracteristica.style.transform = 'translateY(20px)';
        caracteristica.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    });
    
    
    window.addEventListener('scroll', checkScroll);
    window.addEventListener('load', checkScroll);
});