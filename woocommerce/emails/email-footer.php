<?php
/**
 * Admin New Order Email — IslaBeya
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Heading
$email_heading = 'Nuevo pedido recibido';

// Paleta local (para estilos inline)
$color_primary   = '#15AD3C';
$color_button    = '#0E7A2B';
$color_card      = '#F3F8F4';
$color_border    = '#DCE8DF';
$color_text      = '#1F2A22';
$color_text_soft = '#3E4A42';
$color_muted     = '#56635A';

// Datos del pedido
$order_number = $order->get_order_number();
$order_id     = $order->get_id();
$customer     = trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() );
$email_addr   = $order->get_billing_email();
$phone        = $order->get_billing_phone();
$date         = $order->get_date_created() ? $order->get_date_created()->date_i18n( 'j \d\e F \d\e Y H:i' ) : '';
$total_html   = $order->get_formatted_order_total();
$payment      = $order->get_payment_method_title();
$status       = wc_get_order_status_name( $order->get_status() );
$admin_url    = admin_url( 'post.php?post=' . $order_id . '&action=edit' );

/*
 * HEADER
 */
do_action( 'woocommerce_email_header', $email_heading, $email );
?>

    <!-- =========================================================
         HERO — 2 columnas (texto + tarjeta detalles)
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

                  <p style="margin:0 0 12px 0;font-size:13px;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:<?php echo esc_attr( $color_primary ); ?>;font-family:Arial,Helvetica,sans-serif;">Panel de administración</p>

                  <h1 class="h1" style="margin:0 0 24px 0;font-size:48px;line-height:1.15;font-weight:600;color:<?php echo esc_attr( $color_text ); ?>;font-family:Arial,Helvetica,sans-serif;">
                    Nuevo pedido<br>
                    <span style="color:<?php echo esc_attr( $color_primary ); ?>;">#<?php echo esc_html( $order_number ); ?></span>
                  </h1>

                  <p class="txt" style="margin:0 0 32px 0;font-size:18px;line-height:1.6;color:<?php echo esc_attr( $color_text_soft ); ?>;font-family:Arial,Helvetica,sans-serif;">
                    Has recibido un nuevo pedido en IslaBeya. A continuación tienes los detalles del cliente, la dirección de envío y los productos.
                  </p>

                  <!-- Botón -->
                  <table role="presentation" class="btn-full" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td align="center" bgcolor="<?php echo esc_attr( $color_button ); ?>" style="border-radius:12px;">
                        <!--[if mso]>
                        <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" href="<?php echo esc_url( $admin_url ); ?>" style="height:60px;v-text-anchor:middle;width:300px;" arcsize="20%" stroke="f" fillcolor="<?php echo esc_attr( $color_button ); ?>">
                          <w:anchorlock/><center style="color:#FFFFFF;font-family:Arial,sans-serif;font-size:18px;font-weight:bold;">Gestionar pedido</center>
                        </v:roundrect>
                        <![endif]-->
                        <!--[if !mso]><!-->
                        <a href="<?php echo esc_url( $admin_url ); ?>" target="_blank" style="display:inline-block;padding:19px 48px;font-family:Arial,Helvetica,sans-serif;font-size:18px;font-weight:600;line-height:22px;color:#FFFFFF;text-decoration:none;border-radius:12px;background-color:<?php echo esc_attr( $color_button ); ?>;">Gestionar pedido</a>
                        <!--<![endif]-->
                      </td>
                    </tr>
                  </table>

                </td>
              </tr>
            </table>
          </td>

          <!-- Columna derecha: tarjeta detalles -->
          <td class="col stack-mt" width="44%" valign="middle" style="width:44%;vertical-align:middle;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td class="card-pad" style="background-color:<?php echo esc_attr( $color_card ); ?>;border-radius:16px;padding:32px 36px;font-family:Arial,Helvetica,sans-serif;">
                  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">

                    <tr>
                      <td colspan="2" style="padding:0 0 16px 0;font-size:20px;font-weight:600;color:<?php echo esc_attr( $color_text ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;font-family:Arial,Helvetica,sans-serif;">Detalles del pedido</td>
                    </tr>

                    <tr class="det-row">
                      <td class="det-label" style="padding:20px 0;font-size:13px;font-weight:500;letter-spacing:0.06em;text-transform:uppercase;color:<?php echo esc_attr( $color_muted ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;font-family:Arial,Helvetica,sans-serif;">Cliente</td>
                      <td class="det-value" align="right" style="padding:20px 0;font-size:17px;font-weight:600;color:<?php echo esc_attr( $color_text ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;word-break:break-word;font-family:Arial,Helvetica,sans-serif;"><?php echo esc_html( $customer ); ?></td>
                    </tr>

                    <tr class="det-row">
                      <td class="det-label" style="padding:20px 0;font-size:13px;font-weight:500;letter-spacing:0.06em;text-transform:uppercase;color:<?php echo esc_attr( $color_muted ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;font-family:Arial,Helvetica,sans-serif;">Email</td>
                      <td class="det-value" align="right" style="padding:20px 0;font-size:17px;font-weight:600;color:<?php echo esc_attr( $color_text ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;word-break:break-word;font-family:Arial,Helvetica,sans-serif;"><a href="mailto:<?php echo esc_attr( $email_addr ); ?>" style="color:<?php echo esc_attr( $color_button ); ?>;text-decoration:none;"><?php echo esc_html( $email_addr ); ?></a></td>
                    </tr>

                    <?php if ( $phone ) : ?>
                    <tr class="det-row">
                      <td class="det-label" style="padding:20px 0;font-size:13px;font-weight:500;letter-spacing:0.06em;text-transform:uppercase;color:<?php echo esc_attr( $color_muted ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;font-family:Arial,Helvetica,sans-serif;">Teléfono</td>
                      <td class="det-value" align="right" style="padding:20px 0;font-size:17px;font-weight:600;color:<?php echo esc_attr( $color_text ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;font-family:Arial,Helvetica,sans-serif;"><?php echo esc_html( $phone ); ?></td>
                    </tr>
                    <?php endif; ?>

                    <tr class="det-row">
                      <td class="det-label" style="padding:20px 0;font-size:13px;font-weight:500;letter-spacing:0.06em;text-transform:uppercase;color:<?php echo esc_attr( $color_muted ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;font-family:Arial,Helvetica,sans-serif;">Fecha</td>
                      <td class="det-value" align="right" style="padding:20px 0;font-size:17px;font-weight:600;color:<?php echo esc_attr( $color_text ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;font-family:Arial,Helvetica,sans-serif;"><?php echo esc_html( $date ); ?></td>
                    </tr>

                    <tr class="det-row">
                      <td class="det-label" style="padding:20px 0;font-size:13px;font-weight:500;letter-spacing:0.06em;text-transform:uppercase;color:<?php echo esc_attr( $color_muted ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;font-family:Arial,Helvetica,sans-serif;">Total</td>
                      <td class="det-value" align="right" style="padding:20px 0;font-size:17px;font-weight:600;color:<?php echo esc_attr( $color_text ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;font-family:Arial,Helvetica,sans-serif;"><?php echo wp_kses_post( $total_html ); ?></td>
                    </tr>

                    <tr class="det-row">
                      <td class="det-label" style="padding:20px 0;font-size:13px;font-weight:500;letter-spacing:0.06em;text-transform:uppercase;color:<?php echo esc_attr( $color_muted ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;font-family:Arial,Helvetica,sans-serif;">Pago</td>
                      <td class="det-value" align="right" style="padding:20px 0;font-size:17px;font-weight:600;color:<?php echo esc_attr( $color_text ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;font-family:Arial,Helvetica,sans-serif;"><?php echo esc_html( $payment ); ?></td>
                    </tr>

                    <tr class="det-row">
                      <td class="det-label" style="padding:20px 0 4px 0;font-size:13px;font-weight:500;letter-spacing:0.06em;text-transform:uppercase;color:<?php echo esc_attr( $color_muted ); ?>;font-family:Arial,Helvetica,sans-serif;">Estado</td>
                      <td class="det-value det-last" align="right" style="padding:20px 0 4px 0;font-family:Arial,Helvetica,sans-serif;">
                        <span style="display:inline-block;padding:6px 14px;border-radius:999px;background-color:#D9F0E1;color:<?php echo esc_attr( $color_button ); ?>;font-size:13px;font-weight:600;letter-spacing:0.04em;text-transform:uppercase;font-family:Arial,Helvetica,sans-serif;"><?php echo esc_html( $status ); ?></span>
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
         DIRECCIÓN DE ENVÍO
    ========================================================= -->
    <tr>
      <td class="px" style="padding:0 80px 40px 80px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td style="padding-bottom:16px;font-family:Arial,Helvetica,sans-serif;font-size:22px;font-weight:600;color:<?php echo esc_attr( $color_text ); ?>;">Dirección de envío</td>
          </tr>
          <tr>
            <td class="card-pad" style="background-color:<?php echo esc_attr( $color_card ); ?>;border-radius:16px;padding:28px 32px;font-family:Arial,Helvetica,sans-serif;">

              <div style="font-size:17px;font-weight:600;color:<?php echo esc_attr( $color_text ); ?>;margin-bottom:8px;font-family:Arial,Helvetica,sans-serif;">
                <?php echo esc_html( trim( $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name() ) ); ?>
              </div>

              <div style="font-size:15px;line-height:1.7;color:<?php echo esc_attr( $color_text_soft ); ?>;font-family:Arial,Helvetica,sans-serif;">
                <?php
                if ( $order->get_shipping_address_1() ) {
                    echo esc_html( $order->get_shipping_address_1() ) . '<br>';
                }
                if ( $order->get_shipping_address_2() ) {
                    echo esc_html( $order->get_shipping_address_2() ) . '<br>';
                }
                $city_line = array();
                if ( $order->get_shipping_city() ) {
                    $city_line[] = $order->get_shipping_city();
                }
                if ( $order->get_shipping_state() ) {
                    $states = WC()->countries->get_states( $order->get_shipping_country() );
                    if ( isset( $states[ $order->get_shipping_state() ] ) ) {
                        $city_line[] = $states[ $order->get_shipping_state() ];
                    }
                }
                if ( $city_line ) {
                    echo esc_html( implode( ', ', $city_line ) ) . '<br>';
                }
                if ( $order->get_shipping_country() ) {
                    $countries = WC()->countries->get_countries();
                    if ( isset( $countries[ $order->get_shipping_country() ] ) ) {
                        echo esc_html( $countries[ $order->get_shipping_country() ] ) . '<br>';
                    }
                }
                if ( $order->get_shipping_postcode() ) {
                    echo esc_html( 'CP ' . $order->get_shipping_postcode() );
                }
                ?>
              </div>

              <?php
              $extra = array();
              if ( $order->get_meta( 'shipping_identity_card' ) ) {
                  $extra[] = 'Carnet: ' . $order->get_meta( 'shipping_identity_card' );
              }
              if ( $order->get_meta( 'shipping_house_number' ) ) {
                  $extra[] = 'Nº Casa: ' . $order->get_meta( 'shipping_house_number' );
              }
              if ( $order->get_meta( 'shipping_phone' ) ) {
                  $extra[] = 'Teléfono envío: ' . $order->get_meta( 'shipping_phone' );
              }
              if ( $extra ) :
              ?>
                <div style="margin-top:14px;padding-top:14px;border-top:1px solid <?php echo esc_attr( $color_border ); ?>;font-size:13px;line-height:1.7;color:<?php echo esc_attr( $color_muted ); ?>;font-family:Arial,Helvetica,sans-serif;">
                  <?php echo esc_html( implode( ' · ', $extra ) ); ?>
                </div>
              <?php endif; ?>

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

              <!-- Cabecera productos -->
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td style="padding:18px 0 12px 0;font-size:12px;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:<?php echo esc_attr( $color_muted ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;font-family:Arial,Helvetica,sans-serif;">Producto</td>
                  <td align="center" width="80" style="padding:18px 0 12px 0;font-size:12px;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:<?php echo esc_attr( $color_muted ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;font-family:Arial,Helvetica,sans-serif;">Cant.</td>
                  <td align="right" width="130" style="padding:18px 0 12px 0;font-size:12px;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:<?php echo esc_attr( $color_muted ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;font-family:Arial,Helvetica,sans-serif;">Subtotal</td>
                </tr>

                <?php
                $items      = $order->get_items();
                $last_index = count( $items ) - 1;
                $i          = 0;
                foreach ( $items as $item_id => $item ) :
                    $qty       = $item->get_quantity();
                    $subtotal  = $order->get_line_subtotal( $item, true );
                    $item_meta = $item->get_formatted_meta_data();
                    $is_last   = ( $i === $last_index );
                    $row_style = $is_last ? '' : 'border-bottom:1px solid ' . esc_attr( $color_border ) . ';';
                ?>
                <tr>
                  <td style="padding:16px 0;font-size:15px;color:<?php echo esc_attr( $color_text ); ?>;<?php echo $row_style; ?>font-family:Arial,Helvetica,sans-serif;">
                    <strong style="font-weight:600;"><?php echo esc_html( $item->get_name() ); ?></strong>
                    <?php if ( $item_meta ) : ?>
                      <div style="margin-top:6px;font-size:13px;line-height:1.6;color:<?php echo esc_attr( $color_muted ); ?>;font-family:Arial,Helvetica,sans-serif;">
                        <?php
                        $parts = array();
                        foreach ( $item_meta as $meta ) {
                            $parts[] = wp_kses_post( $meta->display_key ) . ': ' . wp_kses_post( $meta->display_value );
                        }
                        echo implode( ' · ', $parts );
                        ?>
                      </div>
                    <?php endif; ?>
                  </td>
                  <td align="center" style="padding:16px 0;font-size:15px;color:<?php echo esc_attr( $color_text_soft ); ?>;<?php echo $row_style; ?>font-family:Arial,Helvetica,sans-serif;"><?php echo esc_html( $qty ); ?></td>
                  <td align="right" style="padding:16px 0;font-size:15px;font-weight:600;color:<?php echo esc_attr( $color_text ); ?>;<?php echo $row_style; ?>font-family:Arial,Helvetica,sans-serif;"><?php echo wp_kses_post( wc_price( $subtotal ) ); ?></td>
                </tr>
                <?php $i++; endforeach; ?>
              </table>

              <!-- Totales -->
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:8px;">
                <?php foreach ( $order->get_order_item_totals() as $key => $total_row ) : ?>
                <tr>
                  <td style="padding:8px 0;font-size:15px;color:<?php echo esc_attr( $color_muted ); ?>;font-family:Arial,Helvetica,sans-serif;"><?php echo esc_html( $total_row['label'] ); ?></td>
                  <td align="right" style="padding:8px 0;font-size:15px;color:<?php echo esc_attr( $color_text ); ?>;font-family:Arial,Helvetica,sans-serif;"><?php echo wp_kses_post( $total_row['value'] ); ?></td>
                </tr>
                <?php endforeach; ?>
              </table>

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