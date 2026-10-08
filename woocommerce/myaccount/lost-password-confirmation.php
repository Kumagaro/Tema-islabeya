<?php
/**
 * Password reset email confirmation.
 *
 * @package IslaBeya
 */

defined( 'ABSPATH' ) || exit;

wc_print_notice( esc_html__( 'Te enviamos un correo para restablecer tu contraseña.', 'woocommerce' ) );

do_action( 'woocommerce_before_lost_password_confirmation_message' );
?>

<div class="auth-confirmation-message">
    <p><?php echo esc_html( apply_filters( 'woocommerce_lost_password_confirmation_message', esc_html__( 'El correo puede tardar unos minutos en llegar. Revisa también la carpeta de correo no deseado antes de solicitar otro enlace.', 'woocommerce' ) ) ); ?></p>
</div>

<?php do_action( 'woocommerce_after_lost_password_confirmation_message' ); ?>
