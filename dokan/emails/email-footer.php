<?php
/**
 * Email Footer for Dokan
 *
 * @package Tema IslaBeya
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Colores de marca IslaBeya
$color_primary = '#15ad3c';
$color_tertiary = '#004D35';

// Información de contacto
$whatsapp_url = 'https://web.whatsapp.com/send?phone=34602483604&text=Hola%20quiero%20hablar%20con%20ustedes';
$phone_number = '+34 602 483 604';
$email_contact = 'info@islabeya.com';

// Redes sociales
$instagram_url = 'https://www.instagram.com/islabeya?igsh=M2xlZGxwZjY3bnNr&utm_source=qr';
$facebook_url = 'https://www.facebook.com/share/17sFxZoxnQ/?mibextid=wwXIfr';

?>

<!-- =========================================================
     FOOTER
========================================================= -->

<div class="footer">

    <div class="footer-logo">
        IslaBeya
    </div>

    <div class="social">
        <a href="<?php echo esc_url( $instagram_url ); ?>" style="color: #ffffff; text-decoration: none;">
            Instagram
        </a>
        <a href="<?php echo esc_url( $facebook_url ); ?>" style="color: #ffffff; text-decoration: none;">
            Facebook
        </a>
    </div>

    <div class="copyright">
        © 2026 islabeya.com - Diseño y Desarrollo por IslaBeya
    </div>

    <div class="contact-info">
        <a href="<?php echo esc_url( $whatsapp_url ); ?>" style="color: #ffffff; text-decoration: none;">
            Contactar por WhatsApp
        </a>
        <span style="color: #ffffff;">|</span>
        <a href="mailto:<?php echo esc_attr( $email_contact ); ?>" style="color: #ffffff; text-decoration: none;">
            <?php echo esc_html( $email_contact ); ?>
        </a>
    </div>

</div>

    </div>

</div>

</body>

</html>

<style>

    /* =========================================================
       FOOTER
    ========================================================= */

    .footer {
        background-color: #15ad3c;
        position: relative;
        padding: 48px 40px 42px 40px;
        text-align: center;
        margin-top: -20px;
    }

    .footer-logo {
        color: #ffffff;
        font-size: 20px;
        line-height: 25px;
        font-weight: 700;
        margin-bottom: 20px;
    }

    .social {
        display: inline-block;
        margin: 0 8px;
        color: #74788f;
        font-size: 17px;
        line-height: 20px;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .social a {
        margin: 0 10px;
        color: #ffffff !important;
        text-decoration: none;
    }

    .copyright {
        margin-bottom: 8px;
        color: #ffffff;
        font-size: 8px;
        line-height: 12px;
    }

    .contact-info {
        font-size: 8px;
        line-height: 12px;
        margin-top: 10px;
    }

    .contact-info a {
        color: #ffffff !important;
        text-decoration: none;
    }

    .contact-info span {
        padding: 0 5px;
        color: #ffffff;
    }

    /* =========================================================
       RESPONSIVE
    ========================================================= */

    @media only screen and (max-width: 520px) {

        .footer {
            padding-left: 24px;
            padding-right: 24px;
        }

    }

    @media only screen and (max-width: 380px) {

        .footer {
            padding-left: 19px;
            padding-right: 19px;
        }

    }

</style>
