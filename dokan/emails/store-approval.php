<?php
/**
 * Store Approval Email
 *
 * @package Tema IslaBeya
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Incluir header personalizado de Dokan
include locate_template( 'dokan/emails/email-header.php' ); ?>

<p><?php esc_html_e( '¡Felicidades! Tu tienda ha sido aprobada.', 'dokan-lite' ); ?></p>

<p><?php esc_html_e( 'Ya puedes comenzar a vender tus productos en IslaBeya.', 'dokan-lite' ); ?></p>

<p style="padding: 12px 24px; background-color: #15ad3c; border-radius: 6px; text-align: center;">
    <a href="<?php echo esc_url( dokan_get_store_url( $user_id ) ); ?>" style="color: #ffffff; text-decoration: none; font-weight: 700;">
        <?php esc_html_e( 'Ir a mi tienda', 'dokan-lite' ); ?>
    </a>
</p>

<?php
// Incluir footer personalizado de Dokan
include locate_template( 'dokan/emails/email-footer.php' );
