<?php
/**
 * Customer Cancelled Order Email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-cancelled-order.php.
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
$color_error = '#e74c3c';

// Información de contacto
$whatsapp_url = 'https://web.whatsapp.com/send?phone=34602483604&text=Hola%20he%20tenido%20un%20problema%20con%20mi%20pedido';
$phone_number = '+34 602 483 604';

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s: Customer first name */ ?>

<!-- =========================================================
     ORDER CARD
========================================================= -->

<div class="order-card-wrapper">

<div class="order-card">

    <div class="greeting">
        Hola
        <span class="first-name">
            <?php echo esc_html( $order->get_billing_first_name() ); ?>
        </span>,
    </div>

    <p><?php esc_html_e( 'Tu pedido ha sido cancelado. Si tienes alguna pregunta, por favor contáctanos.', 'woocommerce' ); ?></p>

    <div class="order-meta">
        <div class="meta-row">
            <span class="meta-cell meta-label">
                ID del pedido:
            </span>
            <span class="meta-cell meta-value">
                <?php echo esc_html( $order->get_order_number() ); ?>
            </span>
        </div>

        <div class="meta-row">
            <span class="meta-cell meta-label">
                Fecha:
            </span>
            <span class="meta-cell meta-value">
                <?php echo esc_html( $order->get_date_created()->date_i18n( 'j \d\e F \d\e Y' ) ); ?>
            </span>
        </div>
    </div>

</div>

<!-- =========================================================
     STATUS
========================================================= -->

<div class="status-section">

    <div class="status-timeline">

        <div class="timeline-line"></div>

        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-icon active">
                    ✓
                </div>
                <div class="timeline-label active">
                    Recibido
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-icon inactive">
                    •
                </div>
                <div class="timeline-label inactive">
                    Procesando
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-icon cancelled">
                    ✕
                </div>
                <div class="timeline-label cancelled">
                    Cancelado
                </div>
            </div>
        </div>

    </div>

</div>

<!-- =========================================================
     ORDER DETAILS
========================================================= -->

<div class="details-section">

    <div class="details-list">
        <div class="detail-row">
            <span class="detail-cell detail-label">
                Precio final:
            </span>
            <span class="detail-cell detail-value">
                <?php echo $order->get_formatted_order_total(); ?>
            </span>
        </div>

        <div class="detail-row">
            <span class="detail-cell detail-label">
                Método de pago:
            </span>
            <span class="detail-cell detail-value">
                <?php echo esc_html( $order->get_payment_method_title() ); ?>
            </span>
        </div>

        <div class="detail-row">
            <span class="detail-cell detail-label">
                Estado:
            </span>
            <span class="detail-cell detail-value">
                <span class="status-badge status-badge-cancelled">
                    <?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
                </span>
            </span>
        </div>
    </div>

</div>

<!-- =========================================================
     BUTTON
========================================================= -->

<div class="button-wrapper">
    <a href="<?php echo esc_url( $whatsapp_url ); ?>" class="track-button">
        Contactar por WhatsApp
    </a>
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
       STATUS
    ========================================================= */

    .status-section {
        padding: 45px 40px 0 40px;
    }

    .status-timeline {
        position: relative;
        width: 100%;
    }

    .timeline-line {
        position: absolute;
        z-index: 0;
        top: 15px;
        left: 34px;
        right: 34px;
        height: 1px;
        background-color: #cccccc;
    }

    .timeline {
        position: relative;
        z-index: 1;
        width: 100%;
        display: flex;
        justify-content: space-between;
    }

    .timeline-item {
        width: 33.333%;
        text-align: center;
    }

    .timeline-icon {
        width: 31px;
        height: 31px;
        margin: 0 auto 9px auto;
        border-radius: 50%;
        box-sizing: border-box;
        text-align: center;
        font-size: 14px;
        line-height: 31px;
        font-weight: 700;
    }

    .timeline-icon.active {
        background-color: <?php echo $color_primary; ?>;
        color: #ffffff;
    }

    .timeline-icon.inactive {
        background-color: #f1f1f1;
        border: 1px solid #dddddd;
        color: #cfcfcf;
    }

    .timeline-icon.cancelled {
        background-color: <?php echo $color_error; ?>;
        color: #ffffff;
    }

    .timeline-label {
        font-size: 11px;
        line-height: 16px;
        font-weight: 700;
    }

    .timeline-label.active {
        color: #111111;
    }

    .timeline-label.inactive {
        color: #c6c6c6;
    }

    .timeline-label.cancelled {
        color: <?php echo $color_error; ?>;
    }

    /* =========================================================
       ORDER INFORMATION
    ========================================================= */

    .details-section {
        padding: 42px 40px 0 40px;
    }

    .details-list {
        width: 100%;
    }

    .detail-row {
        width: 100%;
        display: flex;
        justify-content: space-between;
    }

    .detail-cell {
        padding: 5px 0;
        font-size: 13px;
        line-height: 19px;
    }

    .detail-label {
        width: 48%;
        text-align: left;
        font-weight: 700;
    }

    .detail-value {
        width: 52%;
        text-align: right;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 9px;
        background-color: #a9f1d7;
        color: #0a9d70;
        border-radius: 5px;
        font-size: 10px;
        line-height: 13px;
        font-weight: 700;
    }

    .status-badge-cancelled {
        background-color: #f8d7da;
        color: #721c24;
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
        background-color: <?php echo $color_error; ?>;
        border-radius: 8px;
        color: #ffffff !important;
        font-size: 16px;
        line-height: 21px;
        font-weight: 700;
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

        .status-section,
        .details-section {
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

        .status-section,
        .details-section {
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
