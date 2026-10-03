<?php
/**
 * New Product to Admin Email
 *
 * @package Tema IslaBeya
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Incluir header personalizado de Dokan
include locate_template( 'dokan/emails/email-header.php' ); ?>

<p><?php esc_html_e( 'Un nuevo producto ha sido enviado para revisión.', 'dokan-lite' ); ?></p>

<p>
    <strong><?php esc_html_e( 'Vendedor:', 'dokan-lite' ); ?></strong> <?php echo esc_html( $vendor_name ); ?><br>
    <strong><?php esc_html_e( 'Producto:', 'dokan-lite' ); ?></strong> <?php echo esc_html( $product_title ); ?><br>
    <strong><?php esc_html_e( 'Precio:', 'dokan-lite' ); ?></strong> <?php echo esc_html( $product_price ); ?>
</p>

<p style="padding: 12px 24px; background-color: #15ad3c; border-radius: 6px; text-align: center;">
    <a href="<?php echo esc_url( $product_link ); ?>" style="color: #ffffff; text-decoration: none; font-weight: 700;">
        <?php esc_html_e( 'Revisar producto', 'dokan-lite' ); ?>
    </a>
</p>

<?php
// Incluir footer personalizado de Dokan
include locate_template( 'dokan/emails/email-footer.php' );
