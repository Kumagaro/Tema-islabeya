<?php 
// ============================================
// MOSTRAR TIPO DE ENVÍO EN DETALLES DE ORDEN
// ============================================

/**
 * Muestra un badge con el tipo de envío en la página de detalles de la orden
 * 
 * @param int                   $item_id ID del ítem de la orden
 * @param WC_Order_Item_Product $item    Objeto del ítem de la orden
 * @param WC_Order              $order   Objeto de la orden
 */
add_action('woocommerce_order_item_meta_start', 'display_shipping_in_order', 10, 3);

function display_shipping_in_order($item_id, $item, $order) {
    $shipping_type = $item->get_meta('_shipping_type');
    
    if ($shipping_type) {
        $text = ($shipping_type == 'recogida') ? 'Recogida en el Local' : 'Envío Gratuito';
        $color = ($shipping_type == 'recogida') ? '#ff6b35' : '#00b894';
        $background = ($shipping_type == 'recogida') ? '#fff5f0' : '#f0f9f4';
        
        echo '<br><div style="
            display: inline-block;
            margin-top: 8px;
            padding: 5px 12px;
            background: ' . $background . ';
            border-radius: 20px;
            border-left: 3px solid ' . $color . ';
            font-size: 12px;
            font-weight: 500;
            font-family: \'Fuente Poppins\', sans-serif;
        ">';
        echo '<strong>Tipo de entrega:</strong> ' . esc_html($text);
        echo '</div>';
    }
}

/**
 * MOSTRAR CARNET DE IDENTIDAD Y Nº DE CASA EN LA ORDEN
 * Añade el carnet de identidad y el número de casa/edificio
 * a la dirección de envío formateada para que aparezcan
 * tanto en el admin como en las vistas del cliente.
 */
add_filter( 'woocommerce_order_formatted_shipping_address', 'islabeya_agregar_carnet_a_direccion_envio', 10, 2 );
function islabeya_agregar_carnet_a_direccion_envio( $address, $order ) {
    if ( ! $order ) {
        return $address;
    }

    // Carnet de Identidad
    $carnet = $order->get_meta( '_shipping_identity_card' );
    if ( empty( $carnet ) ) {
        $carnet = $order->get_meta( '_shipping_carnet' );
    }
    if ( ! empty( $carnet ) ) {
        $address['identity_card'] = __( 'Carnet de Identidad: ', 'islabeya' ) . $carnet;
    }

    // Número de casa / Edificio y apartamento
    $house = $order->get_meta( '_shipping_house_number' );
    if ( ! empty( $house ) ) {
        $address['house_number'] = __( 'Nº casa/Edificio: ', 'islabeya' ) . $house;
    }

    return $address;
}

/**
 * Fallback para exportaciones/CSV que usan el array de dirección estándar.
 * Añade el carnet a la dirección de envío formateada.
 */
add_filter( 'woocommerce_order_shipping_address_lines', 'islabeya_agregar_carnet_a_lineas_envio', 10, 2 );
function islabeya_agregar_carnet_a_lineas_envio( $lines, $order ) {
    if ( ! $order ) {
        return $lines;
    }
    $carnet = $order->get_meta( '_shipping_identity_card' );
    if ( empty( $carnet ) ) {
        $carnet = $order->get_meta( '_shipping_carnet' );
    }
    if ( ! empty( $carnet ) ) {
        $lines['identity_card'] = __( 'Carnet de Identidad: ', 'islabeya' ) . $carnet;
    }
    return $lines;
}
