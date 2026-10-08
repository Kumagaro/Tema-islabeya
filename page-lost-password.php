<?php
/**
 * Template Name: Lost Password (Restablecer contraseña)
 *
 * @package IslaBeya
 */

get_header( 'fullheight' );
?>

<div class="islabeya-account-wrapper">
    <div class="islabeya-account-container">
        <a href="<?php echo home_url(); ?>">
            <img width="100" src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/IDENTIFICADOR-VISUAL.svg' ) ); ?>" 
                 alt="Logo IslaBeya" />
        </a>

        <div class="islabeya-login-container">
            <h2><?php esc_html_e( '¿Perdiste tu contraseña?', 'woocommerce' ); ?></h2>
            <p class="registro-intro" id="lost-password-intro"><?php esc_html_e( 'Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.', 'woocommerce' ); ?></p>

            <div class="auth-notices" role="alert" aria-live="assertive">
                <?php wc_print_notices(); ?>
            </div>

            <!-- Agregamos la clase 'woocommerce-form-login' para que herede los estilos -->
            <form method="post" class="woocommerce-form woocommerce-form-login woocommerce-form-lost-password isla-form">
                <p class="form-row form-row-wide">
                    <label for="user_login">
                        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/icons/Letter.svg' ); ?>" alt="Email">
                    </label>
                        <input type="text" class="input-text" name="user_login" id="user_login"
                           placeholder="<?php esc_attr_e( 'Correo electrónico o nombre de usuario', 'woocommerce' ); ?>"
                              autocomplete="username" required aria-required="true"
                              data-required="true" aria-describedby="lost-password-intro" />
                </p>

                <p class="form-row">
                    <button type="submit" class="woocommerce-Button button btn btn-second" name="wc_reset_password" value="<?php esc_attr_e( 'Restablecer contraseña', 'woocommerce' ); ?>">
                        <?php esc_html_e( 'Restablecer contraseña', 'woocommerce' ); ?>
                    </button>
                </p>

                <?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>
            </form>

            <div class="auth-switch">
                <?php esc_html_e( '¿Recordaste tu contraseña?', 'islabeya' ); ?>
                <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">
                    <?php esc_html_e( 'Inicia sesión aquí', 'islabeya' ); ?>
                </a>
            </div>
        </div>
    </div>
</div>

<?php get_footer( 'fullheight' ); ?>