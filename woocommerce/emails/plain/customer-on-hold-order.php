<?php
/**
 * Customer On Hold Order Email (Plain)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/plain/customer-on-hold-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails/Plain
 * @version 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

echo "= " . esc_html( $email_heading ) . " =\n\n";

/* translators: %s: Customer first name */
echo sprintf( esc_html__( 'Hola %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) ) . "\n\n";
echo esc_html__( 'Gracias por tu pedido en IslaBeya. Estamos procesando tu pedido y te notificaremos cuando esté listo para el envío.', 'woocommerce' ) . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );

echo "\n\n";
