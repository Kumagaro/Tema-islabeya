<?php
/**
 * Validación de checkout para productos gratis-provincia
 * Verifica que la dirección de envío coincida con las provincias permitidas
 */

add_action('woocommerce_checkout_process', 'islabeya_validar_gratis_provincia_checkout');

function islabeya_validar_gratis_provincia_checkout() {
    // Verificar si hay productos gratis-provincia en el carrito
    $gratis_provincia_products = array();
    $provincias_permitidas = array();
    
    foreach (WC()->cart->get_cart() as $cart_item) {
        $product_id = $cart_item['product_id'];
        $shipping_type = get_post_meta($product_id, '_shipping_type', true);
        
        if ($shipping_type === 'gratis-provincia') {
            $gratis_provincia_products[] = array(
                'id' => $product_id,
                'name' => $cart_item['data']->get_name()
            );
            
            // Obtener provincias permitidas de este producto usando taxonomía nativa
            $provincias_terms = wp_get_post_terms($product_id, 'provincia');
            if (!empty($provincias_terms) && !is_wp_error($provincias_terms)) {
                foreach ($provincias_terms as $term) {
                    $provincias_permitidas[] = $term->term_id;
                }
            }
        }
    }
    
    // Si no hay productos gratis-provincia, no hacer nada
    if (empty($gratis_provincia_products)) {
        return;
    }
    
    // Obtener provincia de envío del checkout
    $shipping_province = '';
    if (isset($_POST['shipping_state']) && !empty($_POST['shipping_state'])) {
        $shipping_province = sanitize_text_field($_POST['shipping_state']);
    } elseif (isset($_POST['billing_state']) && !empty($_POST['billing_state'])) {
        $shipping_province = sanitize_text_field($_POST['billing_state']);
    }
    
    // Si no hay provincia seleccionada, mostrar error
    if (empty($shipping_province)) {
        wc_add_notice(
            '<strong>⚠️ Error de envío:</strong> Debes seleccionar una provincia en tu dirección de envío para continuar con la compra.',
            'error'
        );
        return;
    }
    
    // Obtener nombres de las provincias permitidas
    $provincias_nombres = array();
    $provincias_permitidas = array_unique($provincias_permitidas);
    
    foreach ($provincias_permitidas as $province_id) {
        $term = get_term($province_id, 'provincia');
        if ($term && !is_wp_error($term)) {
            $provincias_nombres[] = $term->name;
        }
    }
    
    // Verificar si hay múltiples provincias diferentes
    if (count($provincias_permitidas) > 1) {
        $productos_nombres = array();
        foreach ($gratis_provincia_products as $product) {
            $productos_nombres[] = $product['name'];
        }
        
        wc_add_notice(
            '<strong>⚠️ Error de envío:</strong> Tu carrito contiene productos con envío gratis por provincia que solo se pueden enviar a diferentes provincias. No es posible realizar envíos a múltiples provincias en un mismo pedido.<br><br>' .
            '<strong>Productos afectados:</strong> ' . implode(', ', $productos_nombres) . '<br><br>' .
            'Por favor, separa tu compra en pedidos diferentes o contacta con soporte.',
            'error'
        );
        return;
    }
    
    // Verificar si la provincia seleccionada coincide con las permitidas
    $provincia_coincide = false;
    $provincia_seleccionada_nombre = '';
    
    // Intentar obtener el término de la provincia seleccionada
    // Primero buscar por slug o nombre
    $provincia_seleccionada_nombre = $shipping_province;
    
    // Normalizar para comparación
    $shipping_province_normalized = strtolower(trim($shipping_province));
    
    foreach ($provincias_permitidas as $province_id) {
        $term = get_term($province_id, 'provincia');
        if ($term && !is_wp_error($term)) {
            // Comparar por slug, nombre o ID
            if (
                strtolower($term->slug) === $shipping_province_normalized ||
                strtolower($term->name) === $shipping_province_normalized ||
                $term->term_id == $shipping_province
            ) {
                $provincia_coincide = true;
                $provincia_seleccionada_nombre = $term->name;
                break;
            }
        }
    }
    
    if (!$provincia_coincide) {
        $productos_nombres = array();
        foreach ($gratis_provincia_products as $product) {
            $productos_nombres[] = $product['name'];
        }
        
        $provincia_texto = !empty($provincias_nombres) ? implode(', ', $provincias_nombres) : 'la provincia específica';
        
        wc_add_notice(
            '<strong>⚠️ Error de envío:</strong> El producto <strong>' . implode(', ', $productos_nombres) . '</strong> solo se puede enviar a <strong>' . $provincia_texto . '</strong>.<br><br>' .
            'Tu dirección de envío actual está en: <strong>' . esc_html($shipping_province) . '</strong>.<br><br>' .
            'Por favor, actualiza tu dirección de envío a ' . $provincia_texto . ' para continuar con la compra.',
            'error'
        );
    }
}
