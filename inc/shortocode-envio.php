<?php 
// ============================================
// SHORTCODE PARA BADGES DE ENVÍO
// ============================================

/**
 * Shortcode para mostrar badge de tipo de envío
 * Uso: [badge_envio] o [badge_envio producto_id="123"] o [badge_envio tipo="recogida"]
 */
add_shortcode('badge_envio', 'badge_envio_shortcode');

function badge_envio_shortcode($atts) {
    // Atributos del shortcode
    $atts = shortcode_atts(
        array(
            'producto_id' => '', // ID del producto específico
            'tipo'        => '', // 'gratis', 'recogida' o vacío para detectar automático
            'clase'       => '', // Clase CSS adicional
        ),
        $atts,
        'badge_envio'
    );
    
    // Determinar el ID del producto
    $product_id = !empty($atts['producto_id']) ? intval($atts['producto_id']) : 0;
    
    // Si no hay ID específico, intentar obtenerlo del contexto actual
    if (empty($product_id)) {
        global $product;
        
        if (is_a($product, 'WC_Product')) {
            $product_id = $product->get_id();
        } elseif (is_singular('product')) {
            $product_id = get_the_ID();
        } elseif (function_exists('wc_get_product') && isset($GLOBALS['post']) && $GLOBALS['post']->post_type === 'product') {
            $product_id = $GLOBALS['post']->ID;
        } else {
            // En un loop de productos, intentar obtener el ID del post actual
            $product_id = get_the_ID();
        }
    }
    
    // Verificar que tenemos un ID válido
    if (empty($product_id) || !is_numeric($product_id)) {
        return ''; // No mostrar nada si no hay producto
    }
    
    // Determinar el tipo de envío
    if (!empty($atts['tipo']) && in_array($atts['tipo'], ['gratis', 'recogida', 'gratis-provincia'])) {
        $shipping_type = $atts['tipo'];
    } else {
        $shipping_type = obtener_tipo_envio_producto($product_id);
    }
    
    // Para gratis-provincia, obtener el nombre de la provincia
    $provincia_nombre = '';
    if ($shipping_type === 'gratis-provincia') {
        $provincias_terms = wp_get_post_terms($product_id, 'provincia');
        if (!empty($provincias_terms) && !is_wp_error($provincias_terms)) {
            $provincia_nombre = $provincias_terms[0]->name;
        }
    }
    
    // Configuración para cada tipo (sin segunda imagen)
    $config = array(
        'gratis' => array(
            'primera_imagen' => get_parent_theme_file_uri( '/assets/img/Envio.svg' ),
            'texto' => 'gratis',
            'color_fondo' => 'rgb(244 249 236)',
            'color_texto' => '#2C4A3E',
        ),
        'recogida' => array(
            'primera_imagen' => get_parent_theme_file_uri( '/assets/img/Recogida.svg' ),
            'texto' => obtener_provincia_tienda($product_id),
            'color_fondo' => 'rgb(255 245 236)',
            'color_texto' => '#B84A2C',
        ),
        'gratis-provincia' => array(
            'primera_imagen' => get_parent_theme_file_uri( '/assets/img/Gratis provincia.svg' ),
            'texto' => 'gratis-provincia',
            'color_fondo' => 'rgb(245 240 255)',
            'color_texto' => '#6c3483',
        )
    );
    
    // Si no hay configuración para el tipo, no mostrar nada
    if (!isset($config[$shipping_type])) {
        return '';
    }
    
    $cfg = $config[$shipping_type];
    
    // Si es recogida y no hay texto (provincia), no mostrar
    if ($shipping_type === 'recogida' && empty($cfg['texto'])) {
        return '';
    }
    
    // Para gratis-provincia, usar el nombre de la provincia como texto
    $texto_badge = $cfg['texto'];
    if ($shipping_type === 'gratis-provincia' && !empty($provincia_nombre)) {
        $texto_badge = $provincia_nombre;
    }
    
    // Construir clases CSS
    $clases = array('bs-product-card__trend-label', 'badge-envio-shortcode');
    if (!empty($atts['clase'])) {
        $clases[] = esc_attr($atts['clase']);
    }
    $clases[] = 'badge-envio-' . $shipping_type;
    
    // Construir el HTML del badge (sin segunda imagen)
    $output = sprintf(
        '<div class="%s" style="
            display: inline-flex; 
            align-items: center; 
            gap: 4px; 
            border-radius: 5px;
            max-width: 100%%;
            box-sizing: border-box;
            background-color: %s;
            color: %s;
            position: absolute;
            top: 5px;
            left: 5px;
        ">',
        implode(' ', $clases),
        esc_attr($cfg['color_fondo']),
        esc_attr($cfg['color_texto'])
    );
    
    // Primera imagen (única)
    $output .= sprintf(
        '<img src="%s" alt="" role="presentation" aria-hidden="true" style="width: auto; height: 0.8rem; flex-shrink: 0;">',
        esc_url($cfg['primera_imagen'])
    );
    
    // Texto del label
    $output .= sprintf(
        '<p aria-hidden="true" class="label-text" style="
            margin: 0;
            font-size: 0.6rem;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
            min-width: 30px;
            flex-shrink: 1;
            flex-grow: 0;
            color: inherit;
            padding-right: 5px;
        ">%s</p>',
        esc_html($texto_badge)
    );
    
    $output .= '</div>';
    
    return $output;
}

/**
 * Obtiene el tipo de envío de un producto
 */
function obtener_tipo_envio_producto($product_id) {
    $shipping_type = get_post_meta($product_id, '_shipping_type', true);
    
    if ($shipping_type === 'recogida') {
        return 'recogida';
    } elseif ($shipping_type === 'gratis-provincia') {
        return 'gratis-provincia';
    } else {
        return 'gratis';
    }
}

/**
 * Obtiene la provincia de la tienda del vendedor (para badges de recogida)
 */
function obtener_provincia_tienda($product_id) {
    // Obtener el vendedor del producto
    $vendor_id = get_post_field('post_author', $product_id);
    
    if (empty($vendor_id)) {
        return '';
    }
    
    // Intentar obtener la provincia del vendedor desde diferentes fuentes
    
    // 1. Desde los metadatos del usuario
    $provincia = get_user_meta($vendor_id, 'dokan_store_address', true);
    if (!empty($provincia) && is_array($provincia) && isset($provincia['state'])) {
        return $provincia['state'];
    }
    
    // 2. Desde la dirección de la tienda Dokan
    if (function_exists('dokan_get_store_info')) {
        $store_info = dokan_get_store_info($vendor_id);
        if (!empty($store_info['address']['state'])) {
            return $store_info['address']['state'];
        }
    }
    
    // 3. Desde la dirección de envío predeterminada
    $customer = new WC_Customer($vendor_id);
    if ($customer) {
        $state = $customer->get_shipping_state();
        if (!empty($state)) {
            return $state;
        }
    }
    
    // Si no se encuentra provincia, devolver 'Local'
    return 'Local';
}

/**
 * Función helper para usar en templates PHP
 */
function mostrar_badge_envio($product_id = null, $tipo = '') {
    $atts = array();
    if ($product_id) {
        $atts['producto_id'] = $product_id;
    }
    if ($tipo) {
        $atts['tipo'] = $tipo;
    }
    echo badge_envio_shortcode($atts);
}

/**
 * Estilos CSS adicionales para los badges
 */
function badge_envio_styles() {
    ?>
    <style>    
    /* Ajustes responsive */
    @media (max-width: 768px) {
        .badge-envio-shortcode img {
            height: 0.7rem !important;
        }
        
        .badge-envio-shortcode .label-text {
            font-size: 0.55rem !important;
            max-width: 100px !important;
        }
    }
    </style>
    <?php
}
add_action('wp_head', 'badge_envio_styles');