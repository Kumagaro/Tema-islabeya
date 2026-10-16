<?php
/**
 * Store Approval Email — IslaBeya
 *
 * @package Tema IslaBeya
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Incluir header personalizado
include locate_template( 'dokan/emails/email-header.php' );
?>

    <!-- =========================================================
         HERO — 1 columna (celebración)
    ========================================================= -->
    <tr>
      <td class="px py-hero" style="padding:72px 80px;text-align:left;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td class="col" valign="middle" style="font-family:Arial,Helvetica,sans-serif;color:<?php echo esc_attr( $color_text ); ?>;">

            <p style="margin:0 0 12px 0;font-size:13px;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:<?php echo esc_attr( $color_primary ); ?>;font-family:Arial,Helvetica,sans-serif;">
              Bienvenido a IslaBeya
            </p>

            <h1 class="h1" style="margin:0 0 24px 0;font-size:48px;line-height:1.15;font-weight:600;color:<?php echo esc_attr( $color_text ); ?>;font-family:Arial,Helvetica,sans-serif;">
              ¡Tu tienda ha sido<br>
              <span style="color:<?php echo esc_attr( $color_primary ); ?>;">aprobada!</span>
            </h1>

            <p class="txt" style="margin:0 0 16px 0;font-size:18px;line-height:1.6;color:<?php echo esc_attr( $color_text_soft ); ?>;font-family:Arial,Helvetica,sans-serif;">
              ¡Felicidades! Ya puedes comenzar a vender tus productos en IslaBeya.
            </p>

            <p class="txt" style="margin:0 0 32px 0;font-size:18px;line-height:1.6;color:<?php echo esc_attr( $color_text_soft ); ?>;font-family:Arial,Helvetica,sans-serif;">
              Accede a tu panel de vendedor para configurar tu tienda, subir productos y gestionar tus pedidos.
            </p>

            <!-- Botón -->
            <table role="presentation" class="btn-full" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td align="center" bgcolor="<?php echo esc_attr( $color_button ); ?>" style="border-radius:12px;">
                  <!--[if mso]>
                  <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" href="<?php echo esc_url( dokan_get_store_url( $user_id ) ); ?>" style="height:60px;v-text-anchor:middle;width:300px;" arcsize="20%" stroke="f" fillcolor="<?php echo esc_attr( $color_button ); ?>">
                    <w:anchorlock/><center style="color:#FFFFFF;font-family:Arial,sans-serif;font-size:18px;font-weight:bold;">Ir a mi tienda</center>
                  </v:roundrect>
                  <![endif]-->
                  <!--[if !mso]><!-->
                  <a href="<?php echo esc_url( dokan_get_store_url( $user_id ) ); ?>" target="_blank" style="display:inline-block;padding:19px 48px;font-family:Arial,Helvetica,sans-serif;font-size:18px;font-weight:600;line-height:22px;color:#FFFFFF;text-decoration:none;border-radius:12px;background-color:<?php echo esc_attr( $color_button ); ?>;">Ir a mi tienda</a>
                  <!--<![endif]-->
                </td>
              </tr>
            </table>

          </td>
        </tr>
        </table>
      </td>
    </tr>

<?php
// Incluir footer personalizado
include locate_template( 'dokan/emails/email-footer.php' );