<?php
/**
 * Editar cuenta - IslaBeya
 * 
 * @package IslaBeya
 */

defined( 'ABSPATH' ) || exit;

$user = wp_get_current_user();
?>

<div class="islabeya-edit-account-container">
    <h2 class="section-title"><?php esc_html_e( 'Detalles de mi cuenta', 'islabeya' ); ?></h2>
    
    <form method="post" class="woocommerce-form woocommerce-form-edit-account isla-form">
        <div class="form-row form-row-wide">
            <label for="account_first_name"><?php esc_html_e( 'Nombre', 'woocommerce' ); ?> <span class="required">*</span></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_first_name" id="account_first_name" value="<?php echo esc_attr( $user->first_name ); ?>" data-required="true" />
        </div>
        
        <div class="form-row form-row-wide">
            <label for="account_last_name"><?php esc_html_e( 'Apellidos', 'woocommerce' ); ?> <span class="required">*</span></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_last_name" id="account_last_name" value="<?php echo esc_attr( $user->last_name ); ?>" data-required="true" />
        </div>
        
        <div class="form-row form-row-wide">
            <label for="account_display_name"><?php esc_html_e( 'Nombre público', 'woocommerce' ); ?> <span class="required">*</span></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_display_name" id="account_display_name" value="<?php echo esc_attr( $user->display_name ); ?>" data-required="true" />
            <span class="description"><?php esc_html_e( 'Este nombre se mostrará públicamente.', 'woocommerce' ); ?></span>
        </div>
        
        <div class="form-row form-row-wide">
            <label for="account_email"><?php esc_html_e( 'Correo electrónico', 'woocommerce' ); ?> <span class="required">*</span></label>
            <input type="email" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" value="<?php echo esc_attr( $user->user_email ); ?>" data-required="true" data-email="true" />
        </div>
        
        <fieldset class="password-fieldset">
            <legend><?php esc_html_e( 'Cambiar contraseña', 'woocommerce' ); ?></legend>
            
            <div class="form-row form-row-wide">
                <label for="password_current"><?php esc_html_e( 'Contraseña actual (dejar en blanco para no cambiar)', 'woocommerce' ); ?></label>
                <input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_current" id="password_current" />
            </div>
            
            <div class="form-row form-row-wide">
                <label for="password_1"><?php esc_html_e( 'Nueva contraseña (dejar en blanco para no cambiar)', 'woocommerce' ); ?></label>
                <input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_1" id="password_1" data-minlength="8" />
            </div>
            
            <div class="form-row form-row-wide">
                <label for="password_2"><?php esc_html_e( 'Confirmar nueva contraseña', 'woocommerce' ); ?></label>
                <input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_2" id="password_2" data-match="#password_1" />
            </div>
        </fieldset>
        
        <div class="form-row">
            <?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
            <button type="submit" class="woocommerce-Button button btn btn-primary" name="save_account_details" value="Guardar cambios">
                <?php esc_html_e( 'Guardar cambios', 'woocommerce' ); ?>
            </button>
        </div>
    </form>
</div>