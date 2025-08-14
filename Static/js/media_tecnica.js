const tecnicas = [
    {
        nombre: "BASE DE DATOS",
        img: "../Static/img/base datos.png",
        descripcion: "La media técnica en Base de Datos te enseña a organizar, gestionar y analizar información digital de manera eficiente y segura."
    },
    {
        nombre: "IOT",
        img: "../Static/img/iot.png",
        descripcion: "La media técnica en IoT (Internet de las Cosas) te introduce al mundo de los dispositivos conectados, automatización y tecnología inteligente."
    },
    {
        nombre: "DISEÑO GRAFICO",
        img: "../Static/img/diseño grafico.png",
        descripcion: "La media técnica en Diseño Gráfico te permite desarrollar habilidades creativas para comunicar ideas visualmente usando herramientas digitales."
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
// Muestra el logo grande, la descripción y las otras dos opciones debajo
// ===============================
function showDetalle(idx) {
    const t = tecnicas[idx];
    // Genera las otras opciones (las que no están seleccionadas)
    const otras = tecnicas
        .map((o, i) => i !== idx ? `
            <div class="media-card">
                <img src="${o.img}" alt="${o.nombre}">
                <button onclick="showDetalle(${i})">Ver más</button>
            </div>
        ` : '').join('');
    document.getElementById('media-container').innerHTML = `
        <div class="media-detalle">
            <img src="${t.img}" alt="${t.nombre}">
            <h2>${t.nombre}</h2>
            <p>${t.descripcion}</p>
            <div class="media-opciones">
                ${otras}
            </div>
            <button class="volver-btn" onclick="renderMain()">Volver</button>
        </div>
    `;
}
// ===============================
// Inicializa la vista principal al cargar la página
// ===============================
renderMain();
// ===============================
// Hace accesibles las funciones al scope global para que funcionen los botones
// ===============================
window.showDetalle = showDetalle;