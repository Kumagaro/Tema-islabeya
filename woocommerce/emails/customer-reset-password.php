<?php
/**
 * Customer Reset Password Email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-reset-password.php.
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

// Colores de marca IslaBeya
$color_primary = '#15ad3c';
$color_tertiary = '#004D35';
$color_secondary = '#f5f5f5';
$color_text = '#000';
$color_text_secondary = '#6b7280';

// Información de contacto
$whatsapp_url = 'https://web.whatsapp.com/send?phone=34602483604&text=Hola%20quiero%20hablar%20con%20ustedes';
$phone_number = '+34 602 483 604';

// Establecer el título del correo
$email_heading = 'Restablecer contraseña';
$email_description = 'Recibiste esta solicitud porque se inició un restablecimiento de contraseña para tu cuenta.';

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php esc_html_e( 'Hola,', 'woocommerce' ); ?></p>
<p><?php esc_html_e( 'Recibiste una solicitud para restablecer la contraseña de tu cuenta en IslaBeya. Si no solicitaste esto, puedes ignorar este correo.', 'woocommerce' ); ?></p>

<!-- =========================================================
     PASSWORD RESET CARD
========================================================= -->

<div class="order-card-wrapper">

<div class="order-card">

    <div class="greeting">
        Restablecer
        <span class="first-name">
            Contraseña
        </span>
    </div>

    <div class="order-meta">
        <div class="meta-row">
            <span class="meta-cell meta-label">
                Usuario:
            </span>
            <span class="meta-cell meta-value">
                <?php echo esc_html( $user_login ); ?>
            </span>
        </div>

        <div class="meta-row">
            <span class="meta-cell meta-label">
                Fecha:
            </span>
            <span class="meta-cell meta-value">
                <?php echo esc_html( date_i18n( 'j \d\e F \d\e Y' ) ); ?>
            </span>
        </div>
    </div>

</div>

<!-- =========================================================
     INSTRUCTIONS
========================================================= -->

<div class="details-section">

    <p class="instructions">
        <?php esc_html_e( 'Para restablecer tu contraseña, haz clic en el siguiente enlace. Este enlace expirará en 24 horas.', 'woocommerce' ); ?>
    </p>

</div>

<!-- =========================================================
     BUTTON
======================================================== -->

<div class="button-wrapper">
    <a href="<?php echo esc_url( add_query_arg( array( 'key' => $reset_key, 'login' => $user_login ), wc_get_endpoint_url( 'lost-password', '', wc_get_page_permalink( 'myaccount' ) ) ) ); ?>" class="track-button">
        Restablecer contraseña
    </a>
</div>

<!-- =========================================================
     SECURITY NOTICE
======================================================== -->

<div class="security-section">

    <p class="security-notice">
        <?php esc_html_e( 'Si no solicitaste este cambio, por favor ignora este correo. Tu contraseña permanecerá sin cambios.', 'woocommerce' ); ?>
    </p>

</div>

<style>

    /* =========================================================
       ORDER CARD
    ========================================================= */

    .order-card-wrapper {
        position: relative;
        z-index: 5;
        padding: 0 40px;
        margin-top: -35px;
    }

    .order-card {
        background-color: #ffffff;
        border-radius: 20px;
        padding: 34px 35px 36px 35px;
    }

    .greeting {
        margin: 0;
        text-align: center;
        font-size: 25px;
        line-height: 32px;
        font-weight: 700;
    }

    .first-name {
        color: <?php echo $color_primary; ?>;
        font-family: Georgia, "Times New Roman", serif;
        font-style: italic;
    }

    .order-meta {
        width: 100%;
        background-color: #f7f7f7;
        border-radius: 9px;
    }

    .meta-row {
        width: 100%;
        display: flex;
        justify-content: space-between;
    }

    .meta-row + .meta-row {
        border-top: 1px solid #dddddd;
    }

    .meta-cell {
        padding: 12px 10px;
        font-size: 13px;
        line-height: 19px;
    }

    .meta-label {
        width: 48%;
        font-weight: 700;
        text-align: left;
    }

    .meta-value {
        width: 52%;
        text-align: right;
        color: #333333;
    }

    /* =========================================================
       DETAILS SECTION
    ========================================================= */

    .details-section {
        padding: 42px 40px 0 40px;
    }

    .instructions {
        font-size: 13px;
        line-height: 19px;
        color: #555555;
        text-align: center;
    }

    /* =========================================================
       BUTTON
    ========================================================= */

    .button-wrapper {
        padding: 42px 0 48px 0;
        text-align: center;
    }

    .track-button {
        display: inline-block;
        padding: 14px 40px;
        background-color: <?php echo $color_primary; ?>;
        border-radius: 8px;
        color: #ffffff !important;
        font-size: 16px;
        line-height: 21px;
        font-weight: 700;
    }

    /* =========================================================
       SECURITY SECTION
    ========================================================= */

    .security-section {
        padding: 20px 40px 0 40px;
    }

    .security-notice {
        font-size: 11px;
        line-height: 16px;
        color: #777777;
        text-align: center;
        font-style: italic;
    }

    /* =========================================================
       RESPONSIVE
    ========================================================= */

    @media only screen and (max-width: 520px) {

        .order-card-wrapper {
            padding-left: 24px;
            padding-right: 24px;
        }

        .order-card {
            padding-left: 25px;
            padding-right: 25px;
        }

        .details-section,
        .security-section {
            padding-left: 24px;
            padding-right: 24px;
        }

    }

    @media only screen and (max-width: 380px) {

        .order-card {
            padding-left: 19px;
            padding-right: 19px;
        }

        .greeting {
            font-size: 23px;
        }

        .details-section,
        .security-section {
            padding-left: 19px;
            padding-right: 19px;
        }

    }

</style>

<?php
/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
