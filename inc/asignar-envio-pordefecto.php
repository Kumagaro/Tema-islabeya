<?php
// ============================================
// ASIGNAR ENVÍO GRATUITO POR DEFECTO
// ============================================

/**
 * Asigna "Envío Gratuito" a todos los productos que no tienen el campo _shipping_type
 * Esta función se ejecuta solo una vez al activar el tema o al agregar este código
 */
add_action('init', 'set_default_shipping_for_products');

function set_default_shipping_for_products() {
    // Verificar si ya se ejecutó esta función
    if (get_option('shipping_default_set') == '1') {
        return;
    }
    
    // Buscar productos que NO tienen el meta _shipping_type
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_query'     => array(
            array(
                'key'     => '_shipping_type',
                'compare' => 'NOT EXISTS'
            )
        )
    );
    
    $products = get_posts($args);
    
    // Asignar 'gratuito' a cada producto encontrado
    foreach ($products as $product_id) {
        update_post_meta($product_id, '_shipping_type', 'gratuito');
    }
    
    // Marcar que ya se ejecutó (para que no se repita)
    update_option('shipping_default_set', '1');
}