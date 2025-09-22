// ===============================
// Datos de Técnicas
// ===============================
const tecnicas = [
    {
        nombre: "BASE DE DATOS",
        img: "../Static/img/base_datos.png",
        resumen: "Aprende a organizar, administrar y proteger la información con bases de datos relacionales.",
        descripcion: `
         <p> 
             La media técnica en <strong>Bases de Datos</strong> enseña a los estudiantes a <strong>organizar</strong>, 
               <strong>administrar</strong> y <strong>proteger</strong> la información de manera eficiente. 
             <br><br> 
             Durante la formación se aprenden conceptos como <strong>modelado de datos</strong>, 
                <strong>diagramas entidad-relación</strong> y <strong>normalización</strong>, aplicados en sistemas gestores como 
               <strong>MySQL</strong>, <strong>SQL Server</strong> y <strong>Oracle</strong>. 
               <br><br> 
               El objetivo es que el estudiante desarrolle <strong>competencias técnicas</strong> para  <strong>crear</strong>, 
               <strong>consultar</strong> y <strong>mantener bases de datos</strong> en diferentes entornos profesionales. 
         </p>
            <p>
                algunos de los proyectos realizados por los estudiantes son:
            </p>
            <div style="margin-top:15px; text-align:center;">
                <a href="../Templates/juego_ahorcado/juego_ahorcado.html" target="_blank" 
                class="boton-juego">
                🎮 Juego del Ahorcado
                </a>
            </div>
            <div style="margin-top:15px; text-align:center;">
                <a href="../Templates/juego_Carrera/juego_carrera.html" target="_blank" 
                class="boton-juego">
                🚗 Juego de Carrera
                </a>
            </div>
        `,

        
    },
    {
        nombre: "IOT",
        img: "../Static/img/iot.png",
        resumen: "Diseña y conecta dispositivos inteligentes con IoT y microcontroladores.",
        descripcion: `<p> 
            La media técnica en <strong>Internet de las Cosas (IoT)</strong> busca formar a los estudiantes en el 
            <strong>diseño</strong> y <strong>manejo de sistemas</strong> capaces de conectar dispositivos físicos a internet para 
            <strong>recopilar</strong>, <strong>procesar</strong> y <strong>compartir información</strong>. 
            <br><br> 
            En el programa se estudian fundamentos de <strong>electrónica básica</strong>, <strong>sensores</strong>, 
            <strong>redes de comunicación</strong> y <strong>programación</strong> de microcontroladores como 
            <strong>Arduino</strong> o <strong>ESP32</strong>. 
            <br><br> 
            El propósito principal es que los estudiantes comprendan cómo integrar <strong>hardware</strong> y 
            <strong>software</strong> para crear soluciones prácticas como <strong>sistemas de domótica</strong>, 
            <strong>monitoreo ambiental</strong>, <strong>control de dispositivos a distancia</strong> o aplicaciones en la industria. 
            <br><br> 
            Esta formación abre oportunidades en <strong>automatización</strong>, 
            <strong>soporte técnico en sistemas inteligentes</strong> o <strong>emprendimientos tecnológicos</strong>, 
            y sirve como base para continuar estudios en <strong>ingeniería electrónica</strong>, 
            <strong>telecomunicaciones</strong> o <strong>ciencia de datos</strong>. 
        </p>`,
    },
    {
        nombre: "DISEÑO GRAFICO",
        img: "../Static/img/diseño_grafico.png",
        resumen: "Desarrolla tu creatividad y comunica visualmente con diseño gráfico digital.",
        descripcion: `<p> 
        La media técnica en <strong>Diseño Gráfico</strong> está orientada a desarrollar la <strong>creatividad</strong> y el 
        <strong>pensamiento visual</strong> de los estudiantes. 
        <br><br> 
        Se adquieren conocimientos sobre <strong>composición</strong>, <strong>color</strong>, 
        <strong>tipografía</strong> e <strong>ilustración</strong>, además del uso de herramientas digitales como 
        <strong>Photoshop</strong> e <strong>Illustrator</strong>. 
        <br><br> 
        Su objetivo es brindar bases sólidas para comunicar mensajes visuales de manera atractiva, integrando lo 
        <strong>artístico</strong> con lo <strong>tecnológico</strong>. 
        <br><br> 
        Esta formación permite desempeñarse en proyectos de <strong>diseño publicitario</strong>, <strong>branding</strong>, 
        <strong>ilustración digital</strong> y <strong>contenidos para medios</strong> impresos y digitales. 
        <br><br> 
        También sirve como primer acercamiento al ámbito profesional, facilitando la <strong>inserción laboral</strong> temprana 
        o la continuidad de estudios superiores en <strong>diseño</strong>, <strong>comunicación</strong> o <strong>artes visuales</strong>. 
    </p>`,
        carrusel: [
            { img: "../Static/img/20250814_131403.jpg" },
            { img: "../Static/img/digital_carrusel.png", link: "https://drive.google.com/drive/folders/1iMpvD5J44OfhYjQ0akpUXkmMdUpEanqD" },
            { img: "../Static/img/20250814_132815.jpg", link: "../Templates/revista.html"}
        ]
    },
];

// ===============================
// Renderiza la vista principal
// ===============================
function renderMain() {
    let html = `<div class="media-grid">`;
    tecnicas.forEach((t, i) => {
        html += `
<div class="media-card">
    <img src="${t.img}" alt="${t.nombre}">
    <h3>${t.nombre}</h3>
    <div class="resumen" id="resumen-${i}">
        <p>${t.resumen}</p>
        <button onclick="showDetalle(${i})">Ver más</button>
    </div>
</div>`;
    });
    html += `</div>`;
    document.getElementById('media-container').innerHTML = html;
}

// ===============================
// Renderiza la vista detalle con banner
// ===============================
function showDetalle(idx) {
    const t = tecnicas[idx];
    const otras = tecnicas
        .filter((o, i) => i !== idx)
        .map((o, i) => `
            <div class="media-card">
                <img src="${o.img}" alt="${o.nombre}">
                <h3>${o.nombre}</h3>
                <div class="resumen">
                    <p>${o.resumen}</p>
                    <button onclick="showDetalle(${tecnicas.indexOf(o)})">Ver más</button>
                </div>
            </div>
        `).join('');

    // Banner grande con carrusel simple si existe
    let bannerHTML = '';
    if (t.carrusel && t.carrusel.length > 0) {
        bannerHTML = `
        <div id="banner" class="banner">
            <div class="slides">
                ${t.carrusel.map(item => {
    if (item.tipo === "revista") {
        return `
            <div class="slide revista">
                <img src="${item.img}" alt="Revista Digital">
                <div class="submenu-container">
                    <button class="submenu-btn">📖 Ver ediciones</button>
                    <div class="submenu">
                        ${item.ediciones.map(ed => `
                            <a href="${ed.link}" target="_blank">${ed.nombre}</a>
                        `).join('')}
                    </div>
                </div>
            </div>
        `;
    } else {
        const data = typeof item === "string" ? { img: item } : item;
        return `
            <div class="slide">
                <img src="${data.img}" alt="Proyecto de Diseño">
                ${data.link ? `<a href="${data.link}" target="_blank" class="btn-ir">Ir</a>` : ""}
            </div>
        `;
    }
}).join('')}

            </div>
            <button class="prev-btn">❮</button>
            <button class="next-btn">❯</button>
        </div>
        `;
    }

    let contenidoHTML = `
    <div class="media-detalle">
        ${bannerHTML}
        <img src="${t.img}" alt="${t.nombre}" class="logo-tecnica">
        <h2>${t.nombre}</h2>
        <div class="descripcion">${t.descripcion}</div>
        <div class="media-opciones">${otras}</div>
        <button class="volver-btn" onclick="renderMain()">Volver</button>
    </div>
    `;

    document.getElementById('media-container').innerHTML = contenidoHTML;

    // Inicializar banner si existe
    if (t.carrusel && t.carrusel.length > 0) {
        initBanner();
    }
}

// ===============================
// Banner simple funcional
// ===============================
function initBanner() {
    const banner = document.getElementById('banner');
    if (!banner) return;

    const slidesContainer = banner.querySelector('.slides');
    const slides = banner.querySelectorAll('.slide');
    const prevBtn = banner.querySelector('.prev-btn');
    const nextBtn = banner.querySelector('.next-btn');

    let currentIndex = 0;
    const totalSlides = slides.length;

    function updateSlide() {
        const width = banner.clientWidth;
        slidesContainer.style.transform = `translateX(${-width * currentIndex}px)`;
    }

    prevBtn.addEventListener('click', () => {
        currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
        updateSlide();
    });

    nextBtn.addEventListener('click', () => {
        currentIndex = (currentIndex + 1) % totalSlides;
        updateSlide();
    });

    // Auto slide cada 5 segundos
    let autoSlide = setInterval(() => {
        currentIndex = (currentIndex + 1) % totalSlides;
        updateSlide();
    }, 5000);

    // Pausar auto slide al hover
    banner.addEventListener('mouseenter', () => clearInterval(autoSlide));
    banner.addEventListener('mouseleave', () => {
        autoSlide = setInterval(() => {
            currentIndex = (currentIndex + 1) % totalSlides;
            updateSlide();
        }, 5000);
    });

    // Ajustar slide al redimensionar ventana
    window.addEventListener('resize', updateSlide);

    // Inicializar posición
    updateSlide();
}

// ===============================
// Toggle Submenú Revista
// ===============================
document.addEventListener("click", e => {
    if (e.target.classList.contains("submenu-btn")) {
        const submenu = e.target.nextElementSibling;
        submenu.classList.toggle("show");
    }
});

// ===============================
// Inicializa
// ===============================
document.addEventListener('DOMContentLoaded', renderMain);
window.showDetalle = showDetalle;
window.renderMain = renderMain;
