/**
 * Carruseles IslaBeya — Script genérico consolidado
 * =================================================
 * Unifica los 8 carruseles de la portada en un único módulo.
 *
 * Cada carrusel se inicializa con initCarousel(config), donde config
 * define sus selectores exclusivos. Cada instancia mantiene sus propias
 * variables en una clausura (closure), por lo que todos pueden convivir
 * en la misma página funcionando de forma totalmente independiente.
 *
 * Configuración soportada:
 *   selector      {string}  Selector del contenedor deslizante (ej. ".carrusel-productos-motos")
 *   slide         {string}  Selector de los slides/grupos dentro del contenedor (ej. ".grupo-motos")
 *   dots          {string}  Selector del contenedor de indicadores (ej. ".dots-container-moto")
 *   dotClass      {string}  Clase CSS para cada indicador (ej. "dotmoto")
 *   btnLeft       {string}  Selector del botón izquierdo
 *   btnRight      {string}  Selector del botón derecho
 *   width         {number}  Ancho de desplazamiento en px (o % para el hero)
 *   unit          {string}  Unidad del desplazamiento: "px" (default) o "%"
 *   autoplay      {boolean} Habilita autoplay cada 3000ms (solo hero)
 *   swipe         {boolean} Habilita arrastre touch/mouse (solo hero)
 */

(function() {
    'use strict';

    function initCarousel(config) {
        var slider = document.querySelector(config.selector);
        if (!slider) return;

        var slides = document.querySelectorAll(config.selector + ' ' + config.slide);
        if (!slides.length) return;

        var btnLeft = document.querySelector(config.btnLeft);
        var btnRight = document.querySelector(config.btnRight);
        var dotsContainer = document.querySelector(config.dots);

        // Estado por instancia (clausura)
        var operacion = 0;          // Desplazamiento actual
        var counter = 0;            // Índice del slide visible
        var isAnimating = false;    // Evita múltiples animaciones simultáneas
        var autoInterval = null;    // Intervalo del autoplay

var unit = config.unit || 'px';
        // Para unidades en % se calcula el ancho por slide dinámicamente.
        // config.width === 'auto' usa 100 / nSlides (hero); si se pasa número, se usa ese valor.
        var widthUnit = (unit === '%' && config.width === 'auto') ? (100 / slides.length) : config.width;

        // ===========================
        //  Indicadores (dots)
        // ===========================
        function createDots() {
            if (!dotsContainer) return;
            dotsContainer.innerHTML = '';
            slides.forEach(function(_, index) {
                var dot = document.createElement('div');
                dot.classList.add(config.dotClass);
                if (index === counter) dot.classList.add('active');
                dot.addEventListener('click', function() { goToSlide(index); });
                dotsContainer.appendChild(dot);
            });
        }

        function updateDots() {
            if (!dotsContainer) return;
            var dots = dotsContainer.querySelectorAll('.' + config.dotClass);
            dots.forEach(function(dot, index) {
                if (index === counter) dot.classList.add('active');
                else dot.classList.remove('active');
            });
        }

        // ===========================
        //  Movimiento del carrusel
        // ===========================
        function performSlide(newCounter) {
            if (isAnimating) return false;
            if (!slides.length) return false;

            // Índice circular
            if (newCounter < 0) newCounter = slides.length - 1;
            if (newCounter >= slides.length) newCounter = 0;

            if (newCounter === counter) return false;

counter = newCounter;
            operacion = widthUnit * counter;
            slider.style.transform = 'translate(-' + operacion + unit + ')';
            slider.style.transition = 'all ease .6s';

            isAnimating = true;
            slider.addEventListener('transitionend', onTransitionEnd, { once: true });

            updateDots();
            if (config.autoplay) resetAutoSlide();
            return true;
        }

        function onTransitionEnd() {
            isAnimating = false;
            slider.style.transition = '';
        }

        function goToSlide(index) {
            performSlide(index);
        }

        function moveToRight() {
            var newCounter = counter + 1;
            if (newCounter >= slides.length) newCounter = 0;
            performSlide(newCounter);
        }

        function moveToLeft() {
            var newCounter = counter - 1;
            if (newCounter < 0) newCounter = slides.length - 1;
            performSlide(newCounter);
        }

        // ===========================
        //  Autoplay (solo hero)
        // ===========================
        function startAutoSlide() {
            stopAutoSlide();
            autoInterval = setInterval(function() {
                if (!isAnimating) moveToRight();
            }, 3000);
        }

        function stopAutoSlide() {
            if (autoInterval) {
                clearInterval(autoInterval);
                autoInterval = null;
            }
        }

        function resetAutoSlide() {
            startAutoSlide();
        }

        // ===========================
        //  Swipe / arrastre (solo hero)
        // ===========================
        var dragStartX = 0;
        var dragEndX = 0;
        var isDragging = false;

        function handleDragStart(e) {
            var clientX = e.type === 'mousedown' ? e.clientX : e.touches[0].clientX;
            dragStartX = clientX;
            isDragging = true;
            stopAutoSlide();
        }

        function handleDragMove(e) {
            if (!isDragging) return;
            if (e.type === 'touchmove') e.preventDefault();
        }

        function handleDragEnd(e) {
            if (!isDragging) return;
            isDragging = false;

            var clientX = e.type === 'mouseup' ? e.clientX : e.changedTouches[0].clientX;
            dragEndX = clientX;

            var deltaX = dragEndX - dragStartX;
            if (Math.abs(deltaX) > 50) {
                if (deltaX > 0) moveToLeft();
                else moveToRight();
            }

            setTimeout(function() {
                if (!isAnimating) startAutoSlide();
            }, 300);
        }

        // ===========================
        //  Eventos
        // ===========================
        if (btnLeft) btnLeft.addEventListener('click', moveToLeft);
        if (btnRight) btnRight.addEventListener('click', moveToRight);

        if (config.swipe) {
            slider.addEventListener('mousedown', handleDragStart);
            window.addEventListener('mousemove', handleDragMove);
            window.addEventListener('mouseup', handleDragEnd);
            slider.addEventListener('touchstart', handleDragStart);
            slider.addEventListener('touchmove', handleDragMove);
            slider.addEventListener('touchend', handleDragEnd);

            // Pausar autoplay al hacer hover
            slider.addEventListener('mouseenter', stopAutoSlide);
            slider.addEventListener('mouseleave', function() {
                if (!isDragging) startAutoSlide();
            });
        }

        // ===========================
        //  Inicialización
        // ===========================
        createDots();
        if (config.autoplay) startAutoSlide();
    }

    // Exponer la fábrica para su uso (mantener compatibilidad con otros scripts)
    window.initCarousel = initCarousel;

    // Inicializar todos los carruseles cuando el DOM esté listo
    function boot() {
        initCarousel({
            selector: '#slider',
            slide: '.slider',
            dots: '.dots-container',
            dotClass: 'dot',
            btnLeft: '.btn-left',
            btnRight: '.btn-right',
            width: 'auto',
            unit: '%',
            autoplay: true,
            swipe: true
        });

        initCarousel({
            selector: '.carrusel-productos-motos',
            slide: '.grupo-motos',
            dots: '.dots-container-moto',
            dotClass: 'dotmoto',
            btnLeft: '.btn-left-motos',
            btnRight: '.btn-right-motos',
            width: 870
        });

        initCarousel({
            selector: '.carrusel-productos-equipos',
            slide: '.grupo-equipos',
            dots: '.dots-container-equipos',
            dotClass: 'dotequipos',
            btnLeft: '.btn-left-equipos',
            btnRight: '.btn-right-equipos',
            width: 1090
        });

        initCarousel({
            selector: '.carrusel-productos-despensas',
            slide: '.grupo-despensas',
            dots: '.dots-container-despensa',
            dotClass: 'dotdespensa',
            btnLeft: '.btn-left-despensas',
            btnRight: '.btn-right-despensas',
            width: 1090
        });

        initCarousel({
            selector: '.carrusel-productos-combo',
            slide: '.grupo-combo',
            dots: '.dots-container-combo',
            dotClass: 'dotcombo',
            btnLeft: '.btn-left-combo',
            btnRight: '.btn-right-combo',
            width: 1090
        });

        initCarousel({
            selector: '.carrusel-productos-contenedor',
            slide: '.grupo-contenedor',
            dots: '.dots-container-contenedor',
            dotClass: 'dotcontenedor',
            btnLeft: '.btn-left-contenedor',
            btnRight: '.btn-right-contenedor',
            width: 1090
        });

        initCarousel({
            selector: '.carrusel-productos-solares',
            slide: '.grupo-solares',
            dots: '.dots-container-solares',
            dotClass: 'dotsolares',
            btnLeft: '.btn-left-solares',
            btnRight: '.btn-right-solares',
            width: 1090
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
