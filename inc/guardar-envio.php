<?php 
// ============================================
// GUARDAR TIPO DE ENVÍO EN LA ORDEN
// ============================================

/**
 * Guarda el tipo de envío como metadato en cada ítem de la orden
 * 
 * @param WC_Order_Item_Product $item       El ítem de la orden
 * @param string                $cart_item_key Clave del ítem en el carrito
 * @param array                 $values     Datos del ítem en el carrito
 * @param WC_Order              $order      Objeto de la orden
 */
add_action('woocommerce_checkout_create_order_line_item', 'save_shipping_to_order', 10, 4);

function save_shipping_to_order($item, $cart_item_key, $values, $order) {
    $product_id = $values['product_id'];
    $shipping_type = get_post_meta($product_id, '_shipping_type', true);
    
    if ($shipping_type) {
        $text = '';
        
        if ($shipping_type == 'recogida') {
            $text = 'Recogida en el Local';
        } elseif ($shipping_type == 'gratis-provincia') {
            // Obtener provincias permitidas usando taxonomía nativa
            $provincias_terms = wp_get_post_terms($product_id, 'provincia');
            if (!empty($provincias_terms) && !is_wp_error($provincias_terms)) {
                $first_province = $provincias_terms[0];
                $text = 'Envío gratis - ' . $first_province->name;
            } else {
                $text = 'Envío gratis por provincia';
            }
        } else {
            $text = 'Envío Gratuito';
        }
        
        // Guardar metadato invisible (para uso interno)
        $item->add_meta_data('_shipping_type', $shipping_type);
        
        // Guardar metadato visible (para mostrar al cliente y admin)
        $item->add_meta_data('Tipo de entrega', $text);
    }
}