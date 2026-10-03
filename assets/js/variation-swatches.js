/**
 * Selectores visuales para variaciones de producto - IslaBeya
 * Con cambio automático de imagen al seleccionar variación
 */
jQuery(document).ready(function($) {
    
    // Variable para almacenar la URL de la imagen por defecto
    var defaultImageSrc = '';
    var defaultImageSrcset = '';
    
    // Guardar la imagen por defecto al cargar
    function saveDefaultImage() {
        var $mainImage = $('.woocommerce-product-gallery img:first');
        if ($mainImage.length) {
            defaultImageSrc = $mainImage.attr('src');
            defaultImageSrcset = $mainImage.attr('srcset');
        }
    }
    
    // Función para actualizar la imagen principal
    function updateMainImage(variation) {
        var $mainImage = $('.woocommerce-product-gallery img:first');
        
        if ($mainImage.length && variation && variation.image) {
            // Actualizar con la imagen de la variación
            if (variation.image.src) {
                $mainImage.attr('src', variation.image.src);
                $mainImage.attr('srcset', variation.image.srcset || '');
                $mainImage.attr('sizes', 'auto');
            }
            
            // También actualizar el enlace del zoom/lightbox si existe
            var $mainLink = $('.woocommerce-product-gallery a:first');
            if ($mainLink.length && variation.image.src) {
                $mainLink.attr('href', variation.image.full_src || variation.image.src);
            }
        } else if (defaultImageSrc) {
            // Restaurar imagen por defecto si no hay variación seleccionada
            $mainImage.attr('src', defaultImageSrc);
            $mainImage.attr('srcset', defaultImageSrcset);
        }
    }
    
    // Inicializar los swatches
    function initSwatches() {
        $('.islabeya-swatches').each(function() {
            var $container = $(this);
            var attribute = $container.data('attribute');
            
            // Marcar swatch seleccionado según el valor actual del select
            var $select = $container.closest('.value').find('select');
            var currentValue = $select.val();
            
            $container.find('.swatch').removeClass('selected');
            if (currentValue) {
                $container.find('.swatch[data-value="' + currentValue + '"]').addClass('selected');
            }
        });
    }
    
    // Función para obtener los datos de la variación seleccionada
    function getSelectedVariation() {
        var $variationForm = $('.variations_form');
        if (!$variationForm.length) return null;
        
        var variationId = $variationForm.find('input[name="variation_id"]').val();
        if (!variationId || variationId === '') return null;
        
        // Buscar en los datos de variaciones disponibles
        var variationsData = $variationForm.data('product_variations');
        if (!variationsData) return null;
        
        for (var i = 0; i < variationsData.length; i++) {
            if (variationsData[i].variation_id == variationId) {
                return variationsData[i];
            }
        }
        
        return null;
    }
    
    // Escuchar el evento show_variation de WooCommerce
    // Este evento se dispara cuando se selecciona una variación completa
    $('.single_variation_wrap').on('show_variation', function(event, variation) {
        if (variation && variation.image) {
            updateMainImage(variation);
        }
        initSwatches();
    });
    
    // También escuchar cuando se resetea la variación
    $('.single_variation_wrap').on('hide_variation', function() {
        // Restaurar imagen por defecto
        var $mainImage = $('.woocommerce-product-gallery img:first');
        if ($mainImage.length && defaultImageSrc) {
            $mainImage.attr('src', defaultImageSrc);
            $mainImage.attr('srcset', defaultImageSrcset);
        }
        initSwatches();
    });
    
    // Evento: clic en un swatch
    $(document).on('click', '.swatch', function(e) {
        e.preventDefault();
        
        var $swatch = $(this);
        var $container = $swatch.closest('.islabeya-swatches');
        var $value = $container.closest('.value');
        var $select = $value.find('select');
        var attribute = $container.data('attribute');
        var value = $swatch.data('value');
        
        // Cambiar el valor del select original
        $select.val(value).trigger('change');
        
        // Actualizar clase selected en los swatches
        $container.find('.swatch').removeClass('selected');
        $swatch.addClass('selected');
        
        // Forzar que WooCommerce actualice la variación
        $select.trigger('change');
    });
    
    // Escuchar cambios en los selects originales
    $('.variations select').on('change', function() {
        // Esperar a que WooCommerce procese el cambio
        setTimeout(function() {
            var variation = getSelectedVariation();
            if (variation) {
                updateMainImage(variation);
            }
            initSwatches();
        }, 100);
    });
    
    // Guardar imagen por defecto al cargar
    saveDefaultImage();
    
    // Inicializar al cargar la página
    initSwatches();
});