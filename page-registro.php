<?php
/**
 * Template Name: Registro de Clientes IslaBeya
 *
 * @package IslaBeya
 */

$get_posted_value = static function( $key ) {
    if ( ! isset( $_POST[ $key ] ) || ! is_scalar( $_POST[ $key ] ) ) {
        return '';
    }

    return wp_unslash( (string) $_POST[ $key ] );
};

$selected_role = $get_posted_value( 'role' );
if ( ! in_array( $selected_role, array( 'customer', 'seller' ), true ) ) {
    $selected_role = 'customer';
}

if ( ! is_user_logged_in() && isset( $_POST['register'] ) ) {
    $nonce = sanitize_text_field( $get_posted_value( 'woocommerce-register-nonce' ) );

    if ( ! $nonce || ! wp_verify_nonce( $nonce, 'woocommerce-register' ) ) {
        wc_add_notice( __( 'El formulario expiró. Recarga la página e inténtalo de nuevo.', 'woocommerce' ), 'error' );
    } else {
        $email              = sanitize_email( $get_posted_value( 'email' ) );
        $password           = $get_posted_value( 'password' );
        $password_confirm   = $get_posted_value( 'password_confirm' );
        $role               = sanitize_key( $get_posted_value( 'role' ) ?: 'customer' );
        $first_name         = sanitize_text_field( $get_posted_value( 'fname' ) );
        $last_name          = sanitize_text_field( $get_posted_value( 'lname' ) );
        $store_name         = sanitize_text_field( $get_posted_value( 'shopname' ) );
        $store_url          = sanitize_title( $get_posted_value( 'shopurl' ) );
        $phone              = sanitize_text_field( $get_posted_value( 'phone' ) );
        $errors             = array();

        if ( '' === $email ) {
            $errors[] = __( 'Escribe tu correo electrónico.', 'woocommerce' );
        } elseif ( ! is_email( $email ) ) {
            $errors[] = __( 'El correo electrónico no tiene un formato válido.', 'woocommerce' );
        } elseif ( email_exists( $email ) || username_exists( $email ) ) {
            $errors[] = __( 'Ya existe una cuenta con ese correo electrónico.', 'woocommerce' );
        }

        if ( ! in_array( $role, array( 'customer', 'seller' ), true ) ) {
            $errors[] = __( 'Selecciona un tipo de cuenta válido.', 'woocommerce' );
        }

        $errors = array_merge( $errors, islabeya_password_policy_errors( $password ) );

        if ( '' === $password_confirm ) {
            $errors[] = __( 'Confirma tu contraseña.', 'woocommerce' );
        } elseif ( $password !== $password_confirm ) {
            $errors[] = __( 'Las contraseñas no coinciden.', 'woocommerce' );
        }

        if ( 'seller' === $role ) {
            if ( '' === $first_name ) {
                $errors[] = __( 'Escribe el nombre del vendedor.', 'woocommerce' );
            }
            if ( '' === $last_name ) {
                $errors[] = __( 'Escribe los apellidos del vendedor.', 'woocommerce' );
            }
            if ( '' === $store_name ) {
                $errors[] = __( 'Escribe el nombre de la tienda.', 'woocommerce' );
            }
            if ( '' === $store_url ) {
                $errors[] = __( 'Escribe una dirección válida para la tienda.', 'woocommerce' );
            } else {
                $existing_store = get_users(
                    array(
                        'meta_key'   => 'shop_url',
                        'meta_value' => $store_url,
                        'fields'     => 'ids',
                        'number'     => 1,
                    )
                );
                if ( ! empty( $existing_store ) ) {
                    $errors[] = __( 'Esa dirección de tienda ya está en uso. Elige otra.', 'woocommerce' );
                }
            }
            if ( '' === $phone ) {
                $errors[] = __( 'Escribe el teléfono del vendedor.', 'woocommerce' );
            } elseif ( ! preg_match( '/^[+\d\s().-]+$/', $phone ) || strlen( preg_replace( '/\D+/', '', $phone ) ) < 7 || strlen( preg_replace( '/\D+/', '', $phone ) ) > 15 ) {
                $errors[] = __( 'Escribe un teléfono válido de 7 a 15 dígitos.', 'woocommerce' );
            }
            if ( ! function_exists( 'dokan_get_store_info' ) || ! function_exists( 'dokan_get_navigation_url' ) || ! get_role( 'seller' ) ) {
                $errors[] = __( 'El registro de vendedores no está disponible ahora. Inténtalo más tarde.', 'woocommerce' );
            }
        }

        if ( ! empty( $errors ) ) {
            foreach ( $errors as $error ) {
                wc_add_notice( $error, 'error' );
            }
        } else {
            $user_id = wp_create_user( $email, $password, $email );

            if ( is_wp_error( $user_id ) ) {
                if ( in_array( $user_id->get_error_code(), array( 'existing_user_login', 'existing_user_email' ), true ) ) {
                    wc_add_notice( __( 'Ya existe una cuenta con ese correo electrónico.', 'woocommerce' ), 'error' );
                } else {
                    wc_add_notice( __( 'No se pudo crear la cuenta. Revisa los datos e inténtalo de nuevo.', 'woocommerce' ), 'error' );
                }
            } else {
                $user = new WP_User( $user_id );
                $user->set_role( $role );

                if ( 'seller' === $role ) {
                    $store_info = array(
                        'store_name' => $store_name,
                        'address'    => array(),
                        'phone'      => $phone,
                        'banner'     => 0,
                    );
                    update_user_meta( $user_id, 'dokan_profile_settings', $store_info );
                    update_user_meta( $user_id, 'dokan_store_name', $store_name );
                    update_user_meta( $user_id, 'shop_url', $store_url );
                    update_user_meta( $user_id, 'first_name', $first_name );
                    update_user_meta( $user_id, 'last_name', $last_name );
                    update_user_meta( $user_id, 'billing_phone', $phone );
                    do_action( 'dokan_new_seller_created', $user_id, $store_info );
                } else {
                    update_user_meta( $user_id, 'first_name', $first_name );
                    update_user_meta( $user_id, 'last_name', $last_name );
                    update_user_meta( $user_id, 'billing_phone', $phone );
                }

                wp_set_current_user( $user_id );
                wp_set_auth_cookie( $user_id );
                wp_safe_redirect( home_url( '/' ) );
                exit;
            }
        }
    }
}

get_header( 'fullheight' );
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
            ?>
            <div class="auth-notices" role="alert" aria-live="assertive">
                <?php wc_print_notices(); ?>
            </div>
            
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
                              required aria-required="true"
                           data-required="true"
                           data-email="true"
                           value="<?php echo esc_attr( $get_posted_value( 'email' ) ); ?>">
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
                               required aria-required="true"
                               data-required="true"
                               data-minlength="8"
                               data-password-policy
                               aria-describedby="password-requirements">
                           <button type="button" class="toggle-password" data-target="reg_password"
                                aria-label="<?php esc_attr_e( 'Mostrar contraseña', 'woocommerce' ); ?>"
                                data-show-label="<?php esc_attr_e( 'Mostrar contraseña', 'woocommerce' ); ?>"
                                data-hide-label="<?php esc_attr_e( 'Ocultar contraseña', 'woocommerce' ); ?>"
                                aria-pressed="false">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="eye-icon eye-closed">
                                    <path d="M12 8.25C9.92893 8.25 8.25 9.92893 8.25 12C8.25 14.0711 9.92893 15.75 12 15.75C14.0711 15.75 15.75 14.0711 15.75 12C15.75 9.92893 14.0711 8.25 12 8.25ZM9.75 12C9.75 10.7574 10.7574 9.75 12 9.75C13.2426 9.75 14.25 10.7574 14.25 12C14.25 13.2426 13.2426 14.25 12 14.25C10.7574 14.25 9.75 13.2426 9.75 12Z
                                        M12 3.25C7.48587 3.25 4.44529 5.9542 2.68057 8.24686L2.64874 8.2882C2.24964 8.80653 1.88206 9.28392 1.63269 9.8484C1.36564 10.4529 1.25 11.1117 1.25 12C1.25 12.8883 1.36564 13.5471 1.63269 14.1516C1.88206 14.7161 2.24964 15.1935 2.64875 15.7118L2.68057 15.7531C4.44529 18.0458 7.48587 20.75 12 20.75C16.5141 20.75 19.5547 18.0458 21.3194 15.7531L21.3512 15.7118C21.7504 15.1935 22.1179 14.7161 22.3673 14.1516C22.6344 13.5471 22.75 12.8883 22.75 12C22.75 11.1117 22.6344 10.4529 22.3673 9.8484C22.1179 9.28391 21.7504 8.80652 21.3512 8.28818L21.3194 8.24686C19.5547 5.9542 16.5141 3.25 12 3.25ZM3.86922 9.1618C5.49864 7.04492 8.15036 4.75 12 4.75C15.8496 4.75 18.5014 7.04492 20.1308 9.1618C20.5694 9.73159 20.8263 10.0721 20.9952 10.4545C21.1532 10.812 21.25 11.2489 21.25 12C21.25 12.7511 21.1532 13.188 20.9952 13.5455C20.8263 13.9279 20.5694 14.2684 20.1308 14.8382C18.5014 16.9551 15.8496 19.25 12 19.25C8.15036 19.25 5.49864 16.9551 3.86922 14.8382C3.43064 14.2684 3.17374 13.9279 3.00476 13.5455C2.84684 13.188 2.75 12.7511 2.75 12C2.75 11.2489 2.84684 10.812 3.00476 10.4545C3.17374 10.0721 3.43063 9.73159 3.86922 9.1618Z" fill="#666"/>
                                </svg>
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="eye-icon eye-open" style="display: none;">
                                    <path d="M22.2954 6.31083C22.6761 6.474 22.8524 6.91491 22.6893 7.29563L21.9999 7.00019C22.6893 7.29563 22.6894 7.29546 22.6893 7.29563L22.6886 7.29731L22.6875 7.2998L22.6843 7.30716L22.6736 7.33123C22.6646 7.35137 22.6518 7.37958 22.6352 7.41527C22.6019 7.48662 22.5533 7.58794 22.4888 7.71435C22.3599 7.967 22.1675 8.32087 21.9084 8.73666C21.4828 9.4197 20.8724 10.2778 20.0619 11.1304L21.0303 12.0987C21.3231 12.3916 21.3231 12.8665 21.0303 13.1594C20.7374 13.4523 20.2625 13.4523 19.9696 13.1594L18.969 12.1588C18.3093 12.7115 17.5528 13.2302 16.695 13.6564L17.6286 15.0912C17.8545 15.4383 17.7562 15.9029 17.409 16.1288C17.0618 16.3547 16.5972 16.2564 16.3713 15.9092L15.2821 14.2353C14.5028 14.4898 13.659 14.6628 12.7499 14.7248V16.5002C12.7499 16.9144 12.4141 17.2502 11.9999 17.2502C11.5857 17.2502 11.2499 16.9144 11.2499 16.5002V14.7248C10.3689 14.6647 9.54909 14.5004 8.78982 14.2586L7.71575 15.9093C7.48984 16.2565 7.02526 16.3548 6.67807 16.1289C6.33089 15.903 6.23257 15.4384 6.45847 15.0912L7.37089 13.689C6.5065 13.2668 5.74381 12.7504 5.07842 12.1984L4.11744 13.1594C3.82455 13.4523 3.34968 13.4523 3.05678 13.1594C2.76389 12.8665 2.76389 12.3917 3.05678 12.0988L3.98055 11.175C3.15599 10.3153 2.53525 9.44675 2.10277 8.75486C1.83984 8.33423 1.6446 7.97584 1.51388 7.71988C1.44848 7.59182 1.3991 7.48914 1.36537 7.41683C1.3485 7.38067 1.33553 7.35207 1.32641 7.33167L1.31562 7.30729L1.31238 7.29984L1.31129 7.29733L1.31088 7.29638C1.31081 7.2962 1.31056 7.29563 1.99992 7.00019L1.31088 7.29638C1.14772 6.91565 1.32376 6.474 1.70448 6.31083C2.08489 6.1478 2.52539 6.32374 2.68888 6.70381C2.68882 6.70368 2.68894 6.70394 2.68888 6.70381L2.68983 6.706L2.69591 6.71972C2.7018 6.73291 2.7114 6.7541 2.72472 6.78267C2.75139 6.83983 2.79296 6.92644 2.84976 7.03767C2.96345 7.26029 3.13762 7.58046 3.37472 7.95979C3.85033 8.72067 4.57157 9.70728 5.55561 10.6218C6.42151 11.4265 7.48259 12.1678 8.75165 12.656C9.70614 13.0232 10.7854 13.2502 11.9999 13.2502C13.2416 13.2502 14.342 13.013 15.3124 12.631C16.5738 12.1345 17.6277 11.3884 18.4866 10.5822C19.4562 9.67216 20.1668 8.69535 20.6354 7.9434C20.869 7.5685 21.0405 7.25246 21.1525 7.03286C21.2085 6.92315 21.2494 6.83776 21.2757 6.78144C21.2888 6.75328 21.2983 6.73242 21.3041 6.71943L21.31 6.70595L21.3106 6.70475C21.3105 6.70485 21.3106 6.70466 21.3106 6.70475M22.2954 6.31083C21.9147 6.14771 21.4738 6.32423 21.3106 6.70475L22.2954 6.31083ZM2.68888 6.70381C2.68882 6.70368 2.68894 6.70394 2.68888 6.70381V6.70381Z" fill="#666"/>
                                </svg>
                        </button>
                    </span>
                </p>
                <ul class="password-requirements" id="password-requirements" aria-label="<?php esc_attr_e( 'Requisitos de la contraseña', 'woocommerce' ); ?>">
                    <li data-password-rule="length">Al menos 8 caracteres</li>
                    <li data-password-rule="uppercase">Una letra mayúscula</li>
                    <li data-password-rule="lowercase">Una letra minúscula</li>
                    <li data-password-rule="number">Un número</li>
                    <li data-password-rule="symbol">Un símbolo</li>
                </ul>
                <p class="password-feedback" id="password-feedback" data-password-feedback aria-live="polite"></p>

                <p class="form-row form-row-wide">
                    <label for="reg_password_confirm">
                        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/icons/Password.svg' ); ?>" alt="Confirmar contraseña" width="20" height="20">
                    </label>
                    <input type="password" class="input-text" name="password_confirm" id="reg_password_confirm"
                           placeholder="<?php esc_attr_e( 'Confirmar contraseña', 'woocommerce' ); ?>"
                           autocomplete="new-password" required aria-required="true"
                           data-required="true" data-password-confirm data-match="#reg_password">
                </p>

                
                <!-- Campos para vendedor (Dokan) -->
                <div class="show_if_seller">
                    <div class="split-row">
                        <p class="form-row form-group">
                            <label for="first-name">
                                <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/icons/User.svg' ); ?>" alt="Nombre" width="20" height="20">
                            </label>
                            <input type="text" class="input-text form-control" name="fname" id="first-name" 
                                   placeholder="<?php esc_attr_e( 'Nombre', 'dokan-lite' ); ?>" data-required="true" value="<?php echo esc_attr( $get_posted_value( 'fname' ) ); ?>">
                        </p>
                        <p class="form-row form-group">
                            <label for="last-name">
                                <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/icons/User.svg' ); ?>" alt="Apellidos" width="20" height="20">
                            </label>
                            <input type="text" class="input-text form-control" name="lname" id="last-name" 
                                   placeholder="<?php esc_attr_e( 'Apellidos', 'dokan-lite' ); ?>" data-required="true" value="<?php echo esc_attr( $get_posted_value( 'lname' ) ); ?>">
                        </p>
                    </div>
                    
                    <p class="form-row form-group form-row-wide">
                        <label for="company-name">
                            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/icons/Shop.svg' ); ?>" alt="Nombre de la tienda" width="20" height="20">
                        </label>
                        <input type="text" class="input-text form-control" name="shopname" id="company-name" 
                               placeholder="<?php esc_attr_e( 'Nombre de la tienda', 'dokan-lite' ); ?>" data-required="true" value="<?php echo esc_attr( $get_posted_value( 'shopname' ) ); ?>">
                    </p>
                    
                    <div class="form-row form-group form-row-wide store-url-row">
                        <label for="seller-url">
                            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/icons/Link.svg' ); ?>" alt="URL de la tienda" width="20" height="20">
                        </label>
                        <div class="store-url-control">
                            <span class="store-url-prefix" id="store-url-prefix"><?php echo esc_url( home_url( '/tienda/' ) ); ?></span>
                            <input type="text" class="input-text form-control" name="shopurl" id="seller-url"
                                   placeholder="<?php esc_attr_e( 'nombre-tienda', 'dokan-lite' ); ?>" data-required="true" aria-describedby="store-url-prefix" value="<?php echo esc_attr( $get_posted_value( 'shopurl' ) ); ?>">
                            <div id="url-alart-mgs" class="pull-right"></div>
                            <small><strong id="url-alart"></strong></small>
                        </div>
                    </div>
                    
                    <div class="form-row form-group form-row-wide seller-phone-row">
                        <label for="shop-phone">
                            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/icons/Phone Rounded.svg' ); ?>" alt="Teléfono" width="20" height="20">
                        </label>
                        <input type="tel" class="input-text form-control" name="phone" id="shop-phone"
                               placeholder="<?php esc_attr_e( 'Número de teléfono', 'dokan-lite' ); ?>" value="<?php echo esc_attr( $get_posted_value( 'phone' ) ); ?>"
                               required data-required="true">
                        <span id="phone-error" class="phone-error-msg" role="alert" aria-live="polite"></span>
                    </div>
                </div>
                
                <!-- Tipo de usuario -->
                <p class="form-row form-group user-role vendor-customer-registration">
                    <label class="radio">
                        <input type="radio" name="role" value="customer" <?php checked( $selected_role, 'customer' ); ?> class="dokan-role-customer">
                        <?php esc_html_e( 'Soy un cliente', 'dokan-lite' ); ?>
                    </label>
                    <br>
                    <label class="radio">
                        <input type="radio" name="role" value="seller" <?php checked( $selected_role, 'seller' ); ?> class="dokan-role-seller">
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