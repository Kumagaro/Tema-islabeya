<?php 
/**
 * temaislabeya's functions and definitions
 *
 * @package temaislabeya
 * @since temaislabeya 1.0
 */
add_action('init', function() {
    if (!session_id() && !headers_sent()) {
        session_start();
    }
}, 1);

if ( ! isset( $content_width ) ) {
    $content_width = 1280;
}

if ( ! function_exists( 'temaislabeya_setup' ) ) :
    function temaislabeya_setup() {
        load_theme_textdomain( 'temaislabeya', get_template_directory() . '/languages' );
        add_theme_support( 'automatic-feed-links' );
        add_theme_support( 'post-thumbnails' );
        register_nav_menus( array(
            'primary'   => __( 'Primary Menu', 'temaislabeya' ),
            'secondary' => __( 'Secondary Menu', 'temaislabeya' ),
        ) );
        add_theme_support( 'post-formats', array( 'aside', 'gallery', 'quote', 'image', 'video' ) );
    }
endif;
add_action( 'after_setup_theme', 'temaislabeya_setup' );

// Definir constante para la ruta del tema
define('THEME_DIR', get_template_directory());

// Incluir todos los archivos de la carpeta inc
foreach (glob(THEME_DIR . '/inc/*.php') as $file) {
    require_once $file;
}

// Preloader assets (CSS/JS)
add_action( 'wp_enqueue_scripts', function() {
    $enabled = get_theme_mod( 'islabeya_preloader_enable', true );
    if ( ! $enabled ) {
        return;
    }

    wp_enqueue_style(
        'islabeya-preloader',
        get_template_directory_uri() . '/assets/css/preloader.css',
        array(),
        '1.0'
    );

    $image_id = (int) get_theme_mod( 'islabeya_preloader_image_id', 0 );
    $image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';

    wp_enqueue_script(
        'islabeya-preloader',
        get_template_directory_uri() . '/assets/js/preloader.js',
        array(),
        '1.0',
        true
    );

    wp_localize_script( 'islabeya-preloader', 'islabeyaPreloader', array(
        'enabled' => true,
        'bg'      => get_theme_mod( 'islabeya_preloader_bg', '#ffffff' ),
        'image'   => esc_url_raw( $image_url ),
        'fallbackMs' => 2500,
    ) );

}, 20 );



/**
 * Compatibilidad con WooCommerce
 */
function temaislabeya_add_woocommerce_support() {
    add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'temaislabeya_add_woocommerce_support' );

/**
 * Hooks de productos
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

add_action('woocommerce_before_main_content', 'temaislabeya_wrapper_start', 10);
add_action('woocommerce_after_main_content', 'temaislabeya_wrapper_end', 10);

function temaislabeya_wrapper_start() {
    echo '<section class="productos-frontend">';
}
function temaislabeya_wrapper_end() {
    echo '</section>';
}

/**
 * Configuraciones para imagenes de woocommerce
 */
add_theme_support( 'woocommerce', array(
    'thumbnail_image_width'         => 200,  // Miniatura del catálogo (grids de tienda)
    'gallery_thumbnail_image_width' => 100,  // Miniaturas de la galería (debajo de imagen principal)
    'single_image_width'            => 600,  // Imagen grande en la página del producto
) );

/**
 * Función auxiliar para formatear precio con partes separadas
 */
function islabeya_formatear_precio_con_partes( $precio ) {
    $precio_str = number_format( $precio, 2, ',', '.' );
    $partes = explode( ',', $precio_str );
    
    $parte_entera = $partes[0];
    $decimales = $partes[1];
    
    $partes_entera = explode( '.', $parte_entera );
    $miles = isset( $partes_entera[0] ) ? $partes_entera[0] : '';
    $cientos = isset( $partes_entera[1] ) ? $partes_entera[1] : '';
    
    $html = '<span class="precio-miles">€' . esc_html( $miles ) . '</span>';
    if ( $cientos ) {
        $html .= '<span class="precio-punto">.</span>';
        $html .= '<span class="precio-cientos">' . esc_html( $cientos ) . '</span>';
    }
    $html .= '<span class="precio-decimal">,' . esc_html( $decimales ) . '</span>';
    
    return $html;
}

/**
 * Cambiar el número de productos relacionados mostrados
 */
function islabeya_related_products_args( $args ) {
    $args['posts_per_page'] = 10;
    $args['columns']        = 4;
    return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'islabeya_related_products_args', 20 );

/**
 * Activa las funcionalidades de la galería de productos en WooCommerce
 */
function islabeya_activar_galeria_productos() {
    add_theme_support( 'wc-product-gallery-lightbox' );
}
add_action( 'after_setup_theme', 'islabeya_activar_galeria_productos' );

/**
 * Convierte nombre de color a hexadecimal
 */
function islabeya_get_color_hex( $color_name ) {
    $colors = array(
        'amarillo'  => '#FFFF00',
        'azul'      => '#0000FF',
        'beige'     => '#F5F5DC',
        'naranja'   => '#FFA500',
        'negro'     => '#000000',
        'rojo'      => '#FF0000',
        'rosa'      => '#FFC0CB',
        'verde'     => '#00FF00',
        'verde claro' => '#90EE90',
        'verde oscuro' => '#006400',
        'gris'      => '#808080',
        'blanco'    => '#FFFFFF',
        'morado'    => '#800080',
        'carmelita'      => '#8B4513',
        'dorado'    => '#FFD700',
        'plateado'  => '#C0C0C0',
        'carmelita-oscuro'  => '#3e0000', 
    );
    
    $color_name_lower = strtolower( trim( $color_name ) );
    return isset( $colors[ $color_name_lower ] ) ? $colors[ $color_name_lower ] : false;
}

/**
 * Eliminar las pestañas de productos para mostrarlas por separado
 */
add_filter( 'woocommerce_product_tabs', 'islabeya_eliminar_tabs_producto', 98 );

function islabeya_eliminar_tabs_producto( $tabs ) {
    unset( $tabs['description'] );
    unset( $tabs['additional_information'] );
    unset( $tabs['reviews'] );
    return $tabs;
}

/**
 * Muestra la descripción del producto
 */
function islabeya_mostrar_descripcion_producto() {
    global $product;
    if ( ! $product->get_description() ) return;
    echo '<div class="islabeya-descripcion-seccion"><div class="product-description">';
    echo apply_filters( 'the_content', $product->get_description() );
    echo '</div></div>';
}

/**
 * Muestra las valoraciones del producto
 */
function islabeya_mostrar_valoraciones_producto() {
    global $product;
    if ( ! comments_open() && ! get_comments_number() ) return;
    echo '<div class="islabeya-reviews-seccion">';
    comments_template();
    echo '</div>';
}

/**
 * Cambia el número de productos mostrados por página
 */
add_filter( 'loop_shop_per_page', 'cambiar_productos_por_pagina', 20 );
function cambiar_productos_por_pagina( $productos_por_pagina ) {
    return 20;
} 

/**
 * Desactivar la plantilla responsive de YITH Wishlist
 */
add_filter( 'yith_wcwl_is_wishlist_responsive', '__return_false' );

/**
 * Personalizar la paginación de Dokan
 */
add_filter( 'dokan_pagination_args', 'islabeya_customize_dokan_pagination', 10, 1 );
function islabeya_customize_dokan_pagination( $args ) {
    $args['prev_text'] = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    $args['next_text'] = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    return $args;
}

/**
 * Forzar el uso de nuestra plantilla my-account.php
 */
add_filter( 'template_include', 'forzar_plantilla_my_account', 99 );
function forzar_plantilla_my_account( $template ) {
    if ( is_account_page() && ! is_user_logged_in() ) {
        $new_template = get_stylesheet_directory() . '/woocommerce/myaccount/my-account.php';
        if ( file_exists( $new_template ) ) {
            return $new_template;
        }
    }
    return $template;
}



// ============================================
// CHECKOUT: SIMPLIFICAR CAMPOS Y DEFINIR VALORES POR DEFECTO
// ============================================

/**
 * 0. Ordenar campos de dirección estándar usando woocommerce_default_address_fields
 * Este filtro se ejecuta antes de que las configuraciones locales sobrescriban las priorities,
 * lo que garantiza que el ordenamiento funcione correctamente.
 */
add_filter( 'woocommerce_default_address_fields', 'islabeya_default_address_fields_order', 9999 );
function islabeya_default_address_fields_order( $fields ) {
    // Orden para campos de dirección estándar (aplica a billing y shipping)
    // Orden deseado: Nombre, Apellidos, Dirección, Población, Provincia, Código Postal, País
    $address_fields_order = [
        'first_name'  => 10,
        'last_name'   => 20,
        'address_1'   => 90,
        'city'        => 70,
        'state'       => 60,
        'postcode'    => 80,
        'country'     => 50,
    ];

    foreach ( $address_fields_order as $field_key => $priority ) {
        if ( isset( $fields[ $field_key ] ) ) {
            $fields[ $field_key ]['priority'] = $priority;
        }
    }

    return $fields;
}

/**
 * 1. Simplificar campos de facturación y envío
 * - Mantiene solo los campos "esenciales".
 * - Marca como obligatorios los campos definidos.
 * - Asigna priorities solo para campos personalizados (email, phone, identity_card, house_number).
 * - Incluye funcionalidad de ajuste de required según tipo de envío.
 */
add_filter( 'woocommerce_checkout_fields', 'custom_override_checkout_fields', 9999 );
function custom_override_checkout_fields( $fields ) {
    // --- Campos de facturación (billing) ---
    // Orden: Nombre, Apellidos, Correo, Teléfono, País, Provincia, Población, Código Postal, Dirección, Carnet
    $billing_fields_to_keep = [
        'billing_first_name',
        'billing_last_name',
        'billing_country',
        'billing_state',
        'billing_city',
        'billing_postcode',
        'billing_address_1',
        'billing_phone',
        'billing_email',
        'billing_identity_card'
    ];
    
    // Eliminar todos los campos de facturación excepto los definidos
    foreach ( $fields['billing'] as $field_key => $field ) {
        if ( ! in_array( $field_key, $billing_fields_to_keep ) ) {
            unset( $fields['billing'][$field_key] );
        }
    }
    
    // Asignar priorities solo para campos personalizados (no estándar)
    // Los campos estándar se ordenan en woocommerce_default_address_fields
    $billing_custom_priorities = [
        'billing_phone'          => 25,
        'billing_email'          => 95,
        'billing_identity_card'  => 100,
    ];
    
    foreach ( $billing_custom_priorities as $field_key => $priority ) {
        if ( isset( $fields['billing'][$field_key] ) ) {
            $fields['billing'][$field_key]['priority'] = $priority;
        }
    }
    
    // Marcar campos de facturación como obligatorios
    $required_billing = [
        'billing_first_name',
        'billing_last_name',
        'billing_state',
        'billing_city',
        'billing_address_1',
        'billing_phone',
        'billing_email',
        'billing_identity_card'
    ];
    foreach ( $required_billing as $field_key ) {
        if ( isset( $fields['billing'][$field_key] ) ) {
            $fields['billing'][$field_key]['required'] = true;
        }
    }
    
    // País: select, por defecto CU (editable si se agregan más países)
    if ( isset( $fields['billing']['billing_country'] ) ) {
        $fields['billing']['billing_country']['type']     = 'country';
        $fields['billing']['billing_country']['required'] = true;
        $fields['billing']['billing_country']['class']    = [ 'form-row-wide' ];
        $fields['billing']['billing_country']['default']  = 'CU';
    }
    
    // Agregar atributos data-* para validación global (validaciones.js)
    // Todos los campos tienen data-required para validación por pasos
    if ( isset( $fields['billing']['billing_first_name'] ) ) {
        $fields['billing']['billing_first_name']['custom_attributes'] = [
            'data-required' => 'true',
        ];
    }
    if ( isset( $fields['billing']['billing_last_name'] ) ) {
        $fields['billing']['billing_last_name']['custom_attributes'] = [
            'data-required' => 'true',
        ];
    }
    if ( isset( $fields['billing']['billing_country'] ) ) {
        $fields['billing']['billing_country']['custom_attributes'] = [
            'data-required' => 'true',
        ];
    }
    if ( isset( $fields['billing']['billing_state'] ) ) {
        $fields['billing']['billing_state']['custom_attributes'] = [
            'data-required' => 'true',
        ];
    }
    if ( isset( $fields['billing']['billing_city'] ) ) {
        $fields['billing']['billing_city']['custom_attributes'] = [
            'data-required' => 'true',
        ];
    }
    if ( isset( $fields['billing']['billing_postcode'] ) ) {
        $fields['billing']['billing_postcode']['custom_attributes'] = [
            'data-required' => 'true',
        ];
    }
    if ( isset( $fields['billing']['billing_address_1'] ) ) {
        $fields['billing']['billing_address_1']['custom_attributes'] = [
            'data-required' => 'true',
        ];
    }
    if ( isset( $fields['billing']['billing_email'] ) ) {
        $fields['billing']['billing_email']['custom_attributes'] = [
            'data-required' => 'true',
            'data-email'    => 'true',
        ];
    }
    if ( isset( $fields['billing']['billing_phone'] ) ) {
        $fields['billing']['billing_phone']['custom_attributes'] = [
            'data-required' => 'true',
        ];
    }
    if ( isset( $fields['shipping']['shipping_phone'] ) ) {
        $fields['shipping']['shipping_phone']['custom_attributes'] = [
            'data-required' => 'true',
        ];
    }
    
    // --- Campos de envío (shipping) ---
    // Orden: Nombre, Apellidos, Carnet, Teléfono, País, Provincia, Población, Código Postal, Dirección, Nº Casa/Edificio
    $shipping_fields_to_keep = [
        'shipping_first_name',
        'shipping_last_name',
        'shipping_country',
        'shipping_state',
        'shipping_city',
        'shipping_postcode',
        'shipping_address_1',
        'shipping_house_number',
        'shipping_phone',
        'shipping_identity_card',
    ];
    
    // Eliminar todos los campos de envío excepto los definidos
    foreach ( $fields['shipping'] as $field_key => $field ) {
        if ( ! in_array( $field_key, $shipping_fields_to_keep ) ) {
            unset( $fields['shipping'][$field_key] );
        }
    }
    
    // Asignar priorities solo para campos personalizados (no estándar)
    // Los campos estándar se ordenan en woocommerce_default_address_fields
    $shipping_custom_priorities = [
        'shipping_phone'          => 25,
        'shipping_identity_card'  => 30,
        'shipping_house_number'   => 100,
    ];
    
    foreach ( $shipping_custom_priorities as $field_key => $priority ) {
        if ( isset( $fields['shipping'][$field_key] ) ) {
            $fields['shipping'][$field_key]['priority'] = $priority;
        }
    }
    
    // Ajustar campos required según tipo de envío (funcionalidad unificada de checkout-pasos.php)
    $necesita_envio = function_exists( 'islabeya_carrito_necesita_envio_domicilio' ) ? islabeya_carrito_necesita_envio_domicilio() : true;
    if ( ! $necesita_envio ) {
        // Solo recogida → los campos de envío dejan de ser obligatorios
        if ( ! empty( $fields['shipping'] ) && is_array( $fields['shipping'] ) ) {
            foreach ( $fields['shipping'] as $key => $field ) {
                if ( isset( $fields['shipping'][ $key ]['required'] ) ) {
                    $fields['shipping'][ $key ]['required'] = false;
                }
            }
        }
    } else {
        // Con envío → marcar campos de envío como obligatorios
        $required_shipping = [
            'shipping_first_name',
            'shipping_last_name',
            'shipping_state',
            'shipping_city',
            'shipping_address_1',
            'shipping_house_number',
            'shipping_phone',
            'shipping_identity_card',
        ];
        foreach ( $required_shipping as $field_key ) {
            if ( isset( $fields['shipping'][$field_key] ) ) {
                $fields['shipping'][$field_key]['required'] = true;
            }
        }
    }
    
    // País: select, por defecto CU
    if ( isset( $fields['shipping']['shipping_country'] ) ) {
        $fields['shipping']['shipping_country']['type']     = 'country';
        $fields['shipping']['shipping_country']['required'] = true;
        $fields['shipping']['shipping_country']['class']    = [ 'form-row-wide' ];
        $fields['shipping']['shipping_country']['default']  = 'CU';
    }

    // Agregar atributos data-* para validación global (validaciones.js)
    // Todos los campos tienen data-required para validación por pasos
    if ( isset( $fields['shipping']['shipping_first_name'] ) ) {
        $fields['shipping']['shipping_first_name']['custom_attributes'] = [
            'data-required' => 'true',
        ];
    }
    if ( isset( $fields['shipping']['shipping_last_name'] ) ) {
        $fields['shipping']['shipping_last_name']['custom_attributes'] = [
            'data-required' => 'true',
        ];
    }
    if ( isset( $fields['shipping']['shipping_country'] ) ) {
        $fields['shipping']['shipping_country']['custom_attributes'] = [
            'data-required' => 'true',
        ];
    }
    if ( isset( $fields['shipping']['shipping_state'] ) ) {
        $fields['shipping']['shipping_state']['custom_attributes'] = [
            'data-required' => 'true',
        ];
    }
    if ( isset( $fields['shipping']['shipping_city'] ) ) {
        $fields['shipping']['shipping_city']['custom_attributes'] = [
            'data-required' => 'true',
        ];
    }
    if ( isset( $fields['shipping']['shipping_postcode'] ) ) {
        $fields['shipping']['shipping_postcode']['custom_attributes'] = [
            'data-required' => 'true',
        ];
    }
    if ( isset( $fields['shipping']['shipping_address_1'] ) ) {
        $fields['shipping']['shipping_address_1']['custom_attributes'] = [
            'data-required' => 'true',
        ];
    }

// --- Agregar campo 'teléfono' al envío (no es un campo por defecto de WooCommerce) ---
    $fields['shipping']['shipping_phone'] = array(
        'label'       => __( 'Teléfono', 'islabeya' ),
        'placeholder' => __( 'Número de teléfono', 'islabeya' ),
        'type'        => 'tel',
        'required'    => true,
        'class'       => array( 'form-row-wide' ),
        'clear'       => true,
        'validate'    => array( 'phone' ),
        'priority'    => 25,
        // Atributos para el sistema de validación global (validaciones.js)
        'custom_attributes' => array(
            'data-required' => 'true',
            'inputmode'     => 'tel',
        ),
    );

// --- Agregar campo personalizado 'número de casa / edificio y apartamento' al envío ---
    $fields['shipping']['shipping_house_number'] = [
        'label'       => __( 'Número de casa / Edificio y apartamento', 'islabeya' ),
        'placeholder' => __( 'Ej: Edificio 12, Apto 3', 'islabeya' ),
        'type'        => 'text',
        'required'    => true,
        'class'       => ['form-row-wide'],
        'clear'       => true,
        // Atributos para el sistema de validación global (validaciones.js)
        'custom_attributes' => [
            'data-required' => 'true',
        ]
    ];

// --- Agregar campo personalizado 'carnet de identidad' al envío ---
    $fields['shipping']['shipping_identity_card'] = [
        'label'       => __( 'Carnet de Identidad', 'islabeya' ),
        'placeholder' => __( 'Número de carnet de identidad', 'islabeya' ),
        'type'        => 'text',
        'required'    => true,
        'class'       => ['form-row-wide'],
        'clear'       => true,
        // Atributos para el sistema de validación global (validaciones.js)
        'custom_attributes' => [
            'data-required'   => 'true',
            'data-numeric'    => 'true',
            'data-length'     => '11',
            'inputmode'       => 'numeric',
            'pattern'         => '[0-9]*',
            'maxlength'       => '11',
        ]
    ];

    // El campo Carnet de Identidad SOLO aparece en facturación cuando NO se muestra
    // el formulario de envío (es decir, cuando todo el carrito es de recogida local).
    // Si hay envío a domicilio, el carnet se pide en el paso de Envío (shipping_identity_card).
    $necesita_envio = function_exists( 'islabeya_carrito_necesita_envio_domicilio' ) ? islabeya_carrito_necesita_envio_domicilio() : true;
    if ( ! $necesita_envio && ! isset( $fields['billing']['billing_identity_card'] ) ) {
        $fields['billing']['billing_identity_card'] = [
            'label'       => __( 'Carnet de Identidad', 'islabeya' ),
            'placeholder' => __( 'Número de carnet de identidad', 'islabeya' ),
            'type'        => 'text',
            'required'    => true,
            'class'       => ['form-row-wide'],
            'clear'       => true,
            // Atributos para el sistema de validación global (validaciones.js)
            'custom_attributes' => [
                'data-required'   => 'true',
                'data-numeric'    => 'true',
                'data-length'     => '11',
                'inputmode'       => 'numeric',
                'pattern'         => '[0-9]*',
                'maxlength'       => '11',
            ]
        ];
    }

    // El orden de los campos se controla mediante priorities nativos de WooCommerce
    // asignados en este filtro para garantizar el orden correcto.
    
    return $fields;
}



/**
 * 2. Definir valores por defecto para campos ocultos.
 *    Esto asegura que las pasarelas de pago reciban toda la información necesaria.
 */
add_filter( 'woocommerce_checkout_get_value', 'default_values_for_required_fields', 10, 2 );
function default_values_for_required_fields( $value, $input ) {
    $is_empty_value = ( null === $value || '' === $value || '0' === $value );

    if ( 'shipping_country' === $input ) {
        return 'CU';
    }

    if ( 'billing_country' === $input && ! $is_empty_value ) {
        return $value;
    }

    if ( in_array( $input, array( 'billing_state', 'shipping_state' ), true ) && ! $is_empty_value ) {
        return $value;
    }

    $default_billing_values = [
        'billing_country' => 'CU',
        'billing_postcode' => '00000',
        'billing_state'    => 'Ciudad de La Habana',
    ];

    $default_shipping_values = [
        'shipping_postcode' => '00000',
        'shipping_state'    => 'Ciudad de La Habana',
    ];

    if ( array_key_exists( $input, $default_billing_values ) ) {
        return $is_empty_value ? $default_billing_values[ $input ] : $value;
    }

    if ( array_key_exists( $input, $default_shipping_values ) ) {
        return $is_empty_value ? $default_shipping_values[ $input ] : $value;
    }

    return $value;
}

add_action( 'woocommerce_checkout_create_order', 'islabeya_forzar_envio_cuba_en_orden', 20, 2 );
function islabeya_forzar_envio_cuba_en_orden( $order, $data ) {
    $order->set_shipping_country( 'CU' );

    if ( empty( $order->get_shipping_state() ) ) {
        $order->set_shipping_state( 'Ciudad de La Habana' );
    }

    if ( empty( $order->get_shipping_postcode() ) ) {
        $order->set_shipping_postcode( '00000' );
    }

    if ( isset( WC()->customer ) ) {
        WC()->customer->set_shipping_country( 'CU' );
    }
}

/**
 * 3. Cambiar el texto del botón "Realizar pedido" a "Pagar + Cantidad a pagar".
 *    El texto incluye el total del pedido (ej. "Pagar €150,00").
 *    Devuelve HTML para poder estilizar el precio dentro del botón.
 */
add_filter( 'woocommerce_order_button_text', 'islabeya_cambiar_texto_boton_pago' );
function islabeya_cambiar_texto_boton_pago( $button_text ) {
    if ( ! WC()->cart ) {
        return $button_text;
    }

    // Verificar que el carrito no esté vacío.
    if ( WC()->cart->is_empty() ) {
        return $button_text;
    }

    // Obtener el total. En la carga inicial / recálculo AJAX, `get_total()` puede devolver "0"
    // antes de que se calcule el subtotal. Recorremos a `get_total('edit')` y, si aún es 0,
    // calculamos el subtotal de los productos del carrito como última opción.
    $total = WC()->cart->get_total( 'edit' );

    if ( empty( $total ) || 0 == $total ) {
        // Recalcular totals del carrito.
        WC()->cart->calculate_totals();
        $total = WC()->cart->get_total( 'edit' );
    }

    // Fallback: sumar el subtotal de los ítems si sigue siendo 0.
    if ( empty( $total ) || 0 == $total ) {
        $total = 0;
        foreach ( WC()->cart->get_cart() as $cart_item ) {
            $line_total = isset( $cart_item['line_total'] ) ? (float) $cart_item['line_total'] : 0;
            $total     += $line_total;
        }
        // Incluir impuestos si corresponden.
        foreach ( WC()->cart->get_taxes() as $tax ) {
            $total += (float) $tax;
        }
    }

    $total_formateado = wc_price( $total );

    // Texto con HTML: "Pagar " + precio formateado (para estilizar el importe).
    return sprintf(
        __( 'Pagar %s', 'islabeya' ),
        wp_kses_post( $total_formateado )
    );
}

/**
 * 3b. Asegurar que el total del botón se actualice tras cada recálculo AJAX.
 *     WooCommerce re-renderiza el botón con $order_button_text en cada
 *     update_order_review, por lo que el filtro ya se re-evalúa.
 */

// ============================================
// GUARDAR DATOS DE ENVÍO PERSONALIZADOS EN EL PEDIDO
// (Carnet de Identidad y Número de casa)
// ============================================

/**
 * Guarda el Carnet de Identidad como meta de la orden y como parte
 * de la dirección de envío para mostrarlo en todas las vistas.
 */
add_action( 'woocommerce_checkout_update_order_meta', 'islabeya_guardar_campos_envio', 20, 1 );
function islabeya_guardar_campos_envio( $order_id ) {
    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        return;
    }

    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( '[IslaBeya checkout] billing_country=' . ( isset( $_POST['billing_country'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_country'] ) ) : '' ) . ' shipping_country=' . ( isset( $_POST['shipping_country'] ) ? sanitize_text_field( wp_unslash( $_POST['shipping_country'] ) ) : '' ) );
        error_log( '[IslaBeya checkout] billing_state=' . ( isset( $_POST['billing_state'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_state'] ) ) : '' ) . ' shipping_state=' . ( isset( $_POST['shipping_state'] ) ? sanitize_text_field( wp_unslash( $_POST['shipping_state'] ) ) : '' ) );
        error_log( '[IslaBeya checkout] billing_city=' . ( isset( $_POST['billing_city'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_city'] ) ) : '' ) . ' shipping_city=' . ( isset( $_POST['shipping_city'] ) ? sanitize_text_field( wp_unslash( $_POST['shipping_city'] ) ) : '' ) );
    }

// --- Carnet de Identidad (envío) ---
    if ( ! empty( $_POST['shipping_identity_card'] ) || $order->get_meta( '_shipping_identity_card' ) ) {
        $carnet = isset( $_POST['shipping_identity_card'] ) ? sanitize_text_field( wp_unslash( $_POST['shipping_identity_card'] ) ) : $order->get_meta( '_shipping_identity_card' );
        $carnet = preg_replace( '/[^0-9]/', '', $carnet );
        $order->update_meta_data( '_shipping_identity_card', $carnet );
        $order->update_meta_data( '_shipping_carnet', $carnet ); // alias interno
    }

    // --- Carnet de Identidad (facturación) ---
    // Se usa cuando NO hay paso de envío (pedido solo de recogida local): el carnet
    // se pide en el bloque de facturación (billing_identity_card).
    if ( ! empty( $_POST['billing_identity_card'] ) || $order->get_meta( '_billing_identity_card' ) ) {
        $carnet_billing = isset( $_POST['billing_identity_card'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_identity_card'] ) ) : $order->get_meta( '_billing_identity_card' );
        $carnet_billing = preg_replace( '/[^0-9]/', '', $carnet_billing );
        $order->update_meta_data( '_billing_identity_card', $carnet_billing );
        $order->update_meta_data( '_billing_carnet', $carnet_billing ); // alias interno
    }

    // --- Número de casa / Edificio y apartamento ---
    if ( ! empty( $_POST['shipping_house_number'] ) || $order->get_meta( '_shipping_house_number' ) ) {
        $house = isset( $_POST['shipping_house_number'] ) ? sanitize_text_field( wp_unslash( $_POST['shipping_house_number'] ) ) : $order->get_meta( '_shipping_house_number' );
        $order->update_meta_data( '_shipping_house_number', $house );
    }

    // --- Teléfonos completos con prefijo de país ---
    // El JS (checkout-pasos.js) crea campos ocultos billing_phone_full y
    // shipping_phone_full con el número internacional completo (ej: +5351234567).
    // Aquí los guardamos como meta de la orden y actualizamos el teléfono de la
    // dirección para que quede el número completo en el pedido.

    // Facturación
    if ( ! empty( $_POST['billing_phone_full'] ) ) {
        $billing_full = sanitize_text_field( wp_unslash( $_POST['billing_phone_full'] ) );
        $order->update_meta_data( '_billing_phone_full', $billing_full );
        $order->set_billing_phone( $billing_full );
    } elseif ( $order->get_meta( '_billing_phone_full' ) ) {
        $billing_full = $order->get_meta( '_billing_phone_full' );
        $order->set_billing_phone( $billing_full );
    }

    // Envío
    if ( ! empty( $_POST['shipping_phone_full'] ) ) {
        $shipping_full = sanitize_text_field( wp_unslash( $_POST['shipping_phone_full'] ) );
        $order->update_meta_data( '_shipping_phone_full', $shipping_full );
        $order->set_shipping_phone( $shipping_full );
    } elseif ( $order->get_meta( '_shipping_phone_full' ) ) {
        $shipping_full = $order->get_meta( '_shipping_phone_full' );
        $order->set_shipping_phone( $shipping_full );
    }

    $order->save();
}

/**
 * Actualizar manualmente la dirección del pedido con el nº de casa/edificio
 * y carnet cuando se actualiza el pedido (para coherencia en la vista).
 */
add_action( 'woocommerce_checkout_create_order_shipping_item', 'islabeya_agregar_campos_envio_a_item', 10, 4 );
function islabeya_agregar_campos_envio_a_item( $item, $package_key, $package, $order ) {
    // Sin implementación por ítem; la meta queda a nivel de pedido.
    return;
}

/**
 * Redirigir /my-account/ a /mi-cuenta/
 */
add_action( 'template_redirect', 'islabeya_redirect_my_account_to_mi_cuenta' );
function islabeya_redirect_my_account_to_mi_cuenta() {
    if ( strpos( $_SERVER['REQUEST_URI'], '/my-account/' ) !== false ) {
        $new_url = str_replace( '/my-account/', '/mi-cuenta/', $_SERVER['REQUEST_URI'] );
        wp_redirect( home_url( $new_url ), 301 );
        exit;
    }
}

// ============================================
// PRODUCTO VARIABLE: “Disponible para reserva”
// Reemplazar por texto más llamativo
// ============================================
add_filter('woocommerce_available_variation', function ($data, $product, $variation) {
    if (!isset($data['availability_html'])) {
        return $data;
    }

    if (!is_string($data['availability_html'])) {
        return $data;
    }
    $data['availability_html'] = str_replace(
        'Disponible para reserva',
        'No disponible por ahora. Puede reservar el producto',
        $data['availability_html']
    );

    return $data;
}, 20, 3);


/**
 * Muestra el campo 'Carnet de identidad' en los detalles del pedido en el admin.
 */
add_action( 'woocommerce_admin_order_data_after_billing_address', 'mostrar_carnet_en_pedido_admin', 10, 1 );
function mostrar_carnet_en_pedido_admin( $order ) {
    // Reemplaza 'carnet_identidad' con el nombre de tu campo (meta key)
    $carnet = $order->get_meta( 'shipping_identity_card' ); 
    if ( ! empty( $carnet ) ) {
        echo '<p><strong>' . esc_html__( 'Carnet de identidad', 'textdomain' ) . ':</strong> ' . esc_html( $carnet ) . '</p>';
    }
}

/**
 * Muestra el campo en el email de confirmación del pedido.
 */
add_action( 'woocommerce_email_after_order_table', 'mostrar_carnet_en_email', 20, 4 );
function mostrar_carnet_en_email( $order, $sent_to_admin, $plain_text, $email ) {
    // Reemplaza 'carnet_identidad' con el nombre de tu campo
    $carnet = $order->get_meta( 'shipping_identity_card' );
    if ( ! empty( $carnet ) ) {
        echo '<p><strong>' . esc_html__( 'Carnet de identidad', 'textdomain' ) . ':</strong> ' . esc_html( $carnet ) . '</p>';
    }
}

/**
 * Agregar CSS y JS
 */
function add_assets() {
    
    // ============================================
    // CSS GLOBALES (siempre se cargan)
    // ============================================
    wp_enqueue_style( 'style', get_stylesheet_uri() );
    wp_enqueue_style( 'temaislabeya_style_main', get_template_directory_uri() . '/assets/css/styles.css' );
    wp_enqueue_style( 'temaislabeya_header', get_template_directory_uri() . '/assets/css/header.css' );

    // footer.css: no cargar en plantillas footer-fullheight (optimización LCP)
    $fullheight_templates = array(
        'page-checkout.php',
        'page-lost-password.php',
        'page-mi-cuenta.php',
        'page-registro.php',
        'my-account.php'
    );
    $is_fullheight = false;
    foreach ( $fullheight_templates as $template ) {
        if ( is_page_template( $template ) ) {
            $is_fullheight = true;
            break;
        }
    }
    if ( ! $is_fullheight ) {
        wp_enqueue_style( 'temaislabeya_footer', get_template_directory_uri() . '/assets/css/footer.css' );
    }

    // lista-tiendas: cargar solo en páginas de vendedores (optimización LCP)
    if ( is_page( 'vendedores' ) || is_page( 'store-listing' ) ) {
        wp_enqueue_style( 'temaislabeya_lista-tiendas', get_template_directory_uri() . '/assets/css/lista-tiendas.css' );
    }
    
    // ============================================
    // SISTEMA DE VALIDACIÓN GLOBAL (CSS + JS)
    // Se carga en páginas con formularios .isla-form
    // ============================================
    if ( is_checkout() || is_account_page() || is_page( 'registro' ) || is_page( 'lost-password' ) || is_page( 'login' ) ) {
        wp_enqueue_style( 'islabeya-validaciones', get_template_directory_uri() . '/assets/css/validaciones.css', array(), '1.0' );
        wp_enqueue_script( 'islabeya-validaciones', get_template_directory_uri() . '/assets/js/validaciones.js', array( 'jquery' ), '1.0', true );
    }
    
    // ============================================
    // CSS CONDICIONALES (solo en páginas específicas)
    // ============================================

    // Políticas (solo en estas páginas)
    if (
        is_page('privacy-policy') ||
        is_page('politica-de-devoluciones') ||
        is_page('terminos-y-condiciones-compradores')
    ) {
        wp_enqueue_style(
            'temaislabeya_politicas',
            get_template_directory_uri() . '/assets/css/politicas.css',
            array(),
            '1.0'
        );
    }

    // Seguimiento de Pedido
    if ( is_page('order-tracking') ) {
        wp_enqueue_style(
            'temaislabeya_seguimiento-pedido',
            get_template_directory_uri() . '/assets/css/seguimiento-pedido.css',
            array(),
            '1.0'
        );
    }
    
    // Página de inicio
    if ( is_front_page() || is_home() ) {
        wp_enqueue_style( 'temaislabeya_front-page', get_template_directory_uri() . '/assets/css/front-page.css' );
        wp_enqueue_style( 'temaislabeya_carruseles-productos', get_template_directory_uri() . '/assets/css/carruseles-productos.css' );
        wp_enqueue_style( 'temaislabeya_card-productos', get_template_directory_uri() . '/assets/css/card-productos.css' );
    }
    
    // Página de tienda, categorías, etiquetas
    if ( is_shop() || is_product_category() || is_product_tag() ) {
        wp_enqueue_style( 'temaislabeya_card-productos', get_template_directory_uri() . '/assets/css/card-productos.css' );
        wp_enqueue_style( 'temaislabeya_tienda-filtros', get_template_directory_uri() . '/assets/css/tienda-filtros.css' );
        wp_enqueue_style( 'temaislabeya_lista-tiendas', get_template_directory_uri() . '/assets/css/lista-tiendas.css' );
    }
    
    // Página de producto individual
    if ( is_product() ) {
        wp_enqueue_style( 'temaislabeya_pagina-producto', get_template_directory_uri() . '/assets/css/pagina-producto.css' );
        wp_enqueue_style( 'temaislabeya_variation-swatches', get_template_directory_uri() . '/assets/css/variation-swatches.css' );
        wp_enqueue_style( 'temaislabeya_product-gallery', get_template_directory_uri() . '/assets/css/product-gallery.css' );
        wp_enqueue_style( 'temaislabeya_card-productos', get_template_directory_uri() . '/assets/css/card-productos.css' );
        wp_enqueue_style( 'temaislabeya_reserva-badge', get_template_directory_uri() . '/assets/css/pagina-reserva.css' );
    }

    
    // Páginas de mi cuenta (solo si el usuario está logeado)
    if ( is_account_page() && is_user_logged_in() ) {
        wp_enqueue_style( 'temaislabeya_mi-cuenta', get_template_directory_uri() . '/assets/css/mi-cuenta.css' );
    }

    // Registro / lost password (no condicionado por login)
    if ( is_account_page() || is_page( 'registro' ) || is_page( 'lost-password' ) ) {
        wp_enqueue_style( 'temaislabeya_registro', get_template_directory_uri() . '/assets/css/registro.css' );
    }

    
    // Páginas de Dokan (dashboard vendedor, tienda dokan)
    if ( ( function_exists( 'dokan_is_seller_dashboard' ) && dokan_is_seller_dashboard() ) || 
         ( function_exists( 'dokan_is_store_page' ) && dokan_is_store_page() ) ) {
        wp_enqueue_style( 'temaislabeya_tienda-dokan', get_template_directory_uri() . '/assets/css/tienda-dokan.css' );
        wp_enqueue_style( 'temaislabeya_dokan', get_template_directory_uri() . '/assets/css/dokan.css' );
        wp_enqueue_style( 'temaislabeya_card-productos', get_template_directory_uri() . '/assets/css/card-productos.css' );
    }
    
    // Página de lista de vendedores (shortcode dokan-stores)
    if ( is_page( 'vendedores' ) || is_page( 'store-listing' ) ) {
        
    }
    
    // Página de wishlist (YITH)
    if ( is_page( 'wishlist' ) || ( function_exists( 'is_wishlist' ) && is_wishlist() ) ) {
        wp_enqueue_style( 'temaislabeya_favoritos', get_template_directory_uri() . '/assets/css/favoritos.css' );
        wp_enqueue_style( 'temaislabeya_card-productos', get_template_directory_uri() . '/assets/css/card-productos.css' );
    }
    
// Checkout y páginas de FunnelKit
    if ( is_checkout() || ( function_exists( 'is_wffn_funnel_page' ) && is_wffn_funnel_page() ) ) {
        // Nuevo checkout 3 pasos (solo en checkout nativo, no FunnelKit)
        if ( is_checkout() && ! ( function_exists( 'is_wffn_funnel_page' ) && is_wffn_funnel_page() ) ) {
            wp_enqueue_style( 'islabeya-checkout-nuevo', get_template_directory_uri() . '/assets/css/checkout-nuevo.css', array(), '1.0' );
            wp_enqueue_script( 'islabeya-checkout-nuevo-js', get_template_directory_uri() . '/assets/js/checkout-pasos.js', array( 'jquery', 'wc-checkout' ), '1.0', true );
        }
    }

    // Páginas de PAGO / RECIBO / GRACIAS (2 columnas, mismas clases que el checkout)
    if ( is_checkout() || is_order_received_page() || is_checkout_pay_page() ) {
        wp_enqueue_style( 'islabeya-checkout-gracias', get_template_directory_uri() . '/assets/css/checkout-gracias.css', array(), '1.0' );
    }
    
    // ============================================
    // INTEL TEL INPUT (solo en checkout, mi cuenta, registro)
    // ============================================
    if ( is_checkout() || is_account_page() || is_page( 'registro' ) ) {
        wp_enqueue_style( 'intl-tel-input', 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css', array(), '17.0.8' );
    }
    
// ============================================
    // SCRIPTS CONDICIONALES
    // ============================================

// Página de inicio
    if ( is_front_page() || is_home() ) {
        wp_enqueue_script( 'temaislabeya-script-carrusel', get_template_directory_uri() . '/assets/js/carrusel.js', array(), false, true );
        wp_enqueue_script( 'temaislabeya-script-control-video', get_template_directory_uri() . '/assets/js/control-video.js', array(), false, true );
        wp_enqueue_script( 'temaislabeya-script-card-productos', get_template_directory_uri() . '/assets/js/card-productos.js', array(), false, true );
        wp_enqueue_script( 'temaislabeya-script-acordeon-menu-movil', get_template_directory_uri() . '/assets/js/acordeon-menu-movil.js', array(), false, true );
    }
    
    // Scripts de página de producto
    if ( is_product() ) {
        wp_enqueue_script( 'temaislabeya-script-botones-cantidad', get_template_directory_uri() . '/assets/js/botones-cantidad.js', array(), false, true );
        wp_enqueue_script( 'temaislabeya-script-variation-swatches', get_template_directory_uri() . '/assets/js/variation-swatches.js', array(), false, true );
        wp_enqueue_script( 'temaislabeya-script-product-gallery', get_template_directory_uri() . '/assets/js/product-gallery.js', array(), false, true );
    }
    
    // Scripts de tienda y filtros
    if ( is_shop() || is_product_category() || is_product_tag() ) {
        wp_enqueue_script( 'temaislabeya-script-tienda-filtros', get_template_directory_uri() . '/assets/js/tienda-filtros.js', array(), false, true );
    }
    
    // Scripts de mi cuenta, registro, lost password
    if ( is_account_page() || is_page( 'registro' ) || is_page( 'lost-password' ) ) {
        wp_enqueue_script( 'temaislabeya-script-formulario-registro-sesion', get_template_directory_uri() . '/assets/js/formulario-registro-sesion.js', array(), false, true );
        wp_enqueue_script( 'temaislabeya-script-mi-cuenta', get_template_directory_uri() . '/assets/js/mi-cuenta.js', array(), false, true );
    }

    if (is_product()) {
        wp_enqueue_style('islabeya-review-css', get_template_directory_uri() . '/assets/css/review-styles.css', array(), '1.0');
        wp_enqueue_script('islabeya-review-js', get_template_directory_uri() . '/assets/js/review-script.js', array(), '1.0', true);
    }
    
    // ============================================
    // INTEL TEL INPUT (solo en checkout, mi cuenta, registro)
    // ============================================
    if ( is_checkout() || is_account_page() || is_page( 'registro' ) ) {
        wp_enqueue_script( 'intl-tel-input', 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js', array(), '17.0.8', true );
        wp_enqueue_script( 'intl-tel-input-utils', 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js', array('intl-tel-input'), '17.0.8', true );
        wp_enqueue_script( 'islabeya-registro', get_template_directory_uri() . '/assets/js/registro.js', array('intl-tel-input'), '1.0.0', true );
    }
}
add_action('wp_enqueue_scripts', 'add_assets');

// ============================================
// OPTIMIZACIÓN: Agregar defer a scripts no críticos
// ============================================
add_filter( 'script_loader_tag', 'islabeya_add_defer_to_scripts', 10, 2 );
function islabeya_add_defer_to_scripts( $tag, $handle ) {
    // Scripts que pueden tener defer (no críticos para el renderizado inicial)
    $defer_scripts = array(
        'temaislabeya-script-carrusel',
        'temaislabeya-script-card-productos',
        'temaislabeya-script-acordeon-menu-movil',
        'temaislabeya-script-botones-cantidad',
        'temaislabeya-script-variation-swatches',
        'temaislabeya-script-tienda-filtros',
        'temaislabeya-script-formulario-registro-sesion',
        'temaislabeya-script-mi-cuenta',
        'islabeya-review-js',
    );

    if ( in_array( $handle, $defer_scripts ) ) {
        return str_replace( ' src', ' defer src', $tag );
    }

    return $tag;
}


