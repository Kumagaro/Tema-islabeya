<?php
/**
 * Withdraw Request Email (Plain)
 *
 * @package Tema IslaBeya
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

echo "= " . esc_html( $email_heading ) . " =\n\n";

esc_html_e( 'Se ha recibido una nueva solicitud de retiro.', 'dokan-lite' ) . "\n\n";

echo esc_html__( 'Vendedor:', 'dokan-lite' ) . ' ' . esc_html( $user_name ) . "\n";
echo esc_html__( 'Monto:', 'dokan-lite' ) . ' ' . esc_html( $amount ) . "\n";
echo esc_html__( 'Método:', 'dokan-lite' ) . ' ' . esc_html( $method ) . "\n\n";

echo esc_url( admin_url( 'admin.php?page=dokan-withdraw' ) ) . "\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );

echo "\n\n";
