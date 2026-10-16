<?php
/**
 * Vendor Order Email — IslaBeya
 *
 * @package Tema IslaBeya
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Incluir header personalizado
include locate_template( 'dokan/emails/email-header.php' );

// Datos del pedido
$order_number = $order->get_order_number();
$customer     = trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() );
$total_html   = $order->get_formatted_order_total();
$view_url     = $order->get_view_order_url();
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

                  <p style="margin:0 0 12px 0;font-size:13px;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:<?php echo esc_attr( $color_primary ); ?>;font-family:Arial,Helvetica,sans-serif;">
                    Pedido #<?php echo esc_html( $order_number ); ?>
                  </p>

                  <h1 class="h1" style="margin:0 0 24px 0;font-size:48px;line-height:1.15;font-weight:600;color:<?php echo esc_attr( $color_text ); ?>;font-family:Arial,Helvetica,sans-serif;">
                    Nuevo<br>
                    <span style="color:<?php echo esc_attr( $color_primary ); ?>;">pedido</span>
                  </h1>

                  <p class="txt" style="margin:0 0 32px 0;font-size:18px;line-height:1.6;color:<?php echo esc_attr( $color_text_soft ); ?>;font-family:Arial,Helvetica,sans-serif;">
                    Has recibido un nuevo pedido. Revísalo en tu panel de vendedor.
                  </p>

                  <!-- Botón -->
                  <table role="presentation" class="btn-full" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td align="center" bgcolor="<?php echo esc_attr( $color_button ); ?>" style="border-radius:12px;">
                        <!--[if mso]>
                        <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" href="<?php echo esc_url( $view_url ); ?>" style="height:60px;v-text-anchor:middle;width:300px;" arcsize="20%" stroke="f" fillcolor="<?php echo esc_attr( $color_button ); ?>">
                          <w:anchorlock/><center style="color:#FFFFFF;font-family:Arial,sans-serif;font-size:18px;font-weight:bold;">Ver pedido</center>
                        </v:roundrect>
                        <![endif]-->
                        <!--[if !mso]><!-->
                        <a href="<?php echo esc_url( $view_url ); ?>" target="_blank" style="display:inline-block;padding:19px 48px;font-family:Arial,Helvetica,sans-serif;font-size:18px;font-weight:600;line-height:22px;color:#FFFFFF;text-decoration:none;border-radius:12px;background-color:<?php echo esc_attr( $color_button ); ?>;">Ver pedido</a>
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
                      <td class="det-label" style="padding:20px 0;font-size:13px;font-weight:500;letter-spacing:0.06em;text-transform:uppercase;color:<?php echo esc_attr( $color_muted ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;font-family:Arial,Helvetica,sans-serif;">Pedido</td>
                      <td class="det-value" align="right" style="padding:20px 0;font-size:17px;font-weight:600;color:<?php echo esc_attr( $color_text ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;font-family:Arial,Helvetica,sans-serif;">#<?php echo esc_html( $order_number ); ?></td>
                    </tr>

                    <tr class="det-row">
                      <td class="det-label" style="padding:20px 0;font-size:13px;font-weight:500;letter-spacing:0.06em;text-transform:uppercase;color:<?php echo esc_attr( $color_muted ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;font-family:Arial,Helvetica,sans-serif;">Cliente</td>
                      <td class="det-value" align="right" style="padding:20px 0;font-size:17px;font-weight:600;color:<?php echo esc_attr( $color_text ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;word-break:break-word;font-family:Arial,Helvetica,sans-serif;"><?php echo esc_html( $customer ); ?></td>
                    </tr>

                    <tr class="det-row">
                      <td class="det-label" style="padding:20px 0 4px 0;font-size:13px;font-weight:500;letter-spacing:0.06em;text-transform:uppercase;color:<?php echo esc_attr( $color_muted ); ?>;font-family:Arial,Helvetica,sans-serif;">Total</td>
                      <td class="det-value det-last" align="right" style="padding:20px 0 4px 0;font-size:17px;font-weight:600;color:<?php echo esc_attr( $color_text ); ?>;font-family:Arial,Helvetica,sans-serif;"><?php echo wp_kses_post( $total_html ); ?></td>
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
         PRODUCTOS
    ========================================================= -->
    <tr>
      <td class="px" style="padding:0 80px 72px 80px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td style="padding-bottom:16px;font-family:Arial,Helvetica,sans-serif;font-size:22px;font-weight:600;color:<?php echo esc_attr( $color_text ); ?>;">Productos</td>
          </tr>
          <tr>
            <td class="card-pad" style="background-color:<?php echo esc_attr( $color_card ); ?>;border-radius:16px;padding:8px 32px 24px 32px;font-family:Arial,Helvetica,sans-serif;">

              <?php echo islabeya_email_render_products_list( $order ); ?>

            </td>
          </tr>
        </table>
      </td>
    </tr>

<?php
// Incluir footer personalizado
include locate_template( 'dokan/emails/email-footer.php' );