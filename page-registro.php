<?php
/**
 * Template Name: Registro de Clientes IslaBeya
 *
 * @package IslaBeya
 */

get_header('fullheight');

// Procesar el formulario de registro manualmente
if ( isset( $_POST['register'] ) && isset( $_POST['woocommerce-register-nonce'] ) && wp_verify_nonce( $_POST['woocommerce-register-nonce'], 'woocommerce-register' ) ) {
    
    $email = sanitize_email( $_POST['email'] );
    $password = $_POST['password'];
    $role = isset( $_POST['role'] ) ? sanitize_text_field( $_POST['role'] ) : 'customer';
    
    // Validaciones básicas
    if ( empty( $email ) || empty( $password ) ) {
        wc_add_notice( __( 'Por favor completa todos los campos obligatorios.', 'woocommerce' ), 'error' );
    } elseif ( ! is_email( $email ) ) {
        wc_add_notice( __( 'Por favor introduce un correo electrónico válido.', 'woocommerce' ), 'error' );
    } elseif ( email_exists( $email ) ) {
        wc_add_notice( __( 'Ya existe una cuenta con este correo electrónico.', 'woocommerce' ), 'error' );
    } else {
        // Crear usuario
        $user_id = wp_create_user( $email, $password, $email );
        
        if ( is_wp_error( $user_id ) ) {
            wc_add_notice( __( 'Error al crear la cuenta. Por favor intenta de nuevo.', 'woocommerce' ), 'error' );
        } else {
            // Asignar rol
            $user = new WP_User( $user_id );
            $user->set_role( $role );
            
            // Guardar metadatos adicionales si es vendedor
            if ( $role === 'seller' && function_exists( 'dokan_get_store_info' ) ) {
                $store_info = array(
                    'store_name'   => sanitize_text_field( $_POST['shopname'] ),
                    'address'      => array(),
                    'phone'        => sanitize_text_field( $_POST['phone'] ),
                    'banner'       => 0,
                );
                update_user_meta( $user_id, 'dokan_profile_settings', $store_info );
                update_user_meta( $user_id, 'dokan_store_name', sanitize_text_field( $_POST['shopname'] ) );
                update_user_meta( $user_id, 'shop_url', sanitize_title( $_POST['shopurl'] ) );
                update_user_meta( $user_id, 'first_name', sanitize_text_field( $_POST['fname'] ) );
                update_user_meta( $user_id, 'last_name', sanitize_text_field( $_POST['lname'] ) );
                update_user_meta( $user_id, 'billing_phone', sanitize_text_field( $_POST['phone'] ) );
                
                // Notificar a Dokan que hay un nuevo vendedor
                do_action( 'dokan_new_seller_created', $user_id, $store_info );
            } else {
                // Guardar nombre y apellido para clientes
                update_user_meta( $user_id, 'first_name', sanitize_text_field( $_POST['fname'] ?? '' ) );
                update_user_meta( $user_id, 'last_name', sanitize_text_field( $_POST['lname'] ?? '' ) );
                update_user_meta( $user_id, 'billing_phone', sanitize_text_field( $_POST['phone'] ?? '' ) );
            }
            
            // Iniciar sesión automáticamente
            wp_set_current_user( $user_id );
            wp_set_auth_cookie( $user_id );
            
            // Redirigir según el rol
            if ( $role === 'seller' ) {
                $redirect_url = dokan_get_navigation_url( 'dashboard' );
            } else {
                $redirect_url = wc_get_page_permalink( 'myaccount' );
            }
            
            wp_redirect( $redirect_url );
            exit;
        }
    }
}
?>

<div class="islabeya-registro-wrapper">
    <div class="islabeya-registro-container">
        <a href="<?php echo home_url(); ?>">
            <img width="100" src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/IDENTIFICADOR-VISUAL.svg' ) ); ?>" 
                 alt="Logo IslaBeya, Identificador Visual Islabeya" />
        </a> 
        <h2><?php esc_html_e( 'Crear una cuenta', 'woocommerce' ); ?></h2>
        <p class="registro-intro">Regístrate para disfrutar de una experiencia de compra más rápida.</p>

        <?php
        if ( is_user_logged_in() ) {
            echo '<div class="woocommerce-info">';
            echo esc_html__( 'Ya estás registrado. ', 'woocommerce' );
            echo '<a href="' . esc_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ) . '">' . esc_html__( 'Ir a mi cuenta', 'woocommerce' ) . '</a>';
            echo '</div>';
        } else {
            wc_print_notices();
            ?>
            
            <form method="post" class="woocommerce-form woocommerce-form-register register isla-form" 
                  action="<?php echo esc_url( get_permalink() ); ?>"
                  id="registro-form"
                  novalidate>
                  
                <?php do_action( 'woocommerce_register_form_start' ); ?>
                
                <!-- Campo de correo electrónico -->
                <p class="form-row form-row-wide">
                    <label for="reg_email">
                        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/icons/Letter.svg' ); ?>" alt="Email" width="20" height="20">
                    </label>
                    <input type="email" class="input-text" name="email" id="reg_email" 
                           placeholder="<?php esc_attr_e( 'Dirección de correo electrónico', 'woocommerce' ); ?>"
                           autocomplete="email" 
                           data-required="true"
                           data-email="true"
                           value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>">
                </p>
                
                <!-- Campo de contraseña -->
                <p class="form-row form-row-wide">
                    <label for="reg_password">
                        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/icons/Password.svg' ); ?>" alt="Contraseña" width="20" height="20">
                    </label>
                    <span class="password-wrapper">
                        <input type="password" class="input-text" name="password" id="reg_password" 
                               placeholder="<?php esc_attr_e( 'Contraseña', 'woocommerce' ); ?>"
                               autocomplete="new-password"
                               data-required="true"
                               data-minlength="6">
                        <button type="button" class="toggle-password" data-target="reg_password">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="eye-icon eye-closed">
                                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="#666"/>
                            </svg>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="eye-icon eye-open" style="display: none;">
                                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z" fill="#666"/>
                                <path d="M3 3L21 21" stroke="#666" stroke-width="2" stroke-linecap="round"/>
                                <path d="M8.5 15.5L6 18" stroke="#666" stroke-width="2" stroke-linecap="round"/>
                                <path d="M15.5 8.5L18 6" stroke="#666" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>
                    </span>
                </p>

                
                <!-- Campos para vendedor (Dokan) -->
                <div class="show_if_seller">
                    <div class="split-row">
                        <p class="form-row form-group">
                            <label for="first-name">
                                <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/icons/User.svg' ); ?>" alt="Nombre" width="20" height="20">
                            </label>
                            <input type="text" class="input-text form-control" name="fname" id="first-name" 
                                   placeholder="<?php esc_attr_e( 'Nombre', 'dokan-lite' ); ?>" value="<?php echo ( ! empty( $_POST['fname'] ) ) ? esc_attr( $_POST['fname'] ) : ''; ?>">
                        </p>
                        <p class="form-row form-group">
                            <label for="last-name">
                                <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/icons/User.svg' ); ?>" alt="Apellidos" width="20" height="20">
                            </label>
                            <input type="text" class="input-text form-control" name="lname" id="last-name" 
                                   placeholder="<?php esc_attr_e( 'Apellidos', 'dokan-lite' ); ?>" value="<?php echo ( ! empty( $_POST['lname'] ) ) ? esc_attr( $_POST['lname'] ) : ''; ?>">
                        </p>
                    </div>
                    
                    <p class="form-row form-group form-row-wide">
                        <label for="company-name">
                            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/icons/Shop.svg' ); ?>" alt="Nombre de la tienda" width="20" height="20">
                        </label>
                        <input type="text" class="input-text form-control" name="shopname" id="company-name" 
                               placeholder="<?php esc_attr_e( 'Nombre de la tienda', 'dokan-lite' ); ?>" value="<?php echo ( ! empty( $_POST['shopname'] ) ) ? esc_attr( $_POST['shopname'] ) : ''; ?>">
                    </p>
                    
                    <p class="form-row form-group form-row-wide">
                        <label for="seller-url">
                            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/icons/Link.svg' ); ?>" alt="URL de la tienda" width="20" height="20">
                        </label>
                        <div class="store-url-wrapper">
                            <span class="store-url-prefix"><?php echo esc_url( home_url( '/tienda/' ) ); ?></span>
                            <input type="text" class="input-text form-control" name="shopurl" id="seller-url" 
                                   placeholder="<?php esc_attr_e( 'nombre-tienda', 'dokan-lite' ); ?>" value="<?php echo ( ! empty( $_POST['shopurl'] ) ) ? esc_attr( $_POST['shopurl'] ) : ''; ?>">
                        </div>
                        <div id="url-alart-mgs" class="pull-right"></div>
                        <small><strong id="url-alart"></strong></small>
                    </p>
                    
                    <p class="form-row form-group form-row-wide">
                        <label for="shop-phone">
                            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/icons/Phone Rounded.svg' ); ?>" alt="Teléfono" width="20" height="20">
                        </label>
<input type="tel" class="input-text form-control" name="phone" id="shop-phone" 
                               placeholder="<?php esc_attr_e( 'Número de teléfono', 'dokan-lite' ); ?>" value="<?php echo ( ! empty( $_POST['phone'] ) ) ? esc_attr( $_POST['phone'] ) : ''; ?>"
                               data-required="true">
                        <div id="phone-error" class="error-message"></div>
                    </p>
                </div>
                
                <!-- Tipo de usuario -->
                <p class="form-row form-group user-role vendor-customer-registration">
                    <label class="radio">
                        <input type="radio" name="role" value="customer" <?php checked( isset( $_POST['role'] ) ? $_POST['role'] : 'customer', 'customer' ); ?> class="dokan-role-customer">
                        <?php esc_html_e( 'Soy un cliente', 'dokan-lite' ); ?>
                    </label>
                    <br>
                    <label class="radio">
                        <input type="radio" name="role" value="seller" <?php checked( isset( $_POST['role'] ) ? $_POST['role'] : '', 'seller' ); ?> class="dokan-role-seller">
                        <?php esc_html_e( 'Soy un vendedor', 'dokan-lite' ); ?>
                    </label>
                </p>
                
                <div class="woocommerce-privacy-policy-text">
                    <p><?php esc_html_e( 'Tus datos personales se utilizarán para mejorar tu experiencia en esta web descrito esto en nuestra', 'woocommerce' ); ?> 
                    <a href="<?php echo esc_url( get_privacy_policy_url() ); ?>" class="woocommerce-privacy-policy-link" target="_blank"><?php esc_html_e( 'política de privacidad', 'woocommerce' ); ?></a>.</p>
                </div>
                
                <p class="form-row">
                    <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
                    <button type="submit" class="woocommerce-Button button btn btn-second" name="register" value="<?php esc_attr_e( 'Registrarse', 'woocommerce' ); ?>">
                        <?php esc_html_e( 'Registrarse', 'woocommerce' ); ?>
                    </button>
                </p>
                
                <?php do_action( 'woocommerce_register_form_end' ); ?>
            </form>
            
            <?php
        }
        ?>

        <div class="login-link">
            <?php esc_html_e( '¿Ya tienes una cuenta?', 'islabeya' ); ?>
            <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ); ?>">
                <?php esc_html_e( 'Inicia sesión aquí', 'islabeya' ); ?>
            </a>
        </div>
    </div>
</div>

<?php
get_footer('fullheight');