<?php 
// ============================================
// FUNCIÓN AUXILIAR PARA OBTENER DIRECCIÓN DE TIENDA DOKAN
// ============================================

/**
 * Obtiene la dirección completa de una tienda Dokan de forma optimizada
 * 
 * @param int $vendor_id ID del vendedor (usuario de WordPress)
 * @return array|false Array con los datos de la tienda o false si no hay vendor_id
 */
function obtener_direccion_dokan_checkout($vendor_id) {
    if (!$vendor_id) return false;
    
    // 1. Obtener configuración principal del perfil
    $store_settings = get_user_meta($vendor_id, 'dokan_profile_settings', true);
    
    // Si no hay configuración, usar array vacío
    if (!is_array($store_settings)) {
        $store_settings = array();
    }
    
    // 2. Extraer dirección principal
    $address = array(
        'street_1' => '',
        'street_2' => '',
        'city'     => '',
        'zip'      => '',
        'state'    => '',
        'country'  => ''
    );
    
    // Fuente A: profile_settings['address']
    if (isset($store_settings['address']) && is_array($store_settings['address'])) {
        $address = wp_parse_args($store_settings['address'], $address);
    }
    
    // Fuente B: dokan_get_store_info() (método estándar de Dokan)
    if (function_exists('dokan_get_store_info') && (empty($address['street_1']) && empty($address['city']))) {
        $store_info = dokan_get_store_info($vendor_id);
        if (!empty($store_info['address']) && is_array($store_info['address'])) {
            $address = wp_parse_args($store_info['address'], $address);
        }
    }
    
    // Fuente C: campos individuales del usuario (dokan_address_*)
    if (empty($address['street_1']) && empty($address['city'])) {
        $address['street_1'] = get_user_meta($vendor_id, 'dokan_address_street_1', true);
        $address['street_2'] = get_user_meta($vendor_id, 'dokan_address_street_2', true);
        $address['city']     = get_user_meta($vendor_id, 'dokan_address_city', true);
        $address['zip']      = get_user_meta($vendor_id, 'dokan_address_zip', true);
        $address['state']    = get_user_meta($vendor_id, 'dokan_address_state', true);
        $address['country']  = get_user_meta($vendor_id, 'dokan_address_country', true);
    }
    
    // 3. Obtener nombre de la tienda
    $store_name = get_user_meta($vendor_id, 'dokan_store_name', true);
    if (empty($store_name)) {
        $store_name = 'Tienda del Vendedor';
    }
    
    // 4. Obtener contacto
    $phone = get_user_meta($vendor_id, 'dokan_store_phone', true);
    $email = get_user_meta($vendor_id, 'dokan_store_email', true);
    
    // Si no hay teléfono en store_phone, buscar en profile_settings
    if (empty($phone) && isset($store_settings['phone'])) {
        $phone = $store_settings['phone'];
    }
    
    // 5. Convertir código de país a nombre
    $country_name = '';
    if (!empty($address['country'])) {
        $countries = WC()->countries ? WC()->countries->get_countries() : array();
        if (!empty($countries[$address['country']])) {
            $country_name = $countries[$address['country']];
        } else {
            $country_name = $address['country'];
        }
    }
    
    // 6. Verificar si tiene dirección válida
    $has_address = !empty($address['street_1']) || !empty($address['city']);
    
    // 7. Construir HTML de dirección - FORMATO COMPACTO
    $html_direccion = '';
    
    // País (si existe)
    if (!empty($country_name)) {
        $html_direccion .= '<div style="margin-bottom: 5px; font-size: 13px; display: flex; align-items: center;">';
        $html_direccion .= '<img src="https://islabeya.com/wp-content/uploads/2025/12/Globus.svg" style="width: 16px; height: 16px; margin-right: 8px; flex-shrink: 0;" alt="País">';
        $html_direccion .= '<span style="font-weight: 600; color: #333; min-width: 45px;">País:</span>';
        $html_direccion .= '<span style="margin-left: 5px;">' . esc_html($country_name) . '</span>';
        $html_direccion .= '</div>';
    }
    
    // Estado (si existe)
    if (!empty($address['state'])) {
        $html_direccion .= '<div style="margin-bottom: 5px; font-size: 13px; display: flex; align-items: center;">';
        $html_direccion .= '<img src="https://islabeya.com/wp-content/uploads/2025/12/Buildings-4.svg" style="width: 16px; height: 16px; margin-right: 8px; flex-shrink: 0;" alt="Estado">';
        $html_direccion .= '<span style="font-weight: 600; color: #333; min-width: 55px;">Estado:</span>';
        $html_direccion .= '<span style="margin-left: 5px;">' . esc_html($address['state']) . '</span>';
        $html_direccion .= '</div>';
    }
    
    // Ciudad y Código Postal (en línea)
    if (!empty($address['city'])) {
        $html_direccion .= '<div style="margin-bottom: 5px; font-size: 13px; display: flex; align-items: center;">';
        $html_direccion .= '<img src="https://islabeya.com/wp-content/uploads/2025/12/City.svg" style="width: 16px; height: 16px; margin-right: 8px; flex-shrink: 0;" alt="Ciudad">';
        $html_direccion .= '<span style="font-weight: 600; color: #333; min-width: 55px;">Ciudad:</span>';
        
        $city_display = esc_html($address['city']);
        if (!empty($address['zip'])) {
            $city_display .= ' &nbsp;&nbsp; <span style="font-weight: 600; color: #333;">C.P.:</span> ' . esc_html($address['zip']);
        }
        
        $html_direccion .= '<span style="margin-left: 5px;">' . $city_display . '</span>';
        $html_direccion .= '</div>';
    } elseif (!empty($address['zip'])) {
        // Si solo hay C.P.
        $html_direccion .= '<div style="margin-bottom: 5px; font-size: 13px; display: flex; align-items: center;">';
        $html_direccion .= '<img src="https://islabeya.com/wp-content/uploads/2025/12/Box-Minimalistic.svg" style="width: 16px; height: 16px; margin-right: 8px; flex-shrink: 0;" alt="CP">';
        $html_direccion .= '<span style="font-weight: 600; color: #333; min-width: 30px;">C.P.:</span>';
        $html_direccion .= '<span style="margin-left: 5px;">' . esc_html($address['zip']) . '</span>';
        $html_direccion .= '</div>';
    }
    
    // Calle principal (si existe)
    if (!empty($address['street_1'])) {
        $html_direccion .= '<div style="margin-bottom: 5px; font-size: 13px; display: flex; align-items: center;">';
        $html_direccion .= '<img src="https://islabeya.com/wp-content/uploads/2025/12/Point-On-Map.svg" style="width: 16px; height: 16px; margin-right: 8px; flex-shrink: 0;" alt="Calle">';
        $html_direccion .= '<span style="font-weight: 600; color: #333; min-width: 45px;">Calle:</span>';
        $html_direccion .= '<span style="margin-left: 5px;">' . esc_html($address['street_1']) . '</span>';
        $html_direccion .= '</div>';
    }
    
    // No.Casa (antes Calle 2) - si existe
    if (!empty($address['street_2'])) {
        $html_direccion .= '<div style="margin-bottom: 5px; font-size: 13px; display: flex; align-items: center;">';
        $html_direccion .= '<img src="https://islabeya.com/wp-content/uploads/2025/12/Point-On-Map.svg" style="width: 16px; height: 16px; margin-right: 8px; flex-shrink: 0;" alt="No.Casa">';
        $html_direccion .= '<span style="font-weight: 600; color: #333; min-width: 70px;">No.Casa:</span>';
        $html_direccion .= '<span style="margin-left: 5px;">' . esc_html($address['street_2']) . '</span>';
        $html_direccion .= '</div>';
    }
    
    // 8. Retornar todos los datos
    return array(
        'store_name' => $store_name,
        'phone' => $phone,
        'email' => $email,
        'address_data' => $address,
        'country_name' => $country_name,
        'html_direccion' => $html_direccion,
        'has_address' => $has_address
    );
}