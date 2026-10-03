<?php
/**
 * SHORTCODE PARA BADGES DE ENVÍO - VERSIÓN PRODUCTO COMPLETA
 * Nombre: [badge_envio_producto]
 * 
 * Modificado para integrar con el selector de ubicación
 * MODIFICACIÓN: Badge de combo ahora se muestra para productos de la tienda "Comboxpress"
 * MODIFICACIÓN V2: Para productos de la categoría "motos" se muestra información de Aerovaradero
 */

// 1. Función auxiliar para calcular la fecha con Mes abreviado
function obtener_rango_fecha_estimada() {
    $timezone = new DateTimeZone(get_option('timezone_string') ?: 'UTC');
    $fecha_hoy = new DateTime('now', $timezone);
    $fecha_futura = clone $fecha_hoy;
    $fecha_futura->modify('+50 days');
    $hoy_formateado = date_i18n('j M', $fecha_hoy->getTimestamp());
    $futura_formateada = date_i18n('j M', $fecha_futura->getTimestamp());
    return $hoy_formateado . ' - ' . $futura_formateada;
}

// 2. Función auxiliar para obtener info de tienda
function obtener_info_tienda_completa($product_id) {
    $vendor_id = get_post_field('post_author', $product_id);
    if (empty($vendor_id)) return array('nombre' => 'Tienda Local', 'direccion' => 'Dirección a convenir');
    $nombre_tienda = get_the_author_meta('display_name', $vendor_id);
    $direccion = '';
    if (function_exists('dokan_get_store_info')) {
        $store_info = dokan_get_store_info($vendor_id);
        if (!empty($store_info['store_name'])) $nombre_tienda = $store_info['store_name'];
        $address_parts = array();
        if (!empty($store_info['address']['street_1'])) $address_parts[] = $store_info['address']['street_1'];
        if (!empty($store_info['address']['city'])) $address_parts[] = $store_info['address']['city'];
        if (!empty($store_info['address']['state'])) $address_parts[] = $store_info['address']['state'];
        $direccion = implode(', ', $address_parts);
    } 
    if (empty($direccion)) $direccion = obtener_provincia_tienda($product_id); 
    return array('nombre' => $nombre_tienda, 'direccion' => !empty($direccion) ? $direccion : 'Dirección no disponible');
}

// NUEVA FUNCIÓN: Verificar si el producto pertenece a la tienda Comboxpress
function es_tienda_comboxpress($product_id) {
    $vendor_id = get_post_field('post_author', $product_id);
    if (empty($vendor_id)) return false;
    
    // Opción 1: Verificar por el nombre exacto de la tienda
    if (function_exists('dokan_get_store_info')) {
        $store_info = dokan_get_store_info($vendor_id);
        $store_name = isset($store_info['store_name']) ? $store_info['store_name'] : '';
        if (strtolower(trim($store_name)) === 'comboxpress') {
            return true;
        }
    }
    
    // Opción 2: Verificar por el display_name del autor
    $display_name = get_the_author_meta('display_name', $vendor_id);
    if (strtolower(trim($display_name)) === 'comboxpress') {
        return true;
    }
    
    return false;
}

// 3. Función para determinar el tipo de badge según ubicación (MODIFICADA)
function obtener_tipo_badge_por_ubicacion($product_id, $shipping_type_original) {
    // Verificar si es gratis-provincia
    if ($shipping_type_original === 'gratis-provincia') {
        // Obtener provincias permitidas del producto usando taxonomía nativa
        $provincias_terms = wp_get_post_terms($product_id, 'provincia');
        $provincias_permitidas = array();
        
        if (!empty($provincias_terms) && !is_wp_error($provincias_terms)) {
            foreach ($provincias_terms as $term) {
                $provincias_permitidas[] = $term->term_id;
            }
        }
        
        // Obtener provincia seleccionada por el usuario
        $ubicacion_provincia = isset($_SESSION['prov_id']) ? intval($_SESSION['prov_id']) : 0;
        
        if (!empty($provincias_permitidas) && $ubicacion_provincia !== 0) {
            // Verificar si la provincia seleccionada está permitida
            if (in_array($ubicacion_provincia, $provincias_permitidas)) {
                // Obtener nombre de la provincia
                $province_term = get_term($ubicacion_provincia, 'provincia');
                if ($province_term && !is_wp_error($province_term)) {
                    return array(
                        'type' => 'gratis_provincia',
                        'province_name' => $province_term->name
                    );
                }
            }
        }
        
        // Si no hay provincia seleccionada o no coincide, mostrar la primera provincia permitida
        if (!empty($provincias_terms) && !is_wp_error($provincias_terms)) {
            $first_province = $provincias_terms[0];
            return array(
                'type' => 'gratis_provincia',
                'province_name' => $first_province->name
            );
        }
        
        return array('type' => 'gratis_provincia', 'province_name' => 'Provincia específica');
    }
    
    // Verificar si hay ubicación seleccionada
    $ubicacion_provincia = isset($_SESSION['province']) ? intval($_SESSION['province']) : 0;
    
    // Detectar si es una moto
    $es_moto = has_term('motos', 'product_cat', $product_id);
    
    // ==============================================
    // LÓGICA PARA COMBOXPRESS
    // ==============================================
    $es_tienda_comboxpress = es_tienda_comboxpress($product_id);
    
    if ($es_tienda_comboxpress) {
        if ($ubicacion_provincia !== 0) {
            $provincia_seleccionada_nombre = '';
            $location_term = get_term($ubicacion_provincia, 'location');
            if ($location_term && !is_wp_error($location_term)) {
                $provincia_seleccionada_nombre = $location_term->name;
            }
            
            $provincia_normalizada = strtolower(trim($provincia_seleccionada_nombre));
            $es_villa_clara = ($provincia_normalizada === 'villa clara');
            
            if ($es_villa_clara) {
                return 'gratis_ubicacion';
            } else {
                return 'recogida_combo';
            }
        }
        return 'recogida_combo';
    }
    
    // ==============================================
    // LÓGICA PARA MOTOS (con información de Aerovaradero)
    // ==============================================
    if ($es_moto) {
        $provincia_vendedor = obtener_provincia_tienda($product_id);
        
        $provincia_seleccionada_nombre = '';
        if ($ubicacion_provincia !== 0) {
            $location_term = get_term($ubicacion_provincia, 'location');
            if ($location_term && !is_wp_error($location_term)) {
                $provincia_seleccionada_nombre = $location_term->name;
            }
        }
        
        $coinciden = false;
        if (!empty($provincia_vendedor) && !empty($provincia_seleccionada_nombre)) {
            $coinciden = (strtolower(trim($provincia_vendedor)) === strtolower(trim($provincia_seleccionada_nombre)));
        }
        
        if ($ubicacion_provincia !== 0) {
            if ($coinciden) {
                return 'moto_gratis_aerovaradero';
            } else {
                return 'moto_punto_recogida';
            }
        }
        return 'moto_gratis_aerovaradero';
    }
    
    // ==============================================
    // LÓGICA PARA OTROS PRODUCTOS
    // ==============================================
    $provincia_vendedor = obtener_provincia_tienda($product_id);
    
    $provincia_seleccionada_nombre = '';
    if ($ubicacion_provincia !== 0) {
        $location_term = get_term($ubicacion_provincia, 'location');
        if ($location_term && !is_wp_error($location_term)) {
            $provincia_seleccionada_nombre = $location_term->name;
        }
    }
    
    $coinciden = false;
    if (!empty($provincia_vendedor) && !empty($provincia_seleccionada_nombre)) {
        $coinciden = (strtolower(trim($provincia_vendedor)) === strtolower(trim($provincia_seleccionada_nombre)));
    }
    
    if ($ubicacion_provincia !== 0) {
        if ($coinciden) {
            return 'gratis_ubicacion';
        } else {
            return 'recogida';
        }
    }
    
    return 'gratis';
}

// 4. Función principal del Shortcode
function badge_envio_producto_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'producto_id' => '',
            'tipo'        => '', 
            'clase'       => '',
        ),
        $atts,
        'badge_envio_producto'
    );
    
    $product_id = !empty($atts['producto_id']) ? intval($atts['producto_id']) : 0;
    if (empty($product_id)) {
        global $product;
        $product_id = is_a($product, 'WC_Product') ? $product->get_id() : get_the_ID();
    }
    
    if (empty($product_id) || !is_numeric($product_id)) return '';
    
    $shipping_type_original = !empty($atts['tipo']) ? $atts['tipo'] : obtener_tipo_envio_producto($product_id);
    $badge_result = obtener_tipo_badge_por_ubicacion($product_id, $shipping_type_original);
    
    // Manejar el caso cuando badge_result es un array (gratis-provincia)
    if (is_array($badge_result)) {
        $badge_type = $badge_result['type'];
        $province_name = isset($badge_result['province_name']) ? $badge_result['province_name'] : '';
    } else {
        $badge_type = $badge_result;
        $province_name = '';
    }
    
    // Configuración de badges
    $config = array(
        'gratis' => array(
            'primera_imagen' => esc_url( get_parent_theme_file_uri( '/assets/img/Envio.svg' ) ),
            'segunda_imagen' => esc_url( get_parent_theme_file_uri( '/assets/img/Delivery.svg' ) ),
            'texto' => 'envío gratis', 
            'color_fondo' => 'rgba(212, 230, 181, 0.25)',
            'color_texto' => '#2C4A3E',
        ),
        'gratis_ubicacion' => array(
            'primera_imagen' => esc_url( get_parent_theme_file_uri( '/assets/img/Envio.svg' ) ),
            'segunda_imagen' => esc_url( get_parent_theme_file_uri( '/assets/img/Delivery.svg' ) ),
            'texto' => 'envío gratis',
            'color_fondo' => 'rgba(212, 230, 181, 0.25)',
            'color_texto' => '#2C4A3E',
        ),
        'punto_recogida' => array(
            'primera_imagen' => esc_url( get_parent_theme_file_uri( '/assets/img/Envio.svg' ) ),
            'segunda_imagen' => esc_url( get_parent_theme_file_uri( '/assets/img/Delivery.svg' ) ),
            'texto' => 'Punto de recogida',
            'color_fondo' => 'rgba(212, 230, 181, 0.25)',
            'color_texto' => '#2C4A3E',
        ),
        'recogida' => array(
            'primera_imagen' => esc_url( get_parent_theme_file_uri( '/assets/img/Recogida.svg' ) ),
            'segunda_imagen' => esc_url( get_parent_theme_file_uri( '/assets/img/Box.svg' ) ),
            'texto' => obtener_provincia_tienda($product_id),
            'color_fondo' => 'rgba(255, 216, 181, 0.25)',
            'color_texto' => '#B84A2C',
        ),
        'recogida_combo' => array(
            'primera_imagen' => esc_url( get_parent_theme_file_uri( '/assets/img/Recogida.svg' ) ),
            'segunda_imagen' => esc_url( get_parent_theme_file_uri( '/assets/img/Box.svg' ) ),
            'texto' => 'Recogida en local',
            'color_fondo' => 'rgba(255, 216, 181, 0.25)',
            'color_texto' => '#B84A2C',
        ),
        // NUEVOS BADGES PARA MOTOS
        'moto_gratis_aerovaradero' => array(
            'primera_imagen' => esc_url( get_parent_theme_file_uri( '/assets/img/Envio.svg' ) ),
            'segunda_imagen' => esc_url( get_parent_theme_file_uri( '/assets/img/Delivery.svg' ) ),
            'texto' => 'envío gratis',
            'color_fondo' => 'rgba(212, 230, 181, 0.25)',
            'color_texto' => '#2C4A3E',
        ),
        'moto_punto_recogida' => array(
            'primera_imagen' => esc_url( get_parent_theme_file_uri( '/assets/img/Envio.svg' ) ),
            'segunda_imagen' => esc_url( get_parent_theme_file_uri( '/assets/img/Delivery.svg' ) ),
            'texto' => 'Punto de recogida',
            'color_fondo' => 'rgba(212, 230, 181, 0.25)',
            'color_texto' => '#2C4A3E',
        ),
        // NUEVO BADGE PARA GRATIS-PROVINCIA
        'gratis_provincia' => array(
            'primera_imagen' => esc_url( get_parent_theme_file_uri( '/assets/img/Gratis provincia.svg' ) ),
            'segunda_imagen' => esc_url( get_parent_theme_file_uri( '/assets/img/Delivery.svg' ) ),
            'texto' => 'envío gratis', // Se modificará dinámicamente con el nombre de la provincia
            'color_fondo' => 'rgba(155, 89, 182, 0.25)',
            'color_texto' => '#6c3483',
        )
    );
    
    if (!isset($config[$badge_type])) return '';
    $cfg = $config[$badge_type];

    $output = '<div class="wrapper-badge-producto" style="font-family: inherit;">';
    
    // Badge Principal
    $badge_text = $cfg['texto'];
    // Si es gratis-provincia, mostrar solo el nombre de la provincia
    if ($badge_type === 'gratis_provincia' && !empty($province_name)) {
        $badge_text = $province_name;
    }
    
    $output .= sprintf(
        '<div class="badge-envio-shortcode badge-envio-%s %s" style="--font-color: %s; --background-color: %s; display: inline-flex; align-items: center; gap: 4px; border-radius: 5px; background-color: %s; color: %s; padding: 0px;">',
        $badge_type, esc_attr($atts['clase']), $cfg['color_texto'], $cfg['color_fondo'], $cfg['color_fondo'], $cfg['color_texto']
    );
    
    $output .= sprintf('<img src="%s" style="width: auto; height: 1.2rem;" alt="Tipo de envio del producto">', esc_url($cfg['primera_imagen']));
    $output .= sprintf('<img src="%s" style="width: auto; height: 1rem;" alt="Tipo de envio del producto">', esc_url($cfg['segunda_imagen']));
    $output .= sprintf('<p style="margin: 0; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">%s</p>', esc_html($badge_text));
    $output .= '</div>';

    // BLOQUE: Envío Gratis por Provincia
    if ($badge_type === 'gratis_provincia') {
        $fecha_rango = obtener_rango_fecha_estimada();
        $output .= sprintf(
            '<div class="entrega-estimada-container gratis-provincia-info" style="padding-left: 12px; border-left: 2px solid #9b59b6; margin-top: 5px;">
                <p style="margin: 0; font-size: 0.8rem; color: #6c3483; line-height: 1.4;">
                    <strong>Entrega estimada:</strong> %s
                </p>
                <p style="margin: 5px 0 0 0; font-size: 0.8rem; color: #6c3483; line-height: 1.4;">
                    <strong>Envío gratis y entrega solo a %s</strong>
                </p>
                <p style="margin: 5px 0 0 0; font-size: 0.75rem; color: #666; line-height: 1.4;">
                    Este producto solo se envía y se entrega a su domicilio en %s. A otras provincias no se hace entrega.
                </p>
            </div>',
            $fecha_rango,
            esc_html($province_name),
            esc_html($province_name)
        );
    }
    
    // BLOQUE: Envío Gratis (productos normales)
    if (in_array($badge_type, array('gratis', 'gratis_ubicacion'))) {
        $fecha_rango = obtener_rango_fecha_estimada();
        $output .= sprintf(
            '<div class="entrega-estimada-container" style="padding-left: 12px; border-left: 2px solid #D4E6B5; margin-top: 5px;">
                <p style="margin: 0; font-size: 0.8rem; color: #2C4A3E; line-height: 1.4;">
                    <strong>Entrega estimada:</strong> %s
                </p>
                <p style="margin: 0; font-size: 0.8rem; color: #777; font-style: italic;">
                    <span style="color: #15ad3c;">81,73%%</span> son en menos de este rango de días
                </p>
            </div>',
            $fecha_rango
        );
    }
    
    // BLOQUE: Envío para Motos con Aerovaradero
    elseif ($badge_type === 'moto_gratis_aerovaradero') {
        $fecha_rango = obtener_rango_fecha_estimada();
        $provincia_seleccionada = '';
        if (isset($_SESSION['province']) && intval($_SESSION['province']) !== 0) {
            $location_term = get_term(intval($_SESSION['province']), 'location');
            if ($location_term && !is_wp_error($location_term)) {
                $provincia_seleccionada = $location_term->name;
            }
        }
        
        $output .= sprintf(
            '<div class="entrega-estimada-container moto-aerovaradero" style="padding-left: 12px; border-left: 2px solid #D4E6B5; margin-top: 5px;">
                <p style="margin: 0; font-size: 0.8rem; color: #2C4A3E; line-height: 1.4;">
                    <strong>Entrega estimada:</strong> %s
                </p>
                <p style="margin: 5px 0 0 0; font-size: 0.75rem; color: #2C4A3E; line-height: 1.4;">
                    <strong>Envío a través de Aerovaradero S.A.</strong> 
                </p>
                <p style="margin: 5px 0 0 0; font-size: 0.7rem; color: #666; font-style: italic;">
                    Empresa cubana especializada en carga aérea con más de 32 años de experiencia, certificada ISO 9001:2015.
                </p>
                <p style="margin: 5px 0 0 0; font-size: 0.8rem; color: #B84A2C;">
                    <strong>Importante:</strong> Las motos se entregan ÚNICAMENTE en municipios cabecera de provincia.
                </p>
            </div>',
            $fecha_rango,
            esc_html($provincia_seleccionada)
        );
    }
    
    // BLOQUE: Punto de recogida para motos (sin cobertura Aerovaradero)
    elseif ($badge_type === 'moto_punto_recogida') {
        $info_tienda = obtener_info_tienda_completa($product_id);
        $output .= sprintf(
            '<div class="recogida-info-container" style="padding-left: 12px; border-left: 2px solid #FFD8B5; margin-top: 5px;">
                <p style="margin: 0; font-size: 0.8rem; color: #B84A2C; line-height: 1.4;">
                    <strong>📦 Recoger en tienda</strong>
                </p>
                <p style="margin: 5px 0 0 0; font-size: 0.75rem; color: #B84A2C;">
                    ⚠️ <strong>Aerovaradero no tiene cobertura en esta provincia.</strong> Las motos solo se envían a través de Aerovaradero a municipios cabecera de provincia.
                </p>
                <p style="margin: 5px 0 0 0; font-size: 0.7rem; color: #666;">
                    📍 Provincias con cobertura: La Habana, Varadero, Camagüey, Villa Clara, Holguín y Santiago de Cuba.
                </p>
                <p style="margin: 8px 0 0 0; font-size: 0.7rem; color: #B84A2C; font-weight: 500;">
                    📍 <strong>Recoger en:</strong> %s - %s
                </p>
                <p style="margin: 5px 0 0 0; font-size: 0.7rem; color: #B84A2C; font-weight: 500;">
                    ⚠️ Importante: Presentar identificación y comprobante de compra.
                </p>
            </div>',
            esc_html($info_tienda['nombre']),
            esc_html($info_tienda['direccion'])
        );
    }
    
    // BLOQUE: Recogida en Local (productos normales)
    elseif ($badge_type === 'recogida') {
        $info_tienda = obtener_info_tienda_completa($product_id);
        $output .= sprintf(
            '<div class="recogida-info-container" style="padding-left: 12px; border-left: 2px solid #FFD8B5; margin-top: 5px;">
                <p style="margin: 0; font-size: 0.8rem; color: #B84A2C; line-height: 1.4;">
                    <strong>Recoger en:</strong> %s
                </p>
                <p style="margin: 0; font-size: 0.8rem; color: #777; font-style: italic;">
                    %s
                </p>
                <p style="margin: 5px 0 0 0; font-size: 0.7rem; color: #B84A2C; font-weight: 500;">
                    ⚠️ Importante: Presentar identificación y comprobante de compra.
                </p>
            </div>',
            esc_html($info_tienda['nombre']),
            esc_html($info_tienda['direccion'])
        );
    }
    
    // BLOQUE: Recogida Comboxpress (productos de Comboxpress fuera de Villa Clara)
    elseif ($badge_type === 'recogida_combo') {
        $info_tienda = obtener_info_tienda_completa($product_id);
        $output .= sprintf(
            '<div class="recogida-info-container" style="padding-left: 12px; border-left: 2px solid #FFD8B5; margin-top: 5px;">
                <p style="margin: 0; font-size: 0.7rem; color: #B84A2C; line-height: 1.4;">
                    ⚠️ <strong>Disponible solo con envío gratis para VILLA CLARA</strong> — Para otras provincias, recoger en tienda física ubicada en Villa Clara.
                </p>
                <p style="margin: 5px 0 0 0; font-size: 0.7rem; color: #B84A2C; font-weight: 500;">
                    📍 <strong>Recoger en:</strong> %s - %s
                </p>
                <p style="margin: 5px 0 0 0; font-size: 0.7rem; color: #B84A2C; font-weight: 500;">
                    ⚠️ Importante: Presentar identificación y comprobante de compra.
                </p>
            </div>',
            esc_html($info_tienda['nombre']),
            esc_html($info_tienda['direccion'])
        );
    }

    $output .= '</div>';
    
    return $output;
}
add_shortcode('badge_envio_producto', 'badge_envio_producto_shortcode');