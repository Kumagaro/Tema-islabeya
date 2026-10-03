<?php
/**
 * Store Approval Email (Plain)
 *
 * @package Tema IslaBeya
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

echo "= " . esc_html( $email_heading ) . " =\n\n";

esc_html_e( '¡Felicidades! Tu tienda ha sido aprobada.', 'dokan-lite' ) . "\n\n";
esc_html_e( 'Ya puedes comenzar a vender tus productos en IslaBeya.', 'dokan-lite' ) . "\n\n";

echo esc_url( dokan_get_store_url( $user_id ) ) . "\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );

echo "\n\n";
