(function() {
    'use strict';
    
    // Función para crear las estrellas interactivas
    function crearEstrellasInteractivas() {
        var ratingSelect = document.querySelector('.comment-form-rating select[name="rating"]');
        
        if (!ratingSelect) return;
        
        // Ocultar el selector nativo
        ratingSelect.style.display = 'none';
        
        // Buscar el contenedor del formulario de rating
        var commentFormRating = document.querySelector('.comment-form-rating');
        if (!commentFormRating) return;
        
        // Verificar si ya existe nuestro contenedor
        if (document.querySelector('.islabeya-rating-container')) return;
        
        // Crear el contenedor de estrellas
        var container = document.createElement('div');
        container.className = 'islabeya-rating-container';
        
        // Crear el label
        var label = document.createElement('label');
        label.textContent = 'Tu puntuación *';
        container.appendChild(label);
        
        // Crear el contenedor de estrellas
        var starsContainer = document.createElement('div');
        starsContainer.className = 'islabeya-stars';
        
        // Crear 5 estrellas
        var estrellas = [];
        for (var i = 1; i <= 5; i++) {
            var star = document.createElement('span');
            star.innerHTML = '★';
            star.setAttribute('data-value', i);
            star.setAttribute('aria-label', i + ' estrella' + (i > 1 ? 's' : ''));
            starsContainer.appendChild(star);
            estrellas.push(star);
        }
        
        container.appendChild(starsContainer);
        
        // Crear mensaje de feedback
        var feedback = document.createElement('div');
        feedback.className = 'islabeya-rating-feedback';
        feedback.textContent = 'Selecciona una puntuación';
        container.appendChild(feedback);
        
        // Insertar antes del campo nativo
        commentFormRating.insertBefore(container, ratingSelect);
        
        var valorSeleccionado = 0;
        
        // Función para actualizar estrellas
        function actualizarEstrellas(valor) {
            estrellas.forEach(function(star, index) {
                if (index < valor) {
                    star.classList.add('selected');
                } else {
                    star.classList.remove('selected');
                }
            });
        }
        
        // Función para manejar hover
        function manejarHover(valor) {
            estrellas.forEach(function(star, index) {
                if (index < valor) {
                    star.classList.add('hover');
                } else {
                    star.classList.remove('hover');
                }
            });
        }
        
        // Agregar eventos a las estrellas
        estrellas.forEach(function(star) {
            var valor = parseInt(star.getAttribute('data-value'));
            
            star.addEventListener('click', function() {
                valorSeleccionado = valor;
                ratingSelect.value = valor;
                actualizarEstrellas(valor);
                feedback.textContent = 'Has seleccionado ' + valor + ' estrella' + (valor > 1 ? 's' : '');
                
                // Disparar evento change para WooCommerce
                var event = new Event('change', { bubbles: true });
                ratingSelect.dispatchEvent(event);
            });
            
            star.addEventListener('mouseenter', function() {
                manejarHover(valor);
                feedback.textContent = valor + ' estrella' + (valor > 1 ? 's' : '');
            });
            
            star.addEventListener('mouseleave', function() {
                manejarHover(0);
                if (valorSeleccionado > 0) {
                    feedback.textContent = 'Has seleccionado ' + valorSeleccionado + ' estrella' + (valorSeleccionado > 1 ? 's' : '');
                } else {
                    feedback.textContent = 'Selecciona una puntuación';
                }
            });
        });
        
        // Si ya hay un valor seleccionado por defecto
        if (ratingSelect.value && parseInt(ratingSelect.value) > 0) {
            valorSeleccionado = parseInt(ratingSelect.value);
            actualizarEstrellas(valorSeleccionado);
            feedback.textContent = 'Has seleccionado ' + valorSeleccionado + ' estrella' + (valorSeleccionado > 1 ? 's' : '');
        }
    }
    
    // Ejecutar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', crearEstrellasInteractivas);
    } else {
        crearEstrellasInteractivas();
    }
    
    // También ejecutar cuando WooCommerce cargue variaciones (por si acaso)
    if (typeof jQuery !== 'undefined') {
        jQuery(document).on('found_variation', function() {
            setTimeout(crearEstrellasInteractivas, 100);
        });
    }
})();