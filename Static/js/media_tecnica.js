const tecnicas = [
    {
        nombre: "BASE DE DATOS",
        img: "../Static/img/base datos.png",
        descripcion: "La media técnica en Bases de Datos está enfocada en enseñar a los estudiantes cómo organizar, administrar y proteger la información de manera eficiente. Durante la formación se aprenden conceptos fundamentales como modelado de datos, diagramas entidad-relación y normalización, junto con el uso práctico de sistemas gestores como MySQL, SQL Server u Oracle. <br> <br> El objetivo es que el estudiante desarrolle competencias técnicas para crear, consultar y mantener bases de datos, aplicando buenas prácticas de seguridad y optimización. Además, se trabajan habilidades para resolver problemas reales de almacenamiento y manejo de información, que son esenciales en empresas e instituciones. <br> <br>Esta formación brinda una base sólida para desempeñarse como auxiliar en áreas de sistemas, soporte técnico o análisis de información, y también prepara para continuar estudios en carreras de ingeniería, ciencia de datos o desarrollo de software.",
         carrusel: [
            "../Static/img/20250814_131403.jpg",
            "../Static/img/20250814_132701.jpg",
            "../Static/img/20250814_132815.jpg",
        ]
    },
    {
        nombre: "IOT",
        img: "../Static/img/iot.png",
        descripcion: "La media técnica en Internet de las Cosas (IoT) busca formar a los estudiantes en el diseño y manejo de sistemas capaces de conectar dispositivos físicos a internet para recopilar, procesar y compartir información. En el programa se estudian fundamentos de electrónica básica, sensores, redes de comunicación y programación de microcontroladores como Arduino o ESP32.<br> <br>El propósito principal es que los estudiantes comprendan cómo integrar hardware y software para crear soluciones prácticas, como sistemas de domótica, monitoreo ambiental, control de dispositivos a distancia o aplicaciones en la industria. Además, se promueve el desarrollo de la creatividad y la innovación tecnológica, aplicadas a la vida cotidiana y al sector productivo.<br> <br>Esta formación abre oportunidades para trabajar en proyectos de automatización, soporte técnico en sistemas inteligentes o emprendimientos tecnológicos, y también sirve como base para continuar estudios en ingeniería electrónica, telecomunicaciones o ciencia de datos.",
         carrusel: [
            "../Static/img/IOT.JPG",
            "../Static/img/20250814_132701.jpg",
            "../Static/img/20250814_132815.jpg",
        ]
    },
    {
        nombre: "DISEÑO GRAFICO",
        img: "../Static/img/diseño grafico.png",
        descripcion: "La media técnica en Diseño Gráfico es un proceso de formación orientado a desarrollar la creatividad y el pensamiento visual de los estudiantes. <br> <br> En este programa se adquieren conocimientos sobre composición, color, tipografía e ilustración, al mismo tiempo que se aprende el uso de herramientas digitales como Photoshop, Illustrator y otros programas especializados. Su objetivo principal es brindar bases sólidas para comunicar mensajes de manera visual y atractiva, integrando lo artístico con lo tecnológico. Además, ofrece una preparación práctica que permite desempeñarse en proyectos de diseño publicitario, branding, ilustración digital y contenidos para medios impresos y digitales. <br> <br>    Este tipo de formación también sirve como un primer acercamiento al campo profesional, facilitando la inserción laboral temprana o la continuidad de estudios superiores en diseño, comunicación o artes visuales.",
        carrusel: [
            "../Static/img/20250814_131403.jpg",
            "../Static/img/20250814_132701.jpg",
            "../Static/img/20250814_132815.jpg",
        ]
    }
];
// ===============================
// Renderiza la vista principal con los 3 logos y sus botones
// ===============================
function renderMain() {
    let html = `<div class="media-grid">`;
    tecnicas.forEach((t, i) => {
        html += `
        <div class="media-card">
            <img src="${t.img}" alt="${t.nombre}">
            <button onclick="showDetalle(${i})">Ver más</button>
        </div>`;
    });
    html += `</div>`;
    document.getElementById('media-container').innerHTML = html;
}
// ===============================
// Renderiza la vista detalle de una técnica seleccionada
// ===============================
function showDetalle(idx) {
    const t = tecnicas[idx];
    const otras = tecnicas
        .filter((o, i) => i !== idx)
        .map((o, i) => `
            <div class="media-card">
                <img src="${o.img}" alt="${o.nombre}">
                <button onclick="showDetalle(${tecnicas.indexOf(o)})">Ver más</button>
            </div>
        `).join('');
    
    // Generar el carrusel solo si la técnica es "DISEÑO GRAFICO"
    let carruselHTML = '';
    if (t.nombre === "DISEÑO GRAFICO" && t.carrusel && t.carrusel.length > 0) {
        carruselHTML = `
        <div class="carrusel-fundadores">
            <h3>Galería de Trabajos</h3>
            <div class="contenido-carrusel">
                ${t.carrusel.map(img => `
                    <div class="slide">
                        <img src="${img}" alt="Trabajo de diseño gráfico">
                    </div>
                `).join('')}
            </div>
            <div class="controles">
                <button class="prev-btn">❮</button>
                <button class="next-btn">❯</button>
            </div>
        </div>
        `;
    }
    
    document.getElementById('media-container').innerHTML = `
        <div class="media-detalle">
            <img src="${t.img}" alt="${t.nombre}" class="logo-tecnica">
            <h2>${t.nombre}</h2>
            <p>${t.descripcion}</p>
            ${carruselHTML}
            <div class="media-opciones">
                ${otras}
            </div>
            <button class="volver-btn" onclick="renderMain()">Volver</button>
        </div>
    `;
    
    // Inicializar el carrusel si existe
    if (t.nombre === "DISEÑO GRAFICO" && t.carrusel && t.carrusel.length > 0) {
        initCarrusel();
    }
}

// ===============================
// Inicializa el carrusel
// ===============================
function initCarrusel() {
    const slides = document.querySelectorAll('.carrusel-fundadores .slide');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    let currentIndex = 0;
    
    // Mostrar el primer slide
    slides[0].classList.add('activo');
    
    function cambiarSlide(nuevoIndex) {
        slides[currentIndex].classList.remove('activo');
        currentIndex = (nuevoIndex + slides.length) % slides.length;
        slides[currentIndex].classList.add('activo');
    }
    
    // Eventos para los botones
    prevBtn.addEventListener('click', () => cambiarSlide(currentIndex - 1));
    nextBtn.addEventListener('click', () => cambiarSlide(currentIndex + 1));
        
    // Pausar al hacer hover
    const carrusel = document.querySelector('.carrusel-fundadores');
    carrusel.addEventListener('mouseenter', () => clearInterval(interval));
    carrusel.addEventListener('mouseleave', () => {
        interval = setInterval(() => cambiarSlide(currentIndex + 1), 5000);
    });
}

// ===============================
// Inicializa la vista principal al cargar la página
// ===============================
document.addEventListener('DOMContentLoaded', renderMain);

// ===============================
// Hace accesibles las funciones al scope global
// ===============================
window.showDetalle = showDetalle;
window.renderMain = renderMain;

