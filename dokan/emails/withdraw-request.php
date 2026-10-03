<?php
/**
 * Withdraw Request Email
 *
 * @package Tema IslaBeya
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Incluir header personalizado de Dokan
include locate_template( 'dokan/emails/email-header.php' ); ?>

<p><?php esc_html_e( 'Se ha recibido una nueva solicitud de retiro.', 'dokan-lite' ); ?></p>

<p>
    <strong><?php esc_html_e( 'Vendedor:', 'dokan-lite' ); ?></strong> <?php echo esc_html( $user_name ); ?><br>
    <strong><?php esc_html_e( 'Monto:', 'dokan-lite' ); ?></strong> <?php echo esc_html( $amount ); ?><br>
    <strong><?php esc_html_e( 'Método:', 'dokan-lite' ); ?></strong> <?php echo esc_html( $method ); ?>
</p>

<p style="padding: 12px 24px; background-color: #15ad3c; border-radius: 6px; text-align: center;">
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=dokan-withdraw' ) ); ?>" style="color: #ffffff; text-decoration: none; font-weight: 700;">
        <?php esc_html_e( 'Revisar solicitud', 'dokan-lite' ); ?>
    </a>
</p>

<?php
// Incluir footer personalizado de Dokan
include locate_template( 'dokan/emails/email-footer.php' );
