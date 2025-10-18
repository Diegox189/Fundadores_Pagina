let nextDom = document.getElementById('next');
let prevDom = document.getElementById('prev');

let carouselDom = document.querySelector('.carousel');
let SliderDom = carouselDom.querySelector('.carousel .list');
let thumbnailBorderDom = document.querySelector('.carousel .thumbnail');
let thumbnailItemsDom = thumbnailBorderDom.querySelectorAll('.item');
let timeDom = document.querySelector('.carousel .time');
// Inicializa la miniatura activa, Mueve el primer elemento de miniaturas al final (para crear un efecto continuo)
thumbnailBorderDom.appendChild(thumbnailItemsDom[0]);

let timeRunning = 2000; // Duración de la animación
let timeAutoNext = 6000; // Intervalo de auto-desplazamiento

let runTimeOut;
let runNextAuto = setTimeout(() => {
    nextDom.click();
}, timeAutoNext);

// Función principal
function showSlider(type) {
    let SliderItemsDom = SliderDom.querySelectorAll('.carousel .list .item');
    let thumbnailItemsDom = document.querySelectorAll('.carousel .thumbnail .item');
    
    if (type === 'next') {
        SliderDom.appendChild(SliderItemsDom[0]);  // mueve el primer slide al final
        thumbnailBorderDom.appendChild(thumbnailItemsDom[0]);  // mueve la primera miniatura al final
        carouselDom.classList.add('next');    // aplica animacion "next"
    } else {
        SliderDom.prepend(SliderItemsDom[SliderItemsDom.length - 1]); // mueve el ultimo slide al principio
        thumbnailBorderDom.prepend(thumbnailItemsDom[thumbnailItemsDom.length - 1]); // mueve la ultima miniatura al principio
        carouselDom.classList.add('prev');  // aplica animacion "prev"
    }
    
    // Limpia el timeout de animación pero NO el auto-scroll
    clearTimeout(runTimeOut);
    runTimeOut = setTimeout(() => {
        carouselDom.classList.remove('next');
        carouselDom.classList.remove('prev');
    }, timeRunning);

    // Reinicia el contador de auto-scroll al interactuar manualmente
    clearTimeout(runNextAuto);
    runNextAuto = setTimeout(() => {
        nextDom.click();
    }, timeAutoNext);
}

// Eventos de flechas 
nextDom.onclick = function() {
    showSlider('next');    
}

prevDom.onclick = function() {
    showSlider('prev');    
}

// Función de redirección (pagina principal)
function PaginaPrincipal(){
    window.location.href = "Principal_Fundadores.html";
}
// Función de redirección (media técnica)
function media_tecnica(){
    window.location.href = "media_tecnica.html";
}
