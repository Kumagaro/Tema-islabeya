<?php
/**
 * Telegram Notifications para Islabeya
 * 
 * @package Islabeya
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ============================================================
// CONSTANTES Y HELPERS
// ============================================================

define( 'ISLABEYA_TG_LOG_FILE', WP_CONTENT_DIR . '/uploads/telegram-islabeya.log' );
define( 'ISLABEYA_TG_OPTION_KEY', 'islabeya_telegram_settings' );

function islabeya_tg_get_settings() {
    $defaults = array(
        'bot_token'        => '8948930849:AAHReo2SLGG1j2C893QR31e0LHT4Gn2vzSY',
        'chat_id_group'    => '-1003969122003',
        'chat_id_personal' => '833392516',
        'enabled'          => 1,
    );
    $saved = get_option( ISLABEYA_TG_OPTION_KEY, array() );

    if ( isset( $saved['chat_id'] ) && ! isset( $saved['chat_id_group'] ) ) {
        $saved['chat_id_group'] = $saved['chat_id'];
    }

    return wp_parse_args( $saved, $defaults );
}

function islabeya_tg_log( $message ) {
    $timestamp = wp_date( 'Y-m-d H:i:s' );
    $line = "[{$timestamp}] {$message}" . PHP_EOL;

    if ( file_exists( ISLABEYA_TG_LOG_FILE ) && filesize( ISLABEYA_TG_LOG_FILE ) > 1048576 ) {
        rename( ISLABEYA_TG_LOG_FILE, ISLABEYA_TG_LOG_FILE . '.old' );
    }
    error_log( $line, 3, ISLABEYA_TG_LOG_FILE );
}

function islabeya_tg_get_admin_email() {
    return get_option( 'admin_email' );
}

/**
 * Escapa caracteres especiales para MarkdownV2.
 * IMPORTANTE: escapar el backslash PRIMERO.
 */
function islabeya_tg_escape( $texto ) {
    $texto = str_replace( '\\', '\\\\', $texto );
    $chars = array( '_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!' );
    foreach ( $chars as $char ) {
        $texto = str_replace( $char, '\\' . $char, $texto );
    }
    return $texto;
}

// ============================================================
// 1. CAPTURA: FILTROS DE WOOCOMMERCE + HOOKS DIRECTOS
// ============================================================

$GLOBALS['islabeya_tg_processed_orders'] = array();

/**
 * Filtro universal que captura emails al admin.
 * Recibe 3 args: recipient, order, email.
 * Si $order es null/false → es un email de PRUEBA de WooCommerce.
 */
function islabeya_tg_capture_woo_email( $recipient, $order = null, $email = null ) {

    // === CASO A: Email de PRUEBA (WooCommerce envía sin order) ===
    if ( ! $order instanceof WC_Order || $order->get_id() === 0 ) {

        // Anti-duplicados en la misma request
        static $test_procesados = array();
        $email_id = ( $email && isset( $email->id ) ) ? $email->id : 'unknown';
        if ( isset( $test_procesados[ $email_id ] ) ) {
            return $recipient;
        }
        $test_procesados[ $email_id ] = true;

        islabeya_tg_log( "Email de PRUEBA WC detectado: '{$email_id}' → {$recipient}" );
        islabeya_tg_send_test_notification( $email_id, $recipient );

        return $recipient;
    }

    // === CASO B: Email real de pedido ===
    $admin_email = islabeya_tg_get_admin_email();
    if ( strpos( $recipient, $admin_email ) === false ) {
        return $recipient;
    }

    $order_id = $order->get_id();
    if ( in_array( $order_id, $GLOBALS['islabeya_tg_processed_orders'], true ) ) {
        return $recipient;
    }
    $GLOBALS['islabeya_tg_processed_orders'][] = $order_id;

    islabeya_tg_schedule_notification( $order_id, 'wc_email_filter' );

    return $recipient;
}

// Registrar con 3 argumentos para recibir el objeto $email
foreach ( array( 'new_order', 'cancelled_order', 'failed_order' ) as $email_id ) {
    add_filter( "woocommerce_email_recipient_{$email_id}", 'islabeya_tg_capture_woo_email', 20, 3 );
}

// Emails de Dokan al admin
foreach ( array( 'dokan_new_seller', 'dokan_new_product', 'dokan_product_published', 'dokan_withdraw_request', 'dokan_contact_seller' ) as $email_id ) {
    add_filter( "woocommerce_email_recipient_{$email_id}", 'islabeya_tg_capture_woo_email', 20, 3 );
}

/**
 * Envía un mensaje informativo cuando se manda un email de prueba
 * desde WooCommerce → Ajustes → Emails.
 */
function islabeya_tg_send_test_notification( $email_id, $recipient ) {

    $settings = islabeya_tg_get_settings();

    $msg  = "🧪 *Email de PRUEBA de WooCommerce*\n\n";
    $msg .= "📧 *Tipo:* " . islabeya_tg_escape( $email_id ) . "\n";
    $msg .= "👤 *Destinatario:* " . islabeya_tg_escape( $recipient ) . "\n";
    $msg .= "📅 *Fecha:* " . islabeya_tg_escape( wp_date( 'd/m/Y H:i:s' ) ) . "\n\n";
    $msg .= "Si ves este mensaje, el sistema de notificaciones de Telegram funciona correctamente\\.";

    // Enviar al grupo
    if ( ! empty( $settings['chat_id_group'] ) ) {
        $r = islabeya_tg_send_message( $settings['bot_token'], $settings['chat_id_group'], $msg );
        islabeya_tg_log( "Test notification a 'group': " . ( $r ? 'OK' : 'FALLO' ) );
    }
    // Enviar al personal
    if ( ! empty( $settings['chat_id_personal'] ) ) {
        $r = islabeya_tg_send_message( $settings['bot_token'], $settings['chat_id_personal'], $msg );
        islabeya_tg_log( "Test notification a 'personal': " . ( $r ? 'OK' : 'FALLO' ) );
    }
}

/**
 * Hook DIRECTO para cambios de estado.
 */
add_action( 'woocommerce_order_status_changed', 'islabeya_tg_on_status_change', 10, 4 );
function islabeya_tg_on_status_change( $order_id, $from, $to, $order ) {
    islabeya_tg_schedule_notification( $order_id, "status:{$from}->{$to}" );
}

/**
 * Hook para pedidos nuevos.
 */
add_action( 'woocommerce_new_order', 'islabeya_tg_on_new_order', 10, 1 );
function islabeya_tg_on_new_order( $order_id ) {
    islabeya_tg_schedule_notification( $order_id, 'new_order_hook' );
}

// ============================================================
// 2. HOOK DE RESPALDO: wp_mail
// ============================================================

add_filter( 'wp_mail', 'islabeya_tg_backup_wp_mail', 20 );
function islabeya_tg_backup_wp_mail( $atts ) {

    if ( ! empty( $GLOBALS['islabeya_tg_processed_orders'] ) ) {
        return $atts;
    }

    $to = is_array( $atts['to'] ) ? implode( ',', $atts['to'] ) : $atts['to'];
    $admin_email = islabeya_tg_get_admin_email();
    if ( strpos( $to, $admin_email ) === false ) {
        return $atts;
    }

    $order_id = islabeya_tg_extract_order_id( $atts['subject'] );
    if ( ! $order_id ) {
        return $atts;
    }

    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        return $atts;
    }

    $GLOBALS['islabeya_tg_processed_orders'][] = $order_id;

    islabeya_tg_log( "Backup wp_mail: detectado pedido #{$order_id} (asunto: {$atts['subject']})" );
    islabeya_tg_schedule_notification( $order_id, 'wp_mail_backup' );

    return $atts;
}

function islabeya_tg_extract_order_id( $subject ) {
    if ( preg_match( '/#(\d{1,10})/', $subject, $m ) ) {
        return (int) $m[1];
    }
    if ( preg_match( '/(?:pedido|order|compra)\s+(\d{1,10})/i', $subject, $m ) ) {
        return (int) $m[1];
    }
    return 0;
}

// ============================================================
// 3. EJECUCIÓN EN SHUTDOWN
// ============================================================

function islabeya_tg_schedule_notification( $order_id, $source = 'unknown' ) {

    static $ya_programados = array();
    if ( isset( $ya_programados[ $order_id ] ) ) {
        return;
    }
    $ya_programados[ $order_id ] = true;

    add_action( 'shutdown', function() use ( $order_id, $source ) {
        islabeya_tg_process_notification( array(
            'order_id' => $order_id,
            'source'   => $source,
        ) );
    }, 9999 );

    islabeya_tg_log( "Programado en shutdown: pedido #{$order_id} (origen: {$source})" );
}

function islabeya_tg_process_notification( $args ) {

    islabeya_tg_log( ">>> INICIO process_notification: pedido #{$args['order_id']} (origen: {$args['source']})" );

    if ( empty( $args['order_id'] ) ) return;

    $settings = islabeya_tg_get_settings();
    if ( empty( $settings['enabled'] ) ) {
        islabeya_tg_log( "!!! Notificaciones desactivadas" );
        return;
    }

    $order = wc_get_order( $args['order_id'] );
    if ( ! $order ) {
        islabeya_tg_log( "!!! Pedido no encontrado" );
        return;
    }

    $mensaje = islabeya_tg_build_message( $order );
    islabeya_tg_log( ">>> Mensaje construido (" . strlen( $mensaje ) . " bytes)" );

    $chats = array();
    if ( ! empty( $settings['chat_id_group'] ) ) {
        $chats['group'] = $settings['chat_id_group'];
    }
    if ( ! empty( $settings['chat_id_personal'] ) ) {
        $chats['personal'] = $settings['chat_id_personal'];
    }

    if ( empty( $chats ) ) return;

    $messages = $order->get_meta( '_islabeya_telegram_messages' );
    if ( ! is_array( $messages ) ) {
        $messages = array();
    }

    foreach ( $chats as $key => $chat_id ) {

        $existing_id = isset( $messages[ $key ] ) ? $messages[ $key ] : 0;

        if ( $existing_id ) {
            $ok = islabeya_tg_edit_message( $settings['bot_token'], $chat_id, $existing_id, $mensaje );
            islabeya_tg_log( "Editado mensaje {$existing_id} en '{$key}': " . ( $ok ? 'OK' : 'FALLO' ) );
        } else {
            $result = islabeya_tg_send_message( $settings['bot_token'], $chat_id, $mensaje );
            if ( $result && ! empty( $result['message_id'] ) ) {
                $messages[ $key ] = $result['message_id'];
                islabeya_tg_log( "Enviado mensaje {$result['message_id']} a '{$key}': OK" );
            } else {
                islabeya_tg_log( "Fallo al enviar a '{$key}'" );
            }
        }
    }

    $order->update_meta_data( '_islabeya_telegram_messages', $messages );
    $order->save();

    islabeya_tg_log( ">>> FIN process_notification pedido #{$args['order_id']}" );
}

// ============================================================
// 4. CONSTRUCCIÓN DEL MENSAJE
// ============================================================

function islabeya_tg_get_dokan_vendors( $order ) {
    if ( ! function_exists( 'dokan_get_store_info' ) ) {
        return array();
    }

    $vendor_ids = array();
    foreach ( $order->get_items() as $item ) {
        $vid = $item->get_meta( '_dokan_vendor_id' );
        if ( ! $vid ) $vid = $item->get_meta( 'dokan_vendor_id' );
        if ( $vid ) $vendor_ids[ (int) $vid ] = true;
    }

    if ( empty( $vendor_ids ) && function_exists( 'dokan_get_seller_id_by_order' ) ) {
        $sid = dokan_get_seller_id_by_order( $order->get_id() );
        if ( $sid ) $vendor_ids[ (int) $sid ] = true;
    }

    $vendors = array();
    foreach ( array_keys( $vendor_ids ) as $vid ) {
        $store = dokan_get_store_info( $vid );
        $user  = get_userdata( $vid );

        $commission = 0;
        foreach ( $order->get_items() as $item ) {
            $item_vid = $item->get_meta( '_dokan_vendor_id' );
            if ( (int) $item_vid !== (int) $vid ) continue;
            $earning = $item->get_meta( '_dokan_vendor_earning' );
            if ( ! $earning ) $earning = $item->get_meta( 'dokan_commission' );
            if ( ! $earning ) $earning = $item->get_meta( '_dokan_line_item_commission' );
            $commission += floatval( $earning );
        }

        $vendors[] = array(
            'store_name' => ! empty( $store['store_name'] ) ? $store['store_name'] : ( $user ? $user->display_name : "Vendedor #{$vid}" ),
            'commission' => $commission,
        );
    }

    return $vendors;
}

function islabeya_tg_build_message( $order ) {

    $order_id     = $order->get_id();
    $order_num    = $order->get_order_number();
    $status       = wc_get_order_status_name( $order->get_status() );
    $moneda       = $order->get_currency();

    $nombre       = trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() );
    $telefono     = $order->get_billing_phone();
    $email        = $order->get_billing_email();
    $dir_factura  = $order->get_formatted_billing_address();
    $dir_envio    = $order->get_formatted_shipping_address();
    $metodo_pago  = $order->get_payment_method_title();
    $metodo_envio = $order->get_shipping_method();
    $notas        = $order->get_customer_note();
    $cupones      = $order->get_coupon_codes();

    $fecha = $order->get_date_created();
    $fecha_str = $fecha ? wp_date( 'd/m/Y H:i', $fecha->getTimestamp() ) : '—';

    $enlace_admin   = admin_url( "post.php?post={$order_id}&action=edit" );
    $enlace_cliente = $order->get_checkout_order_received_url();

    $price = function( $amount ) use ( $moneda ) {
        return islabeya_tg_escape( strip_tags( wc_price( $amount, array( 'currency' => $moneda ) ) ) );
    };

    $msg  = "🛒 *PEDIDO \\#" . islabeya_tg_escape( $order_num ) . "* — " . islabeya_tg_escape( $status ) . "\n";
    $msg .= "📅 " . islabeya_tg_escape( $fecha_str ) . "\n\n";

    $msg .= "👤 *Cliente:* " . islabeya_tg_escape( $nombre ) . "\n";
    if ( $telefono ) $msg .= "📞 " . islabeya_tg_escape( $telefono ) . "\n";
    if ( $email )    $msg .= "✉️ " . islabeya_tg_escape( $email ) . "\n";
    $msg .= "\n";

    if ( $dir_factura ) {
        $msg .= "🏠 *Facturación:*\n" . islabeya_tg_escape( strip_tags( $dir_factura ) ) . "\n\n";
    }
    if ( $dir_envio && $dir_envio !== $dir_factura ) {
        $msg .= "🚚 *Envío:*\n" . islabeya_tg_escape( strip_tags( $dir_envio ) ) . "\n\n";
    }

    $msg .= "📦 *Productos:*\n";
    foreach ( $order->get_items() as $item ) {
        $cant = $item->get_quantity();
        $prod = $item->get_name();
        $sub  = $item->get_subtotal();
        $msg .= "• {$cant}x " . islabeya_tg_escape( $prod ) . " — " . $price( $sub ) . "\n";
    }
    $msg .= "\n";

    $vendors = islabeya_tg_get_dokan_vendors( $order );
    if ( ! empty( $vendors ) ) {
        $msg .= "🏪 *Vendedores:*\n";
        foreach ( $vendors as $v ) {
            $line = "• " . islabeya_tg_escape( $v['store_name'] );
            if ( $v['commission'] > 0 ) {
                $line .= " — Comisión: " . $price( $v['commission'] );
            }
            $msg .= $line . "\n";
        }
        $msg .= "\n";
    }

    $msg .= "💰 *Subtotal:* " . $price( $order->get_subtotal() ) . "\n";
    if ( $order->get_shipping_total() > 0 ) {
        $msg .= "🚚 *Envío:* " . $price( $order->get_shipping_total() ) . "\n";
    }
    if ( $order->get_total_discount() > 0 ) {
        $msg .= "🏷️ *Descuento:* -" . $price( $order->get_total_discount() ) . "\n";
    }
    if ( $order->get_total_tax() > 0 ) {
        $msg .= "📊 *Impuestos:* " . $price( $order->get_total_tax() ) . "\n";
    }
    $msg .= "💵 *TOTAL: " . $price( $order->get_total() ) . "*\n\n";

    if ( $metodo_pago )  $msg .= "💳 *Pago:* " . islabeya_tg_escape( $metodo_pago ) . "\n";
    if ( $metodo_envio ) $msg .= "📦 *Envío:* " . islabeya_tg_escape( $metodo_envio ) . "\n";
    if ( ! empty( $cupones ) ) $msg .= "🎟️ *Cupón:* " . islabeya_tg_escape( implode( ', ', $cupones ) ) . "\n";

    if ( $notas ) {
        $msg .= "\n📝 *Notas:* " . islabeya_tg_escape( $notas ) . "\n";
    }

    $url_admin_safe   = str_replace( array( '\\', ')' ), array( '\\\\', '\\)' ), $enlace_admin );
    $url_cliente_safe = str_replace( array( '\\', ')' ), array( '\\\\', '\\)' ), $enlace_cliente );

    $msg .= "\n🔗 [Ver en Admin](" . $url_admin_safe . ")\n";
    if ( $enlace_cliente ) {
        $msg .= "🔗 [Ver pedido](" . $url_cliente_safe . ")\n";
    }

    return $msg;
}

// ============================================================
// 5. API DE TELEGRAM
// ============================================================

function islabeya_tg_send_message( $token, $chat_id, $texto ) {

    $url = "https://api.telegram.org/bot{$token}/sendMessage";

    $response = wp_remote_post( $url, array(
        'timeout' => 10,
        'headers' => array( 'Content-Type' => 'application/json' ),
        'body'    => wp_json_encode( array(
            'chat_id'                  => $chat_id,
            'text'                     => $texto,
            'parse_mode'               => 'MarkdownV2',
            'disable_web_page_preview' => true,
        ) ),
    ) );

    if ( is_wp_error( $response ) ) {
        islabeya_tg_log( "Error WP send: " . $response->get_error_message() );
        return false;
    }

    $code = wp_remote_retrieve_response_code( $response );
    $body = json_decode( wp_remote_retrieve_body( $response ), true );

    if ( $code === 200 && ! empty( $body['ok'] ) ) {
        return array( 'message_id' => $body['result']['message_id'] );
    }

    $desc = isset( $body['description'] ) ? $body['description'] : '';
    if ( strpos( $desc, 'parse entities' ) !== false ) {

        islabeya_tg_log( "MarkdownV2 falló, reintentando sin formato. Error: {$desc}" );
        $texto_plano = preg_replace( '/\\\\(.)/', '$1', $texto );

        $response2 = wp_remote_post( $url, array(
            'timeout' => 10,
            'headers' => array( 'Content-Type' => 'application/json' ),
            'body'    => wp_json_encode( array(
                'chat_id'                  => $chat_id,
                'text'                     => $texto_plano,
                'disable_web_page_preview' => true,
            ) ),
        ) );

        $code2 = wp_remote_retrieve_response_code( $response2 );
        $body2 = json_decode( wp_remote_retrieve_body( $response2 ), true );

        if ( $code2 === 200 && ! empty( $body2['ok'] ) ) {
            islabeya_tg_log( "Fallback sin formato: OK (message_id {$body2['result']['message_id']})" );
            return array( 'message_id' => $body2['result']['message_id'] );
        }
        islabeya_tg_log( "Fallback sin formato también falló: " . print_r( $body2, true ) );
    } else {
        islabeya_tg_log( "Error API send (HTTP {$code}): " . print_r( $body, true ) );
    }

    return false;
}

function islabeya_tg_edit_message( $token, $chat_id, $message_id, $texto ) {

    $url = "https://api.telegram.org/bot{$token}/editMessageText";

    $response = wp_remote_post( $url, array(
        'timeout' => 10,
        'headers' => array( 'Content-Type' => 'application/json' ),
        'body'    => wp_json_encode( array(
            'chat_id'                  => $chat_id,
            'message_id'               => $message_id,
            'text'                     => $texto,
            'parse_mode'               => 'MarkdownV2',
            'disable_web_page_preview' => true,
        ) ),
    ) );

    if ( is_wp_error( $response ) ) {
        islabeya_tg_log( "Error WP edit: " . $response->get_error_message() );
        return false;
    }

    $code = wp_remote_retrieve_response_code( $response );
    $body = json_decode( wp_remote_retrieve_body( $response ), true );

    if ( $code === 200 && ! empty( $body['ok'] ) ) {
        return true;
    }

    $desc = isset( $body['description'] ) ? $body['description'] : '';

    if ( strpos( $desc, 'message is not modified' ) !== false ) {
        islabeya_tg_log( "Edit ignorado: contenido idéntico. OK." );
        return true;
    }

    if ( strpos( $desc, 'parse entities' ) !== false ) {

        islabeya_tg_log( "MarkdownV2 edit falló, reintentando sin formato. Error: {$desc}" );
        $texto_plano = preg_replace( '/\\\\(.)/', '$1', $texto );

        $response2 = wp_remote_post( $url, array(
            'timeout' => 10,
            'headers' => array( 'Content-Type' => 'application/json' ),
            'body'    => wp_json_encode( array(
                'chat_id'                  => $chat_id,
                'message_id'               => $message_id,
                'text'                     => $texto_plano,
                'disable_web_page_preview' => true,
            ) ),
        ) );

        $code2 = wp_remote_retrieve_response_code( $response2 );
        $body2 = json_decode( wp_remote_retrieve_body( $response2 ), true );

        if ( $code2 === 200 && ! empty( $body2['ok'] ) ) {
            islabeya_tg_log( "Fallback sin formato edit: OK" );
            return true;
        }

        $desc2 = isset( $body2['description'] ) ? $body2['description'] : '';
        if ( strpos( $desc2, 'message is not modified' ) !== false ) {
            islabeya_tg_log( "Fallback edit ignorado: contenido idéntico. OK." );
            return true;
        }

        islabeya_tg_log( "Fallback sin formato edit también falló: " . print_r( $body2, true ) );
    } else {
        islabeya_tg_log( "Error API edit (HTTP {$code}): " . print_r( $body, true ) );
    }

    return false;
}

// ============================================================
// 6. PANEL DE ADMINISTRACIÓN
// ============================================================

add_action( 'admin_menu', 'islabeya_tg_admin_menu' );
function islabeya_tg_admin_menu() {
    add_menu_page(
        'Telegram Islabeya', 'Telegram Islabeya', 'manage_options',
        'islabeya-telegram', 'islabeya_tg_admin_page', 'dashicons-email', 30
    );
}

function islabeya_tg_admin_page() {

    if ( isset( $_POST['islabeya_tg_save'] ) && check_admin_referer( 'islabeya_tg_settings' ) ) {
        $settings = array(
            'bot_token'        => sanitize_text_field( $_POST['bot_token'] ),
            'chat_id_group'    => sanitize_text_field( $_POST['chat_id_group'] ),
            'chat_id_personal' => sanitize_text_field( $_POST['chat_id_personal'] ),
            'enabled'          => isset( $_POST['enabled'] ) ? 1 : 0,
        );
        update_option( ISLABEYA_TG_OPTION_KEY, $settings );
        echo '<div class="notice notice-success"><p>Configuración guardada.</p></div>';
    }

    if ( isset( $_POST['islabeya_tg_clear_log'] ) && check_admin_referer( 'islabeya_tg_clear_log' ) ) {
        if ( file_exists( ISLABEYA_TG_LOG_FILE ) ) unlink( ISLABEYA_TG_LOG_FILE );
        echo '<div class="notice notice-success"><p>Log limpiado.</p></div>';
    }

    if ( isset( $_POST['islabeya_tg_test'] ) && check_admin_referer( 'islabeya_tg_settings' ) ) {
        $s = islabeya_tg_get_settings();
        $test_msg = "🧪 *Prueba desde Islabeya*\n\nSi ves esto, todo funciona\\.";
        $r1 = islabeya_tg_send_message( $s['bot_token'], $s['chat_id_group'], $test_msg );
        $r2 = islabeya_tg_send_message( $s['bot_token'], $s['chat_id_personal'], $test_msg );
        echo '<div class="notice notice-info"><p>Grupo: ' . ( $r1 ? '✅' : '❌' ) . ' | Personal: ' . ( $r2 ? '✅' : '❌' ) . '</p></div>';
    }

    if ( isset( $_POST['islabeya_tg_resend'] ) && check_admin_referer( 'islabeya_tg_settings' ) ) {
        $oid = (int) $_POST['resend_order_id'];
        if ( $oid && wc_get_order( $oid ) ) {
            $order = wc_get_order( $oid );
            $order->delete_meta_data( '_islabeya_telegram_messages' );
            $order->save();
            islabeya_tg_process_notification( array( 'order_id' => $oid, 'source' => 'manual_resend' ) );
            echo '<div class="notice notice-success"><p>Pedido #' . $oid . ' reenviado.</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>Pedido no válido.</p></div>';
        }
    }

    $settings = islabeya_tg_get_settings();
    $log_content = file_exists( ISLABEYA_TG_LOG_FILE ) ? file_get_contents( ISLABEYA_TG_LOG_FILE ) : 'Sin registros.';
    ?>
    <div class="wrap">
        <h1>📬 Telegram Islabeya</h1>

        <div style="display: flex; gap: 20px; flex-wrap: wrap;">

            <div style="flex: 1; min-width: 320px; background: #fff; padding: 20px; border: 1px solid #ccd0d4;">
                <h2>Configuración</h2>
                <form method="post">
                    <?php wp_nonce_field( 'islabeya_tg_settings' ); ?>
                    <table class="form-table">
                        <tr>
                            <th><label for="bot_token">Bot Token</label></th>
                            <td><input type="text" name="bot_token" id="bot_token" value="<?php echo esc_attr( $settings['bot_token'] ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label for="chat_id_group">Chat ID del grupo</label></th>
                            <td>
                                <input type="text" name="chat_id_group" id="chat_id_group" value="<?php echo esc_attr( $settings['chat_id_group'] ); ?>" class="regular-text" />
                                <p class="description">Ej. <code>-1003969122003</code></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="chat_id_personal">Chat ID personal</label></th>
                            <td>
                                <input type="text" name="chat_id_personal" id="chat_id_personal" value="<?php echo esc_attr( $settings['chat_id_personal'] ); ?>" class="regular-text" />
                                <p class="description">Ej. <code>833392516</code>. Deja vacío para no enviar.</p>
                            </td>
                        </tr>
                        <tr>
                            <th>Activo</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="enabled" value="1" <?php checked( $settings['enabled'], 1 ); ?> />
                                    Enviar notificaciones
                                </label>
                            </td>
                        </tr>
                    </table>
                    <p>
                        <input type="submit" name="islabeya_tg_save" class="button button-primary" value="Guardar cambios" />
                        <input type="submit" name="islabeya_tg_test" class="button" value="Enviar mensaje de prueba" />
                    </p>

                    <hr style="margin: 20px 0;">
                    <h3>Reenviar pedido manualmente</h3>
                    <p>
                        <input type="number" name="resend_order_id" placeholder="ID del pedido" style="width: 140px;" />
                        <input type="submit" name="islabeya_tg_resend" class="button" value="Reenviar al bot" />
                    </p>
                    <p class="description">
                        <strong>Nota:</strong> Los emails de prueba que envíes desde <em>WooCommerce → Ajustes → Emails → "Enviar email de prueba"</em> 
                        también generarán un mensaje aquí como confirmación (con encabezado "🧪 Email de PRUEBA de WooCommerce").
                    </p>
                </form>
            </div>

            <div style="flex: 2; min-width: 420px; background: #fff; padding: 20px; border: 1px solid #ccd0d4;">
                <h2>Log de actividad</h2>
                <form method="post" style="margin-bottom: 10px;">
                    <?php wp_nonce_field( 'islabeya_tg_clear_log' ); ?>
                    <input type="submit" name="islabeya_tg_clear_log" class="button" value="Limpiar log" onclick="return confirm('¿Limpiar el log?');" />
                </form>
                <pre style="background: #f1f1f1; padding: 15px; max-height: 500px; overflow: auto; font-size: 12px; line-height: 1.6; white-space: pre-wrap; word-break: break-all;"><?php echo esc_html( $log_content ); ?></pre>
            </div>

        </div>
    </div>
    <?php
}