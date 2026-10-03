<?php
/**
 * COMPRA DIRECTA POR ENLACE - USA template_redirect
 * Uso: https://islabeya.com/checkouts/verificar/?comprar=11212
 */
add_action( 'template_redirect', 'islabeya_enlace_comprar_ahora' );
function islabeya_enlace_comprar_ahora() {
    // Verificar si existe el parámetro 'comprar' en la URL
    if ( isset( $_GET['comprar'] ) && is_numeric( $_GET['comprar'] ) ) {
        
        $product_id = intval( $_GET['comprar'] );
        $quantity = 1;
        $redirect_url = wc_get_checkout_url();
        
        // Validación para contenedores
        if ( $product_id && has_term( 'contenedor', 'product_cat', $product_id ) ) {
            wc_add_notice( 
                '⚠️ Debes aceptar TODOS los requisitos obligatorios: Ser PYME/TCP y estar inscrito en Impocaribe.', 
                'error' 
            );
            wp_redirect( get_permalink( $product_id ) );
            exit;
        }
        
        // Limpiar carrito
        if ( function_exists( 'WC' ) && isset( WC()->cart ) ) {
            WC()->cart->empty_cart();
        }
        
        // Obtener el producto
        $product = wc_get_product( $product_id );
        
        if ( ! $product ) {
            wc_add_notice( 'Producto no encontrado.', 'error' );
            wp_redirect( home_url( '/tienda/' ) );
            exit;
        }
        
        // Agregar al carrito (igual que en el botón)
        if ( $product->is_type( 'simple' ) ) {
            $added = WC()->cart->add_to_cart( $product_id, $quantity );
        } elseif ( $product->is_type( 'variable' ) ) {
            $variations = $product->get_available_variations();
            if ( ! empty( $variations ) ) {
                $variation_id = $variations[0]['variation_id'];
                $variation_attributes = $variations[0]['attributes'];
                $added = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation_attributes );
            } else {
                $added = false;
            }
        } else {
            $added = WC()->cart->add_to_cart( $product_id, $quantity );
        }
        
        // Redirigir
        if ( $added ) {
            wp_redirect( $redirect_url );
            exit;
        } else {
            wc_add_notice( 'Error al añadir el producto al carrito.', 'error' );
            wp_redirect( home_url( '/tienda/' ) );
            exit;
        }
    }
}