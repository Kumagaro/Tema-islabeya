<?php
/**
 * ============================================
 * FUNCIONES AUXILIARES PARA LISTA DE PRODUCTOS EN CORREOS
 * ============================================
 *
 * - Obtiene información completa de producto (imagen, nombre, vendedor, tipo de entrega, cantidad, precio)
 * - Agrupa productos por tipo de entrega y dirección de recogida
 * - Renderiza lista de productos con nueva estructura
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Obtiene información completa de un producto para mostrar en correos
 * 
 * @param WC_Order_Item_Product $item Ítem del pedido
 * @param WC_Order $order Pedido
 * @return array Información del producto
 */
function islabeya_email_get_product_info($item, $order) {
    $product = $item->get_product();
    $product_id = $item->get_product_id();
    
    // Imagen del producto
    $image_url = '';
    if ($product) {
        $image_id = $product->get_image_id();
        if ($image_id) {
            $image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
        }
        if (empty($image_url)) {
            $image_url = wc_placeholder_img_src();
        }
    }
    
    // Nombre del producto
    $product_name = $item->get_name();
    
    // Vendedor (tienda)
    $vendor_id = get_post_field('post_author', $product_id);
    $vendor_name = '';
    $vendor_address = '';
    
    if ($vendor_id) {
        if (function_exists('dokan_get_store_info')) {
            $store_info = dokan_get_store_info($vendor_id);
            if (!empty($store_info['store_name'])) {
                $vendor_name = $store_info['store_name'];
            } else {
                $vendor_name = get_the_author_meta('display_name', $vendor_id);
            }
            
            // Dirección de la tienda
            if (!empty($store_info['address'])) {
                $address_parts = array();
                if (!empty($store_info['address']['street_1'])) {
                    $address_parts[] = $store_info['address']['street_1'];
                }
                if (!empty($store_info['address']['city'])) {
                    $address_parts[] = $store_info['address']['city'];
                }
                if (!empty($store_info['address']['state'])) {
                    $address_parts[] = $store_info['address']['state'];
                }
                $vendor_address = implode(', ', $address_parts);
            }
        } else {
            $vendor_name = get_the_author_meta('display_name', $vendor_id);
        }
    }
    
    if (empty($vendor_name)) {
        $vendor_name = 'Tienda Local';
    }
    
    // Tipo de entrega (del metadato del ítem o del producto)
    $shipping_type = $item->get_meta('_shipping_type', true);
    if (empty($shipping_type)) {
        $shipping_type = get_post_meta($product_id, '_shipping_type', true);
    }
    if (empty($shipping_type)) {
        $shipping_type = 'gratis'; // Default
    }
    
    // Texto del tipo de entrega
    $shipping_text = ($shipping_type === 'recogida') ? 'Recogida en local' : 'Envío gratuito';
    
    // Cantidad
    $quantity = $item->get_quantity();
    
    // Precio unitario
    $unit_price = $item->get_subtotal() / $quantity;
    
    // Precio total del ítem
    $total_price = $item->get_total();
    
    // Variaciones del producto
    $variations = array();
    $item_data = $item->get_formatted_meta_data();
    foreach ($item_data as $meta) {
        $variations[] = array(
            'key' => $meta->display_key,
            'value' => $meta->display_value
        );
    }
    
    return array(
        'image_url' => $image_url,
        'product_name' => $product_name,
        'vendor_name' => $vendor_name,
        'vendor_address' => $vendor_address,
        'vendor_id' => $vendor_id,
        'shipping_type' => $shipping_type,
        'shipping_text' => $shipping_text,
        'quantity' => $quantity,
        'unit_price' => $unit_price,
        'total_price' => $total_price,
        'variations' => $variations
    );
}

/**
 * Agrupa los productos del pedido por tipo de entrega y dirección de recogida
 * 
 * @param WC_Order $order Pedido
 * @return array Productos agrupados
 */
function islabeya_email_group_products_by_delivery($order) {
    $groups = array(
        'shipping' => array(
            'label' => 'Envío a domicilio',
            'address' => '',
            'products' => array()
        ),
        'pickup' => array()
    );
    
    foreach ($order->get_items() as $item_id => $item) {
        if (!$item instanceof WC_Order_Item_Product) {
            continue;
        }
        
        $product_info = islabeya_email_get_product_info($item, $order);
        
        if ($product_info['shipping_type'] === 'recogida') {
            // Agrupar por dirección de recogida (vendedor)
            $address_key = $product_info['vendor_address'];
            if (empty($address_key)) {
                $address_key = $product_info['vendor_name'];
            }
            
            if (!isset($groups['pickup'][$address_key])) {
                $groups['pickup'][$address_key] = array(
                    'label' => 'Recogida en local',
                    'address' => $address_key,
                    'vendor_name' => $product_info['vendor_name'],
                    'vendor_id' => $product_info['vendor_id'],
                    'products' => array()
                );
            }
            
            $groups['pickup'][$address_key]['products'][] = $product_info;
        } else {
            // Envío a domicilio
            $groups['shipping']['products'][] = $product_info;
        }
    }
    
    return $groups;
}

/**
 * Renderiza un producto individual en formato lista
 * 
 * @param array $product_info Información del producto
 * @return string HTML del producto
 */
function islabeya_email_render_product_item($product_info) {
    $output = '<div class="product-list-item">';
    
    // Imagen
    $output .= '<div class="product-image">';
    $output .= '<img src="' . esc_url($product_info['image_url']) . '" alt="' . esc_attr($product_info['product_name']) . '" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">';
    $output .= '</div>';
    
    // Información del producto
    $output .= '<div class="product-details">';
    
    // Nombre
    $output .= '<div class="product-name">' . esc_html($product_info['product_name']) . '</div>';
    
    // Variaciones
    if (!empty($product_info['variations'])) {
        $output .= '<div class="product-variations">';
        foreach ($product_info['variations'] as $variation) {
            $output .= '<span class="variation">' . esc_html($variation['key']) . ': ' . esc_html($variation['value']) . '</span>';
        }
        $output .= '</div>';
    }
    
    // Vendedor y tipo de entrega
    $output .= '<div class="product-meta">';
    $output .= '<span class="vendor-name">' . esc_html($product_info['vendor_name']) . '</span>';
    $output .= '<span class="shipping-type">' . esc_html($product_info['shipping_text']) . '</span>';
    $output .= '</div>';
    
    $output .= '</div>';
    
    // Cantidad y precio
    $output .= '<div class="product-qty-price">';
    $output .= '<div class="quantity">x' . esc_html($product_info['quantity']) . '</div>';
    $output .= '<div class="price">' . wc_price($product_info['total_price']) . '</div>';
    $output .= '</div>';
    
    $output .= '</div>';
    
    return $output;
}

/**
 * Renderiza la lista completa de productos agrupados
 * 
 * @param WC_Order $order Pedido
 * @return string HTML de la lista de productos
 */
function islabeya_email_render_products_list($order) {
    $groups = islabeya_email_group_products_by_delivery($order);
    $output = '<div class="products-list-container">';
    
    // Envío a domicilio
    if (!empty($groups['shipping']['products'])) {
        $output .= '<div class="delivery-group shipping-group">';
        $output .= '<div class="group-header">';
        $output .= '<div class="group-label">' . esc_html($groups['shipping']['label']) . '</div>';
        if (!empty($order->get_shipping_address_1())) {
            $output .= '<div class="group-address">';
            $output .= esc_html($order->get_shipping_address_1());
            if ($order->get_shipping_city()) {
                $output .= ', ' . esc_html($order->get_shipping_city());
            }
            if ($order->get_shipping_state()) {
                $output .= ', ' . esc_html($order->get_shipping_state());
            }
            $output .= '</div>';
        }
        $output .= '</div>';
        
        foreach ($groups['shipping']['products'] as $product) {
            $output .= islabeya_email_render_product_item($product);
        }
        
        $output .= '</div>';
    }
    
    // Recogida local (agrupado por dirección)
    if (!empty($groups['pickup'])) {
        foreach ($groups['pickup'] as $pickup_group) {
            $output .= '<div class="delivery-group pickup-group">';
            $output .= '<div class="group-header">';
            $output .= '<div class="group-label">' . esc_html($pickup_group['label']) . '</div>';
            $output .= '<div class="group-address">' . esc_html($pickup_group['address']) . '</div>';
            $output .= '</div>';
            
            foreach ($pickup_group['products'] as $product) {
                $output .= islabeya_email_render_product_item($product);
            }
            
            $output .= '</div>';
        }
    }
    
    $output .= '</div>';
    
    return $output;
}

/**
 * Estilos CSS para la lista de productos en correos
 */
function islabeya_email_products_list_styles() {
    $color_primary = '#15ad3c';
    $color_secondary = '#f5f5f5';
    $color_text = '#000';
    $color_text_secondary = '#6b7280';
    
    ob_start();
    ?>
    <style>
        .products-list-container {
            width: 100%;
        }
        
        .delivery-group {
            margin-bottom: 20px;
        }
        
        .group-header {
            background-color: <?php echo $color_secondary; ?>;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 12px;
        }
        
        .group-label {
            font-size: 13px;
            font-weight: 700;
            color: <?php echo $color_text; ?>;
            margin-bottom: 4px;
        }
        
        .group-address {
            font-size: 11px;
            color: <?php echo $color_text_secondary; ?>;
        }
        
        .product-list-item {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #eeeeee;
        }
        
        .product-list-item:last-child {
            border-bottom: none;
        }
        
        .product-image {
            flex-shrink: 0;
            margin-right: 12px;
        }
        
        .product-details {
            flex-grow: 1;
            min-width: 0;
        }
        
        .product-name {
            font-size: 13px;
            font-weight: 600;
            color: <?php echo $color_text; ?>;
            margin-bottom: 4px;
        }
        
        .product-variations {
            font-size: 11px;
            color: <?php echo $color_text_secondary; ?>;
            margin-bottom: 6px;
        }
        
        .product-variations .variation {
            display: inline-block;
            margin-right: 8px;
        }
        
        .product-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
        }
        
        .vendor-name {
            color: <?php echo $color_text_secondary; ?>;
        }
        
        .shipping-type {
            color: <?php echo $color_primary; ?>;
            font-weight: 600;
        }
        
        .product-qty-price {
            flex-shrink: 0;
            text-align: right;
            margin-left: 12px;
        }
        
        .quantity {
            font-size: 12px;
            color: <?php echo $color_text_secondary; ?>;
            margin-bottom: 4px;
        }
        
        .price {
            font-size: 14px;
            font-weight: 700;
            color: <?php echo $color_text; ?>;
        }
        
        @media only screen and (max-width: 520px) {
            .product-list-item {
                flex-wrap: wrap;
            }
            
            .product-qty-price {
                width: 100%;
                text-align: left;
                margin-left: 0;
                margin-top: 8px;
                display: flex;
                justify-content: space-between;
            }
        }
    </style>
    <?php
    return ob_get_clean();
}
