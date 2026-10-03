/**
 * Funcionalidad de acordeón para el menú de categorías y tiendas en móvil
 */
(function() {
    'use strict';
    
    function initMovilAcordeones() {
        // Acordeón de Categorías
        const menuCategorias = document.querySelector('.menu-item-categorias-movil');
        if (menuCategorias) {
            const headerCategorias = menuCategorias.querySelector('.categorias-movil-header');
            if (headerCategorias) {
                headerCategorias.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    menuCategorias.classList.toggle('open');
                });
            }
        }
        
        // Acordeón de Tiendas
        const menuTiendas = document.querySelector('.menu-item-tiendas-movil');
        if (menuTiendas) {
            const headerTiendas = menuTiendas.querySelector('.tiendas-movil-header');
            if (headerTiendas) {
                headerTiendas.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    menuTiendas.classList.toggle('open');
                });
            }
        }
    }
    
    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMovilAcordeones);
    } else {
        initMovilAcordeones();
    }
})();