<?php
/**
 * Customer Invoice Email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-invoice.php.
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

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s: Customer first name */ ?>
<p><?php printf( esc_html__( 'Hola %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) ); ?></p>
<p><?php esc_html_e( 'Aquí está tu factura. Gracias por tu compra en IslaBeya.', 'woocommerce' ); ?></p>

<!-- =========================================================
     ORDER CARD
========================================================= -->

<div class="order-card-wrapper">

<div class="order-card">

    <div class="greeting">
        Factura
        <span class="first-name">
            #<?php echo esc_html( $order->get_order_number() ); ?>
        </span>
    </div>

    <div class="order-meta">
        <div class="meta-row">
            <span class="meta-cell meta-label">
                Fecha:
            </span>
            <span class="meta-cell meta-value">
                <?php echo esc_html( $order->get_date_created()->date_i18n( 'j \d\e F \d\e Y' ) ); ?>
            </span>
        </div>

        <div class="meta-row">
            <span class="meta-cell meta-label">
                Estado:
            </span>
            <span class="meta-cell meta-value">
                <?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
            </span>
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
                Método de pago:
            </span>
            <span class="detail-cell detail-value">
                <?php echo esc_html( $order->get_payment_method_title() ); ?>
            </span>
        </div>
    </div>

</div>

<!-- =========================================================
     BILLING / SHIPPING ADDRESSES
========================================================= -->

<div class="address-section">

    <h2 class="section-title">
        Información de facturación
    </h2>

    <!-- BILLING ADDRESS -->
    <div class="address-card">
        <div class="address-heading">
            Dirección de facturación
        </div>

        <div class="address-name">
            <?php echo esc_html( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ); ?>
        </div>

        <div class="address-text">
            <?php
            if ( $order->get_billing_address_1() ) {
                echo esc_html( $order->get_billing_address_1() ) . '<br>';
            }
            if ( $order->get_billing_address_2() ) {
                echo esc_html( $order->get_billing_address_2() ) . '<br>';
            }
            if ( $order->get_billing_city() ) {
                echo esc_html( $order->get_billing_city() ) . ', ';
            }
            if ( $order->get_billing_state() ) {
                echo esc_html( WC()->countries->get_states( $order->get_billing_country() )[ $order->get_billing_state() ] ) . '<br>';
            }
            if ( $order->get_billing_country() ) {
                echo esc_html( WC()->countries->get_countries()[ $order->get_billing_country() ] ) . '<br>';
            }
            if ( $order->get_billing_postcode() ) {
                echo esc_html( 'CP ' . $order->get_billing_postcode() );
            }
            ?>
        </div>

        <div class="address-contact">
            <?php if ( $order->get_billing_phone() ) : ?>
                Tel: <?php echo esc_html( $order->get_billing_phone() ); ?><br>
            <?php endif; ?>
            <?php if ( $order->get_billing_email() ) : ?>
                Email: <?php echo esc_html( $order->get_billing_email() ); ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if ( $order->get_shipping_address_1() ) : ?>
    <!-- SHIPPING ADDRESS -->
    <div class="address-card">
        <div class="address-heading">
            Dirección de envío
        </div>

        <div class="address-name">
            <?php echo esc_html( $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name() ); ?>
        </div>

        <div class="address-text">
            <?php
            if ( $order->get_shipping_address_1() ) {
                echo esc_html( $order->get_shipping_address_1() ) . '<br>';
            }
            if ( $order->get_shipping_address_2() ) {
                echo esc_html( $order->get_shipping_address_2() ) . '<br>';
            }
            if ( $order->get_shipping_city() ) {
                echo esc_html( $order->get_shipping_city() ) . ', ';
            }
            if ( $order->get_shipping_state() ) {
                echo esc_html( WC()->countries->get_states( $order->get_shipping_country() )[ $order->get_shipping_state() ] ) . '<br>';
            }
            if ( $order->get_shipping_country() ) {
                echo esc_html( WC()->countries->get_countries()[ $order->get_shipping_country() ] ) . '<br>';
            }
            if ( $order->get_shipping_postcode() ) {
                echo esc_html( 'CP ' . $order->get_shipping_postcode() );
            }
            ?>
        </div>
    </div>
    <?php endif; ?>

</div>

<!-- =========================================================
     PRODUCTS
========================================================= -->

<div class="products-section">

    <!-- PRODUCTOS LISTA CON NUEVA ESTRUCTURA -->
    <?php echo islabeya_email_render_products_list( $order ); ?>

    <!-- TOTALS -->
    <div class="totals">
        <?php
        foreach ( $order->get_order_item_totals() as $key => $total ) {
            ?>
            <div class="total-row">
                <span class="total-cell total-label">
                    <?php echo esc_html( $total['label'] ); ?>
                </span>
                <span class="total-cell total-value">
                    <?php echo $total['value']; ?>
                </span>
            </div>
            <?php
        }
        ?>
        <div class="total-row final-total">
            <span class="total-cell total-label">
                Total
            </span>
            <span class="total-cell total-value">
                <?php echo $order->get_formatted_order_total(); ?>
            </span>
        </div>
    </div>

</div>

<!-- =========================================================
     BUTTON
========================================================= -->

<div class="button-wrapper">
    <a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" class="track-button">
        Ver factura
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

    /* =========================================================
       ADDRESSES
    ========================================================= */

    .address-section {
        padding: 38px 40px 0 40px;
    }

    .section-title {
        margin: 0 0 17px 0;
        font-size: 17px;
        line-height: 23px;
        font-weight: 700;
    }

    .address-card { 
        margin-bottom: 15px;
        background-color: #ffffff;
        border: 1px solid #e9e9e9;
        border-radius: 10px;
        padding: 17px 18px;
    }

    .address-heading {
        padding-bottom: 8px;
        font-size: 12px;
        line-height: 16px;
        font-weight: 700;
        color: <?php echo $color_primary; ?>;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .address-name {
        padding-top: 0;
        padding-bottom: 3px;
        font-size: 13px;
        line-height: 19px;
        font-weight: 700;
    }

    .address-text {
        padding-top: 0;
        font-size: 12px;
        line-height: 19px;
        color: #555555;
    }

    .address-contact {
        padding-top: 7px;
        font-size: 11px;
        line-height: 17px;
        color: #777777;
    }

    /* =========================================================
       PRODUCTS
    ========================================================= */

    .products-section {
        padding: 38px 40px 0 40px;
    }

    .products-table {
        width: 100%;
        background-color: #ffffff;
        border-radius: 7px;
        overflow: hidden;
    }

    .products-header {
        display: flex;
        border-bottom: 1px solid #eeeeee;
    }

    .products-header-item {
        padding: 11px 9px;
        font-size: 10px;
        line-height: 15px;
        font-weight: 700;
        text-align: left;
    }

    .products-header-item:nth-child(2) {
        text-align: center;
    }

    .products-header-item:nth-child(3) {
        text-align: left;
    }

    .products-header-item:last-child {
        text-align: right;
    }

    .products-body {
        display: flex;
        flex-direction: column;
    }

    .product-row {
        display: flex;
        border-bottom: 1px solid #eeeeee;
    }

    .product-cell {
        padding: 12px 9px;
        font-size: 10px;
        line-height: 15px;
        color: #222222;
    }

    .product-cell:nth-child(2) {
        text-align: center;
        color: #777777;
    }

    .product-cell:nth-child(3) {
        color: #666666;
    }

    .product-cell:last-child {
        text-align: right;
        color: #555555;
        white-space: nowrap;
    }

    /* =========================================================
       TOTALS
    ========================================================= */

    .totals {
        width: 100%;
        padding-top: 16px;
    }

    .total-row {
        width: 100%;
        display: flex;
        justify-content: space-between;
    }

    .total-cell {
        padding: 5px 9px;
        font-size: 12px;
        line-height: 18px;
    }

    .total-label {
        text-align: left;
        font-weight: 700;
    }

    .total-value {
        text-align: right;
    }

    .discount-value {
        color: #12a66d;
    }

    .final-total {
        margin-top: 17px;
        padding-top: 16px;
        border-top: 1px solid #cccccc;
    }

    .final-total .total-cell {
        font-size: 14px;
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
        .address-section,
        .products-section {
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
        .address-section,
        .products-section {
            padding-left: 19px;
            padding-right: 19px;
        }

        .products-header-item,
        .product-cell {
            padding-left: 5px;
            padding-right: 5px;
            font-size: 9px;
        }

        .address-card {
            padding-left: 14px;
            padding-right: 14px;
        }

    }

    /* =========================================================
       PRODUCTS LIST (NEW STRUCTURE)
    ========================================================= */

    .products-list-container {
        width: 100%;
    }

    .delivery-group {
        margin-bottom: 20px;
    }

    .group-header {
        background-color: #f5f5f5;
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 12px;
    }

    .group-label {
        font-size: 13px;
        font-weight: 700;
        color: #000;
        margin-bottom: 4px;
    }

    .group-address {
        font-size: 11px;
        color: #6b7280;
    }

    .product-list-item {
        display: flex;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #eeeeee;
    }

    .product-list-item:last-child {
        border-bottom: none;
    }

    .product-image {
        flex-shrink: 0;
        margin-right: 12px;
    }

    .product-details {
        flex-grow: 1;
        min-width: 0;
    }

    .product-name {
        font-size: 13px;
        font-weight: 600;
        color: #000;
        margin-bottom: 4px;
    }

    .product-variations {
        font-size: 11px;
        color: #6b7280;
        margin-bottom: 6px;
    }

    .product-variations .variation {
        display: inline-block;
        margin-right: 8px;
    }

    .product-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
    }

    .vendor-name {
        color: #6b7280;
    }

    .shipping-type {
        color: #15ad3c;
        font-weight: 600;
    }

    .product-qty-price {
        flex-shrink: 0;
        text-align: right;
        margin-left: 12px;
    }

    .quantity {
        font-size: 12px;
        color: #6b7280;
        margin-bottom: 4px;
    }

    .price {
        font-size: 14px;
        font-weight: 700;
        color: #000;
    }

    @media only screen and (max-width: 520px) {
        .product-list-item {
            flex-wrap: wrap;
        }

        .product-qty-price {
            width: 100%;
            text-align: left;
            margin-left: 0;
            margin-top: 8px;
            display: flex;
            justify-content: space-between;
        }
    }

</style>

<?php
/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
