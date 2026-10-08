<?php
/**
 * My Account page - Solo Login
 *
 * @package IslaBeya
 */

defined( 'ABSPATH' ) || exit;

if ( isset( $_GET['password-reset'] ) && is_scalar( $_GET['password-reset'] ) && 'true' === sanitize_text_field( wp_unslash( (string) $_GET['password-reset'] ) ) ) {
    wc_add_notice( __( 'Tu contraseña se actualizó. Ya puedes iniciar sesión con la nueva contraseña.', 'woocommerce' ) );
    wp_safe_redirect( remove_query_arg( 'password-reset' ) );
    exit;
}

get_header('fullheight');
?>


<!-- Plantilla para pagina de mi cuenta con el usuario ya logeado -->
<?php if ( function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url( 'lost-password' ) ) : ?>
    <div class="islabeya-account-wrapper">
        <div class="islabeya-account-container">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <img width="100" src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/IDENTIFICADOR-VISUAL.svg' ) ); ?>" alt="Logo IslaBeya" />
            </a>
            <div class="islabeya-login-container">
                <h2><?php esc_html_e( 'Recuperar contraseña', 'woocommerce' ); ?></h2>
                <p class="registro-intro"><?php esc_html_e( 'Sigue las instrucciones para crear una contraseña nueva y segura.', 'woocommerce' ); ?></p>
                <div class="woocommerce auth-reset-content">
                    <?php echo do_shortcode( '[woocommerce_my_account]' ); ?>
                </div>
                <div class="auth-switch">
                    <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'Volver al inicio de sesión', 'islabeya' ); ?></a>
                </div>
            </div>
        </div>
    </div>
<?php elseif ( is_user_logged_in() ) : ?>
    <?php wc_get_template( 'myaccount/content-mi-cuenta.php' ); ?>
<?php else : ?>
    <?php wc_get_template( 'myaccount/page-sesion.php' ); ?>
<?php endif; ?>

<?php get_footer('fullheight'); ?>