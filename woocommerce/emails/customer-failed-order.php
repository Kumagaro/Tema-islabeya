<?php
/**
 * Customer Failed Order Email — IslaBeya
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Paleta local (para estilos inline)
$color_primary         = '#15AD3C';
$color_button          = '#0E7A2B';
$color_card            = '#F3F8F4';
$color_border          = '#DCE8DF';
$color_text            = '#1F2A22';
$color_text_soft       = '#3E4A42';
$color_muted           = '#56635A';
$color_pill_off        = '#F3F4F6';
$color_pill_off_txt    = '#9CA3AF';
$color_error           = '#DC2626';
$color_error_bg        = '#FEE2E2';
$color_warn            = '#92400E';
$color_warn_bg         = '#FEF3C7';

// Datos del pedido
$order_number = $order->get_order_number();
$order_id     = $order->get_id();
$first_name   = $order->get_billing_first_name();
$date         = $order->get_date_created() ? $order->get_date_created()->date_i18n( 'j \d\e F \d\e Y' ) : '';
$total_html   = $order->get_formatted_order_total();
$payment      = $order->get_payment_method_title();
$status       = wc_get_order_status_name( $order->get_status() );
$retry_url    = $order->get_checkout_payment_url();

// WhatsApp con mensaje general
$whatsapp_url = 'https://web.whatsapp.com/send?phone=34602483604&text=' . rawurlencode( 'Hola quiero hablar con ustedes' );

/*
 * HEADER
 */
do_action( 'woocommerce_email_header', $email_heading, $email );
?>

    <!-- =========================================================
         HERO — 2 columnas
    ========================================================= -->
    <tr>
      <td class="px py-hero" style="padding:72px 80px;text-align:left;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>

          <!-- Columna izquierda -->
          <td class="col" width="56%" valign="middle" style="width:56%;vertical-align:middle;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td class="col-left-pad" style="padding-right:60px;font-family:Arial,Helvetica,sans-serif;color:<?php echo esc_attr( $color_text ); ?>;">

                  <p style="margin:0 0 12px 0;font-size:13px;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:<?php echo esc_attr( $color_warn ); ?>;font-family:Arial,Helvetica,sans-serif;">
                    Pedido #<?php echo esc_html( $order_number ); ?>
                  </p>

                  <h1 class="h1" style="margin:0 0 24px 0;font-size:48px;line-height:1.15;font-weight:600;color:<?php echo esc_attr( $color_text ); ?>;font-family:Arial,Helvetica,sans-serif;">
                    Hola,<br>
                    <span style="color:<?php echo esc_attr( $color_warn ); ?>;"><?php echo esc_html( $first_name ); ?></span>
                  </h1>

                  <p class="txt" style="margin:0 0 32px 0;font-size:18px;line-height:1.6;color:<?php echo esc_attr( $color_text_soft ); ?>;font-family:Arial,Helvetica,sans-serif;">
                    Hubo un problema al procesar tu pedido. Puedes reintentar el pago o contactarnos por WhatsApp si necesitas asistencia.
                  </p>

                  <!-- Botón principal: Reintentar pago -->
                  <table role="presentation" class="btn-full" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:12px;">
                    <tr>
                      <td align="center" bgcolor="<?php echo esc_attr( $color_button ); ?>" style="border-radius:12px;">
                        <!--[if mso]>
                        <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" href="<?php echo esc_url( $retry_url ); ?>" style="height:60px;v-text-anchor:middle;width:300px;" arcsize="20%" stroke="f" fillcolor="<?php echo esc_attr( $color_button ); ?>">
                          <w:anchorlock/><center style="color:#FFFFFF;font-family:Arial,sans-serif;font-size:18px;font-weight:bold;">Reintentar pago</center>
                        </v:roundrect>
                        <![endif]-->
                        <!--[if !mso]><!-->
                        <a href="<?php echo esc_url( $retry_url ); ?>" target="_blank" style="display:inline-block;padding:19px 48px;font-family:Arial,Helvetica,sans-serif;font-size:18px;font-weight:600;line-height:22px;color:#FFFFFF;text-decoration:none;border-radius:12px;background-color:<?php echo esc_attr( $color_button ); ?>;">Reintentar pago</a>
                        <!--<![endif]-->
                      </td>
                    </tr>
                  </table>

                  <!-- Botón secundario: WhatsApp -->
                  <table role="presentation" class="btn-full" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td align="center" bgcolor="<?php echo esc_attr( $color_error ); ?>" style="border-radius:12px;">
                        <!--[if mso]>
                        <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" href="<?php echo esc_url( $whatsapp_url ); ?>" style="height:60px;v-text-anchor:middle;width:300px;" arcsize="20%" stroke="f" fillcolor="<?php echo esc_attr( $color_error ); ?>">
                          <w:anchorlock/><center style="color:#FFFFFF;font-family:Arial,sans-serif;font-size:18px;font-weight:bold;">Contactar por WhatsApp</center>
                        </v:roundrect>
                        <![endif]-->
                        <!--[if !mso]><!-->
                        <a href="<?php echo esc_url( $whatsapp_url ); ?>" target="_blank" style="display:inline-block;padding:19px 48px;font-family:Arial,Helvetica,sans-serif;font-size:18px;font-weight:600;line-height:22px;color:#FFFFFF;text-decoration:none;border-radius:12px;background-color:<?php echo esc_attr( $color_error ); ?>;">Contactar por WhatsApp</a>
                        <!--<![endif]-->
                      </td>
                    </tr>
                  </table>

                </td>
              </tr>
            </table>
          </td>

          <!-- Columna derecha: tarjeta de detalles -->
          <td class="col stack-mt" width="44%" valign="middle" style="width:44%;vertical-align:middle;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td class="card-pad" style="background-color:<?php echo esc_attr( $color_card ); ?>;border-radius:16px;padding:32px 36px;font-family:Arial,Helvetica,sans-serif;">
                  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">

                    <tr>
                      <td colspan="2" style="padding:0 0 16px 0;font-size:20px;font-weight:600;color:<?php echo esc_attr( $color_text ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;font-family:Arial,Helvetica,sans-serif;">Detalles del pedido</td>
                    </tr>

                    <tr class="det-row">
                      <td class="det-label" style="padding:20px 0;font-size:13px;font-weight:500;letter-spacing:0.06em;text-transform:uppercase;color:<?php echo esc_attr( $color_muted ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;font-family:Arial,Helvetica,sans-serif;">Fecha</td>
                      <td class="det-value" align="right" style="padding:20px 0;font-size:17px;font-weight:600;color:<?php echo esc_attr( $color_text ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;font-family:Arial,Helvetica,sans-serif;"><?php echo esc_html( $date ); ?></td>
                    </tr>

                    <tr class="det-row">
                      <td class="det-label" style="padding:20px 0;font-size:13px;font-weight:500;letter-spacing:0.06em;text-transform:uppercase;color:<?php echo esc_attr( $color_muted ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;font-family:Arial,Helvetica,sans-serif;">Precio final</td>
                      <td class="det-value" align="right" style="padding:20px 0;font-size:17px;font-weight:600;color:<?php echo esc_attr( $color_text ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;font-family:Arial,Helvetica,sans-serif;"><?php echo wp_kses_post( $total_html ); ?></td>
                    </tr>

                    <tr class="det-row">
                      <td class="det-label" style="padding:20px 0;font-size:13px;font-weight:500;letter-spacing:0.06em;text-transform:uppercase;color:<?php echo esc_attr( $color_muted ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;font-family:Arial,Helvetica,sans-serif;">Método de pago</td>
                      <td class="det-value" align="right" style="padding:20px 0;font-size:17px;font-weight:600;color:<?php echo esc_attr( $color_text ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;font-family:Arial,Helvetica,sans-serif;"><?php echo esc_html( $payment ); ?></td>
                    </tr>

                    <tr class="det-row">
                      <td class="det-label" style="padding:20px 0 4px 0;font-size:13px;font-weight:500;letter-spacing:0.06em;text-transform:uppercase;color:<?php echo esc_attr( $color_muted ); ?>;font-family:Arial,Helvetica,sans-serif;">Estado</td>
                      <td class="det-value det-last" align="right" style="padding:20px 0 4px 0;font-family:Arial,Helvetica,sans-serif;">
                        <span style="display:inline-block;padding:6px 14px;border-radius:999px;background-color:<?php echo esc_attr( $color_warn_bg ); ?>;color:<?php echo esc_attr( $color_warn ); ?>;font-size:13px;font-weight:600;letter-spacing:0.04em;text-transform:uppercase;font-family:Arial,Helvetica,sans-serif;"><?php echo esc_html( $status ); ?></span>
                      </td>
                    </tr>

                  </table>
                </td>
              </tr>
            </table>
          </td>

        </tr>
        </table>
      </td>
    </tr>

    <!-- =========================================================
         TIMELINE — 3 pasos, "Fallido" en rojo
    ========================================================= -->
    <tr>
      <td class="px" style="padding:0 80px 72px 80px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td style="padding-bottom:16px;font-family:Arial,Helvetica,sans-serif;font-size:22px;font-weight:600;color:<?php echo esc_attr( $color_text ); ?>;">Estado del pedido</td>
          </tr>
          <tr>
            <td class="card-pad" style="background-color:<?php echo esc_attr( $color_card ); ?>;border-radius:16px;padding:24px 32px;text-align:center;">

              <div style="font-size:0;text-align:center;">

                <span style="display:inline-block;padding:10px 20px;margin:4px;border-radius:999px;background-color:#D9F0E1;color:<?php echo esc_attr( $color_button ); ?>;font-size:14px;font-weight:600;letter-spacing:0.02em;font-family:Arial,Helvetica,sans-serif;">
                  ✓ Recibido
                </span>

                <span style="display:inline-block;padding:10px 20px;margin:4px;border-radius:999px;background-color:<?php echo esc_attr( $color_error_bg ); ?>;color:<?php echo esc_attr( $color_error ); ?>;font-size:14px;font-weight:600;letter-spacing:0.02em;font-family:Arial,Helvetica,sans-serif;">
                  ✕ Fallido
                </span>

                <span style="display:inline-block;padding:10px 20px;margin:4px;border-radius:999px;background-color:<?php echo esc_attr( $color_pill_off ); ?>;color:<?php echo esc_attr( $color_pill_off_txt ); ?>;font-size:14px;font-weight:600;letter-spacing:0.02em;font-family:Arial,Helvetica,sans-serif;">
                  • Enviado
                </span>

              </div>

            </td>
          </tr>
        </table>
      </td>
    </tr>

<?php
/*
 * FOOTER
 */
do_action( 'woocommerce_email_footer', $email );