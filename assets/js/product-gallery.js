/**
 * Product Gallery JavaScript
 * Para manejar el cambio de imágenes y lightbox
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Verificar que estamos en una página de producto
        if ( ! $('.woocommerce-product-gallery').length ) {
            return;
        }
        
        /**
         * Función para inicializar el lightbox en todas las imágenes
         */
        function initLightbox() {
            if ( typeof PhotoSwipe === 'undefined' ) {
                return;
            }
            
            // Función para abrir lightbox desde cualquier imagen
            function openLightbox(currentIndex) {
                // Obtener todas las imágenes de la galería (imagen principal + miniaturas)
                var items = [];
                var $mainImage = $('.product-gallery__main-image .main-image-wrapper a');
                var $thumbnails = $('.thumbnail-item a');
                var $allImages = $mainImage.add($thumbnails);
                
                $allImages.each(function(index) {
                    var $this = $(this);
                    var href = $this.attr('href');
                    var $img = $this.find('img');
                    var caption = $this.data('caption') || $img.attr('alt') || '';
                    
                    // Obtener dimensiones reales de la imagen
                    var imgWidth = $img.data('full-width') || 1200;
                    var imgHeight = $img.data('full-height') || 1200;
                    
                    items.push({
                        src: href,
                        w: imgWidth,
                        h: imgHeight,
                        title: caption
                    });
                });
                
                // Configurar opciones de PhotoSwipe
                var options = {
                    index: currentIndex,
                    bgOpacity: 0.85,
                    showHideOpacity: true,
                    history: false,
                    shareEl: false,
                    captionEl: true,
                    fullscreenEl: true,
                    zoomEl: true,
                    closeEl: true,
                    arrowEl: true
                };
                
                // Inicializar PhotoSwipe
                var gallery = new PhotoSwipe($('.pswp')[0], PhotoSwipeUI_Default, items, options);
                gallery.init();
            }
            
            // Evento para la imagen principal
            $('.product-gallery__main-image .main-image-wrapper a').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Encontrar el índice de la imagen principal (siempre es 0 si es la primera)
                var currentIndex = 0;
                openLightbox(currentIndex);
            });
            
            // Evento para las miniaturas
            $('.thumbnail-item a').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Calcular el índice: la miniatura 1 es índice 1 (porque la imagen principal ocupa el índice 0)
                var currentIndex = $(this).closest('.thumbnail-item').index() + 1;
                openLightbox(currentIndex);
            });
        }
        
        /**
         * Cambiar la imagen principal al hacer clic en una miniatura
         */
        function initThumbnailClick() {
            $('.thumbnail-item').off('click').on('click', function(e) {
                e.preventDefault();
                
                var $this = $(this);
                var $mainImageContainer = $('.product-gallery__main-image .main-image-wrapper');
                var $thumbnailImg = $this.find('img');
                
                // Obtener datos de la imagen seleccionada
                var fullImageUrl = $thumbnailImg.data('full-image');
                var altText = $thumbnailImg.attr('alt') || '';
                var imageId = $this.data('image-id');
                var caption = $this.find('a').data('caption') || altText;
                var fullWidth = $thumbnailImg.data('full-width') || 1200;
                var fullHeight = $thumbnailImg.data('full-height') || 1200;
                
                // Si no hay URL, salir
                if ( ! fullImageUrl ) {
                    return;
                }
                
                // Cambiar la imagen principal y el enlace del lightbox
                $mainImageContainer.html(
                    '<a href="' + fullImageUrl + '" class="woocommerce-main-image zoom" data-caption="' + caption + '">' +
                        '<img src="' + fullImageUrl + '" alt="' + altText + '" class="wp-post-image" data-full-image="' + fullImageUrl + '" data-full-width="' + fullWidth + '" data-full-height="' + fullHeight + '">' +
                    '</a>'
                );
                
                // Actualizar el data-image-id del contenedor principal
                $mainImageContainer.data('image-id', imageId);
                
                // Actualizar clase activa en miniaturas
                $('.thumbnail-item').removeClass('active');
                $this.addClass('active');
                
                // Reinicializar eventos de lightbox para la nueva imagen principal
                initLightbox();
                
                // Disparar evento personalizado
                $(document).trigger('product-gallery-image-changed', [{
                    imageId: imageId,
                    imageUrl: fullImageUrl,
                    altText: altText
                }]);
            });
        }
        
        // Inicializar todo
        initLightbox();
        initThumbnailClick();
        
        /**
         * Soporte para teclado (navegación con flechas)
         */
        $(document).on('keydown', function(e) {
            if ( ! $('.woocommerce-product-gallery').length ) {
                return;
            }
            
            // Evitar que funcione si hay un modal abierto
            if ( $('.pswp--open').length ) {
                return;
            }
            
            var $activeThumb = $('.thumbnail-item.active');
            var $allThumbs = $('.thumbnail-item');
            var currentIndex = $allThumbs.index($activeThumb);
            
            if ( e.keyCode === 39 && currentIndex < $allThumbs.length - 1 ) {
                e.preventDefault();
                $allThumbs.eq(currentIndex + 1).trigger('click');
            }
            
            if ( e.keyCode === 37 && currentIndex > 0 ) {
                e.preventDefault();
                $allThumbs.eq(currentIndex - 1).trigger('click');
            }
        });
        
    });
    
})(jQuery);