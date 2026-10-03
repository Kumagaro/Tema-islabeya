<?php
/**
 * New Product to Admin Email (Plain)
 *
 * @package Tema IslaBeya
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

echo "= " . esc_html( $email_heading ) . " =\n\n";

esc_html_e( 'Un nuevo producto ha sido enviado para revisión.', 'dokan-lite' ) . "\n\n";

echo esc_html__( 'Vendedor:', 'dokan-lite' ) . ' ' . esc_html( $vendor_name ) . "\n";
echo esc_html__( 'Producto:', 'dokan-lite' ) . ' ' . esc_html( $product_title ) . "\n";
echo esc_html__( 'Precio:', 'dokan-lite' ) . ' ' . esc_html( $product_price ) . "\n\n";

echo esc_url( $product_link ) . "\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );

echo "\n\n";
