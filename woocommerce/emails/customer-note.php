<?php
/**
 * Customer Note Email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-note.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s: Customer first name */ ?>
<p><?php printf( esc_html__( 'Hola %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) ); ?></p>
<p><?php esc_html_e( 'Se ha añadido una nota a tu pedido:', 'woocommerce' ); ?></p>

<blockquote><?php echo wpautop( wptexturize( $customer_note ) ); ?></blockquote>

<p><?php esc_html_e( 'Para ver los detalles de tu pedido, visita el siguiente enlace:', 'woocommerce' ); ?></p>

<p style="padding: 12px 24px; background-color: #15ad3c; border-radius: 6px; text-align: center;">
    <a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" style="color: #ffffff; text-decoration: none; font-weight: 700;">
        <?php esc_html_e( 'Ver pedido', 'woocommerce' ); ?>
    </a>
</p>

<?php

/*
 * @hooked WC_Emails::order_details() Shows order details
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );
do_action( 'woocommerce_email_footer', $email );
