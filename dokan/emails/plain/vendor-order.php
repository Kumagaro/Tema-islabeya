<?php
/**
 * Vendor Order Email (Plain)
 *
 * @package Tema IslaBeya
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

echo "= " . esc_html( $email_heading ) . " =\n\n";

esc_html_e( 'Has recibido un nuevo pedido.', 'dokan-lite' ) . "\n\n";

echo esc_html__( 'Pedido #:', 'dokan-lite' ) . ' ' . esc_html( $order->get_order_number() ) . "\n";
echo esc_html__( 'Cliente:', 'dokan-lite' ) . ' ' . esc_html( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ) . "\n";
echo esc_html__( 'Total:', 'dokan-lite' ) . ' ' . esc_html( $order->get_formatted_order_total() ) . "\n\n";

echo esc_url( $order->get_view_order_url() ) . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );

echo "\n\n";
