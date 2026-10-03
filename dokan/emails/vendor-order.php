<?php
/**
 * Vendor Order Email
 *
 * @package Tema IslaBeya
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Incluir header personalizado de Dokan
include locate_template( 'dokan/emails/email-header.php' ); ?>

<p><?php esc_html_e( 'Has recibido un nuevo pedido.', 'dokan-lite' ); ?></p>

<p>
    <strong><?php esc_html_e( 'Pedido #:', 'dokan-lite' ); ?></strong> <?php echo esc_html( $order->get_order_number() ); ?><br>
    <strong><?php esc_html_e( 'Cliente:', 'dokan-lite' ); ?></strong> <?php echo esc_html( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ); ?><br>
    <strong><?php esc_html_e( 'Total:', 'dokan-lite' ); ?></strong> <?php echo esc_html( $order->get_formatted_order_total() ); ?>
</p>

<p style="padding: 12px 24px; background-color: #15ad3c; border-radius: 6px; text-align: center;">
    <a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" style="color: #ffffff; text-decoration: none; font-weight: 700;">
        <?php esc_html_e( 'Ver pedido', 'dokan-lite' ); ?>
    </a>
</p>

<?php

/**
 * @hooked WC_Emails::order_details() Shows order details
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

// Incluir footer personalizado de Dokan
include locate_template( 'dokan/emails/email-footer.php' );
