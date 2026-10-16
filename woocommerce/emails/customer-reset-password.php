<?php
/**
 * Customer Reset Password Email — IslaBeya
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Paleta local (para estilos inline)
$color_primary   = '#15AD3C';
$color_button    = '#0E7A2B';
$color_card      = '#F3F8F4';
$color_border    = '#DCE8DF';
$color_text      = '#1F2A22';
$color_text_soft = '#3E4A42';
$color_muted     = '#56635A';

// Heading
$email_heading = 'Restablecer contraseña';

// URL de restablecimiento
$reset_url = add_query_arg(
    array(
        'key'   => $reset_key,
        'login' => rawurlencode( $user_login ),
    ),
    wc_get_endpoint_url( 'lost-password', '', wc_get_page_permalink( 'myaccount' ) )
);

// Fecha actual
$date = date_i18n( 'j \d\e F \d\e Y' );

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

          <!-- Columna izquierda: instrucciones + botón -->
          <td class="col" width="56%" valign="middle" style="width:56%;vertical-align:middle;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td class="col-left-pad" style="padding-right:60px;font-family:Arial,Helvetica,sans-serif;color:<?php echo esc_attr( $color_text ); ?>;">

                  <p style="margin:0 0 12px 0;font-size:13px;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:<?php echo esc_attr( $color_primary ); ?>;font-family:Arial,Helvetica,sans-serif;">
                    Seguridad de la cuenta
                  </p>

                  <h1 class="h1" style="margin:0 0 24px 0;font-size:48px;line-height:1.15;font-weight:600;color:<?php echo esc_attr( $color_text ); ?>;font-family:Arial,Helvetica,sans-serif;">
                    Restablece<br>
                    <span style="color:<?php echo esc_attr( $color_primary ); ?>;">tu contraseña</span>
                  </h1>

                  <p class="txt" style="margin:0 0 16px 0;font-size:18px;line-height:1.6;color:<?php echo esc_attr( $color_text_soft ); ?>;font-family:Arial,Helvetica,sans-serif;">
                    Hola,
                  </p>

                  <p class="txt" style="margin:0 0 16px 0;font-size:18px;line-height:1.6;color:<?php echo esc_attr( $color_text_soft ); ?>;font-family:Arial,Helvetica,sans-serif;">
                    Recibiste una solicitud para restablecer la contraseña de tu cuenta en IslaBeya. Si no solicitaste esto, puedes ignorar este correo.
                  </p>

                  <p class="txt" style="margin:0 0 32px 0;font-size:18px;line-height:1.6;color:<?php echo esc_attr( $color_text_soft ); ?>;font-family:Arial,Helvetica,sans-serif;">
                    Para restablecer tu contraseña, haz clic en el siguiente botón. Este enlace expirará en <strong style="color:<?php echo esc_attr( $color_text ); ?>;">24 horas</strong>.
                  </p>

                  <!-- Botón -->
                  <table role="presentation" class="btn-full" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td align="center" bgcolor="<?php echo esc_attr( $color_button ); ?>" style="border-radius:12px;">
                        <!--[if mso]>
                        <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" href="<?php echo esc_url( $reset_url ); ?>" style="height:60px;v-text-anchor:middle;width:300px;" arcsize="20%" stroke="f" fillcolor="<?php echo esc_attr( $color_button ); ?>">
                          <w:anchorlock/><center style="color:#FFFFFF;font-family:Arial,sans-serif;font-size:18px;font-weight:bold;">Restablecer contraseña</center>
                        </v:roundrect>
                        <![endif]-->
                        <!--[if !mso]><!-->
                        <a href="<?php echo esc_url( $reset_url ); ?>" target="_blank" style="display:inline-block;padding:19px 48px;font-family:Arial,Helvetica,sans-serif;font-size:18px;font-weight:600;line-height:22px;color:#FFFFFF;text-decoration:none;border-radius:12px;background-color:<?php echo esc_attr( $color_button ); ?>;">Restablecer contraseña</a>
                        <!--<![endif]-->
                      </td>
                    </tr>
                  </table>

                  <!-- Fallback: enlace copiable -->
                  <p style="margin:24px 0 0 0;font-size:13px;line-height:1.6;color:<?php echo esc_attr( $color_muted ); ?>;font-family:Arial,Helvetica,sans-serif;">
                    ¿El botón no funciona? Copia y pega este enlace en tu navegador:<br>
                    <a href="<?php echo esc_url( $reset_url ); ?>" target="_blank" style="color:<?php echo esc_attr( $color_button ); ?>;word-break:break-all;"><?php echo esc_html( $reset_url ); ?></a>
                  </p>

                </td>
              </tr>
            </table>
          </td>

          <!-- Columna derecha: tarjeta detalles + aviso seguridad -->
          <td class="col stack-mt" width="44%" valign="middle" style="width:44%;vertical-align:middle;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">

              <!-- Tarjeta de detalles -->
              <tr>
                <td class="card-pad" style="background-color:<?php echo esc_attr( $color_card ); ?>;border-radius:16px;padding:32px 36px;font-family:Arial,Helvetica,sans-serif;">
                  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">

                    <tr>
                      <td colspan="2" style="padding:0 0 16px 0;font-size:20px;font-weight:600;color:<?php echo esc_attr( $color_text ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;font-family:Arial,Helvetica,sans-serif;">Detalles de la solicitud</td>
                    </tr>

                    <tr class="det-row">
                      <td class="det-label" style="padding:20px 0;font-size:13px;font-weight:500;letter-spacing:0.06em;text-transform:uppercase;color:<?php echo esc_attr( $color_muted ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;font-family:Arial,Helvetica,sans-serif;">Usuario</td>
                      <td class="det-value" align="right" style="padding:20px 0;font-size:17px;font-weight:600;color:<?php echo esc_attr( $color_text ); ?>;border-bottom:1px solid <?php echo esc_attr( $color_border ); ?>;word-break:break-word;font-family:Arial,Helvetica,sans-serif;"><?php echo esc_html( $user_login ); ?></td>
                    </tr>

                    <tr class="det-row">
                      <td class="det-label" style="padding:20px 0 4px 0;font-size:13px;font-weight:500;letter-spacing:0.06em;text-transform:uppercase;color:<?php echo esc_attr( $color_muted ); ?>;font-family:Arial,Helvetica,sans-serif;">Fecha</td>
                      <td class="det-value det-last" align="right" style="padding:20px 0 4px 0;font-size:17px;font-weight:600;color:<?php echo esc_attr( $color_text ); ?>;font-family:Arial,Helvetica,sans-serif;"><?php echo esc_html( $date ); ?></td>
                    </tr>

                  </table>
                </td>
              </tr>

              <!-- Aviso de seguridad -->
              <tr>
                <td style="padding:24px 8px 0 8px;font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.6;font-style:italic;color:<?php echo esc_attr( $color_muted ); ?>;">
                  Si no solicitaste este cambio, ignora este correo. Tu contraseña permanecerá sin cambios.
                </td>
              </tr>

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