<?php 
// ============================================
// GUARDAR CAMPO DE TIPO DE ENVÍO
// ============================================

/**
 * Guarda el tipo de envío cuando se guarda un producto desde:
 * - Dokan (nuevo producto o actualización)
 * - Admin de WooCommerce
 */
add_action('dokan_new_product_added', 'save_shipping_type', 20);
add_action('dokan_product_updated', 'save_shipping_type', 20);
add_action('woocommerce_process_product_meta', 'save_shipping_type', 20);

function save_shipping_type($product_id) {
    if (isset($_POST['shipping_type']) && !empty($_POST['shipping_type'])) {
        $shipping_type = sanitize_text_field($_POST['shipping_type']);
        update_post_meta($product_id, '_shipping_type', $shipping_type);
        
        // Para gratis-provincia, usamos la taxonomía provincia nativa
        // No necesitamos guardar nada adicional aquí, los checkboxes nativos de WordPress
        // ya guardan automáticamente los términos de la taxonomía provincia
    }
}