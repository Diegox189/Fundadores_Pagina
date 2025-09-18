document.addEventListener('DOMContentLoaded', function () {
    // ==============================
    //  CARRUSEL
    // ==============================
    const slides = document.querySelectorAll('.carousel-slide');
    const prevBtn = document.getElementById('carousel-prev');
    const nextBtn = document.getElementById('carousel-next');
    let slideIndex = 0;
    let autoInterval;

    function showCarouselSlide(n) {
        slides.forEach((slide, i) => {
            slide.classList.toggle('active', i === n);
            if (i === n) {
                slide.classList.add('carousel-anim');
                setTimeout(() => {
                    slide.classList.remove('carousel-anim');
                }, 500);
            }
        });
    }

    function nextSlide() {
        slideIndex = (slideIndex + 1) % slides.length;
        showCarouselSlide(slideIndex);
    }

    function prevSlide() {
        slideIndex = (slideIndex - 1 + slides.length) % slides.length;
        showCarouselSlide(slideIndex);
    }

    if (prevBtn && nextBtn) {
        prevBtn.onclick = function () {
            prevSlide();
            resetAuto();
        };
        nextBtn.onclick = function () {
            nextSlide();
            resetAuto();
        };
    }

    function startAuto() {
        autoInterval = setInterval(nextSlide, 5000);
    }

    function resetAuto() {
        clearInterval(autoInterval);
        startAuto();
    }

    showCarouselSlide(slideIndex);
    startAuto();


    // ==============================
    //  FECHA ACTUAL
    // ==============================
    function actualizarFecha() {
        const meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
        const dias = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sabado"];
        const ahora = new Date();
        const texto = `${dias[ahora.getDay()]}, ${ahora.getDate()} de ${meses[ahora.getMonth()]} de ${ahora.getFullYear()}`;
        document.getElementById('fecha-actual').textContent = texto;
    }
    actualizarFecha();
    setInterval(actualizarFecha, 60000); // cada minuto


    // ==============================
    //  DROPDOWNS
    // ==============================
    document.querySelectorAll('.dropdown').forEach(dropdown => {
        dropdown.addEventListener('mouseenter', () => {
            dropdown.querySelector('.submenu').style.display = 'block';
        });
        dropdown.addEventListener('mouseleave', () => {
            dropdown.querySelector('.submenu').style.display = 'none';
        });
    });


    // ==============================
    //  CONTENIDO DINÁMICO
    // ==============================
    function mostrarContenido(titulo, texto) {
        document.getElementById("contenido-principal").style.display = "none";

        const contenedor = document.getElementById("contenido-dinamico");
        contenedor.style.display = "block";
        contenedor.innerHTML = `
            <h2 style="margin-bottom:20px;">${titulo}</h2>
            <p style="max-width:800px; margin:0 auto; font-size:1.1rem; line-height:1.6;">
                ${texto}
            </p>
            <br>
            <button onclick="regresar()" style="padding:10px 20px; cursor:pointer;">Regresar</button>
        `;
    }

    window.regresar = function () {
        document.getElementById("contenido-principal").style.display = "flex";
        document.getElementById("contenido-dinamico").style.display = "none";
    };

    document.querySelectorAll(".btn-dinamico").forEach(boton => {
        boton.addEventListener("click", (e) => {
            e.preventDefault();
            const titulo = boton.dataset.titulo;
            const texto = boton.dataset.texto;
            mostrarContenido(titulo, texto);
        });
    });
});

