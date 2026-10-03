jQuery(function($) {
    // Prevenir clics múltiples y manejar el estado de carga
    $(document).on('click', '.ajax_add_to_cart', function(e) {
        var $btn = $(this);
        // Si ya está en carga, no hacer nada
        if ($btn.hasClass('loading')) return true; // permitir que WooCommerce procese

        // Guardar texto original
        var originalText = $btn.text();
        // Cambiar texto a "Añadiendo..."
        $btn.text('Añadiendo...');
        // Añadir clase loading (para el spinner)
        $btn.addClass('loading');

        // Evento cuando el producto se añade correctamente
        $(document.body).one('added_to_cart', function() {
            $btn.removeClass('loading');
            $btn.text(originalText);
            // Eliminar el enlace "Ver carrito" que WooCommerce añade
            $('.added_to_cart').remove();
        });

        // Evento si hay error (por ejemplo, problema de stock)
        $(document.body).one('wc_cart_button_updated', function() {
            $btn.removeClass('loading');
            $btn.text(originalText);
        });
    });
});