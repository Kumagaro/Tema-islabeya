<?php
/**
 * ============================================
 * FUNCIONES AUXILIARES PARA LISTA DE PRODUCTOS EN CORREOS
 * ============================================
 *
 * - Obtiene información completa de producto (imagen, nombre, vendedor, tipo de entrega, cantidad, precio)
 * - Agrupa productos por tipo de entrega y dirección de recogida
 * - Renderiza lista de productos en formato tabla con estilos inline (compatible con el nuevo diseño IslaBeya)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Obtiene información completa de un producto para mostrar en correos
 *
 * @param WC_Order_Item_Product $item Ítem del pedido
 * @param WC_Order $order Pedido
 * @return array Información del producto
 */
function islabeya_email_get_product_info( $item, $order ) {

    $product    = $item->get_product();
    $product_id = $item->get_product_id();

    // Imagen del producto
    $image_url = '';
    if ( $product ) {
        $image_id = $product->get_image_id();
        if ( $image_id ) {
            $image_url = wp_get_attachment_image_url( $image_id, 'thumbnail' );
        }
        if ( empty( $image_url ) ) {
            $image_url = wc_placeholder_img_src();
        }
    }

    // Nombre del producto
    $product_name = $item->get_name();

    // Vendedor (tienda)
    $vendor_id      = get_post_field( 'post_author', $product_id );
    $vendor_name    = '';
    $vendor_address = '';

    if ( $vendor_id ) {
        if ( function_exists( 'dokan_get_store_info' ) ) {
            $store_info = dokan_get_store_info( $vendor_id );

            if ( ! empty( $store_info['store_name'] ) ) {
                $vendor_name = $store_info['store_name'];
            } else {
                $vendor_name = get_the_author_meta( 'display_name', $vendor_id );
            }

            // Dirección de la tienda
            if ( ! empty( $store_info['address'] ) ) {
                $address_parts = array();
                if ( ! empty( $store_info['address']['street_1'] ) ) {
                    $address_parts[] = $store_info['address']['street_1'];
                }
                if ( ! empty( $store_info['address']['city'] ) ) {
                    $address_parts[] = $store_info['address']['city'];
                }
                if ( ! empty( $store_info['address']['state'] ) ) {
                    $address_parts[] = $store_info['address']['state'];
                }
                $vendor_address = implode( ', ', $address_parts );
            }
        } else {
            $vendor_name = get_the_author_meta( 'display_name', $vendor_id );
        }
    }

    if ( empty( $vendor_name ) ) {
        $vendor_name = 'Tienda Local';
    }

    // Tipo de entrega (del metadato del ítem o del producto)
    $shipping_type = $item->get_meta( '_shipping_type', true );
    if ( empty( $shipping_type ) ) {
        $shipping_type = get_post_meta( $product_id, '_shipping_type', true );
    }
    if ( empty( $shipping_type ) ) {
        $shipping_type = 'gratis'; // Default
    }

    // Texto del tipo de entrega
    $shipping_text = ( $shipping_type === 'recogida' ) ? 'Recogida en local' : 'Envío gratuito';

    // Cantidad
    $quantity = $item->get_quantity();

    // Precio unitario
    $unit_price = $item->get_subtotal() / max( 1, $quantity );

    // Precio total del ítem
    $total_price = $item->get_total();

    // Variaciones del producto
    $variations = array();
    $item_data  = $item->get_formatted_meta_data();
    foreach ( $item_data as $meta ) {
        $variations[] = array(
            'key'   => $meta->display_key,
            'value' => $meta->display_value,
        );
    }

    return array(
        'image_url'      => $image_url,
        'product_name'   => $product_name,
        'vendor_name'    => $vendor_name,
        'vendor_address' => $vendor_address,
        'vendor_id'      => $vendor_id,
        'shipping_type'  => $shipping_type,
        'shipping_text'  => $shipping_text,
        'quantity'       => $quantity,
        'unit_price'     => $unit_price,
        'total_price'    => $total_price,
        'variations'     => $variations,
    );
}

/**
 * Agrupa los productos del pedido por tipo de entrega y dirección de recogida
 *
 * @param WC_Order $order Pedido
 * @return array Productos agrupados
 */
function islabeya_email_group_products_by_delivery( $order ) {

    $groups = array(
        'shipping' => array(
            'label'    => 'Envío a domicilio',
            'address'  => '',
            'products' => array(),
        ),
        'pickup' => array(),
    );

    foreach ( $order->get_items() as $item_id => $item ) {
        if ( ! $item instanceof WC_Order_Item_Product ) {
            continue;
        }

        $product_info = islabeya_email_get_product_info( $item, $order );

        if ( $product_info['shipping_type'] === 'recogida' ) {
            // Agrupar por dirección de recogida (vendedor)
            $address_key = $product_info['vendor_address'];
            if ( empty( $address_key ) ) {
                $address_key = $product_info['vendor_name'];
            }

            if ( ! isset( $groups['pickup'][ $address_key ] ) ) {
                $groups['pickup'][ $address_key ] = array(
                    'label'       => 'Recogida en local',
                    'address'     => $address_key,
                    'vendor_name' => $product_info['vendor_name'],
                    'vendor_id'   => $product_info['vendor_id'],
                    'products'    => array(),
                );
            }

            $groups['pickup'][ $address_key ]['products'][] = $product_info;
        } else {
            // Envío a domicilio
            $groups['shipping']['products'][] = $product_info;
        }
    }

    return $groups;
}

/**
 * Renderiza la cabecera de un grupo (envío o recogida)
 *
 * @param string $label   Etiqueta del grupo (ej. "Envío a domicilio")
 * @param string $address Dirección asociada al grupo
 * @return string HTML del <tr> con la cabecera
 */
function islabeya_email_render_group_header( $label, $address = '' ) {
    ob_start();
    ?>
    <tr>
      <td colspan="3" style="padding:24px 0 8px 0;font-family:Arial,Helvetica,sans-serif;">
        <div style="font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:#0E7A2B;line-height:1.4;">
          <?php echo esc_html( $label ); ?>
        </div>
        <?php if ( $address ) : ?>
          <div style="font-size:13px;color:#56635A;line-height:1.5;margin-top:2px;">
            <?php echo esc_html( $address ); ?>
          </div>
        <?php endif; ?>
      </td>
    </tr>
    <?php
    return ob_get_clean();
}

/**
 * Renderiza un producto individual en formato <tr> con estilos inline
 *
 * @param array $product_info Información del producto
 * @return string HTML del <tr>
 */
function islabeya_email_render_product_item( $product_info ) {
    ob_start();
    ?>
    <tr>
      <td style="padding:16px 0;border-bottom:1px solid #DCE8DF;font-family:Arial,Helvetica,sans-serif;vertical-align:top;">
        <table role="presentation" cellpadding="0" cellspacing="0" border="0">
          <tr>
            <?php if ( ! empty( $product_info['image_url'] ) ) : ?>
            <td width="72" valign="top" style="vertical-align:top;padding:0 12px 0 0;">
              <img src="<?php echo esc_url( $product_info['image_url'] ); ?>"
                   width="60" height="60"
                   alt="<?php echo esc_attr( $product_info['product_name'] ); ?>"
                   style="display:block;width:60px;height:60px;border-radius:8px;object-fit:cover;border:0;outline:none;text-decoration:none;">
            </td>
            <?php endif; ?>
            <td valign="top" style="vertical-align:top;font-family:Arial,Helvetica,sans-serif;">
              <div style="font-size:15px;font-weight:600;color:#1F2A22;line-height:1.35;">
                <?php echo esc_html( $product_info['product_name'] ); ?>
              </div>

              <?php if ( ! empty( $product_info['variations'] ) ) : ?>
                <div style="font-size:13px;color:#56635A;line-height:1.5;margin-top:4px;">
                  <?php
                  $variation_parts = array();
                  foreach ( $product_info['variations'] as $v ) {
                      $variation_parts[] = esc_html( $v['key'] ) . ': ' . esc_html( $v['value'] );
                  }
                  echo implode( ' &middot; ', $variation_parts );
                  ?>
                </div>
              <?php endif; ?>

              <div style="font-size:13px;color:#56635A;line-height:1.5;margin-top:4px;">
                <?php echo esc_html( $product_info['vendor_name'] ); ?>
                <span style="color:#DCE8DF;">&middot;</span>
                <span style="color:#15AD3C;font-weight:600;"><?php echo esc_html( $product_info['shipping_text'] ); ?></span>
              </div>
            </td>
          </tr>
        </table>
      </td>
      <td align="center" width="60" style="padding:16px 0;border-bottom:1px solid #DCE8DF;font-family:Arial,Helvetica,sans-serif;font-size:15px;color:#56635A;vertical-align:top;white-space:nowrap;">
        x<?php echo esc_html( $product_info['quantity'] ); ?>
      </td>
      <td align="right" width="100" style="padding:16px 0;border-bottom:1px solid #DCE8DF;font-family:Arial,Helvetica,sans-serif;font-size:15px;font-weight:600;color:#1F2A22;vertical-align:top;white-space:nowrap;">
        <?php echo wp_kses_post( wc_price( $product_info['total_price'] ) ); ?>
      </td>
    </tr>
    <?php
    return ob_get_clean();
}

/**
 * Renderiza la lista completa de productos agrupados en formato tabla
 *
 * Devuelve una <table> autocontenida (cabecera + grupos + productos) lista
 * para insertarse dentro del card de productos en las plantillas.
 *
 * @param WC_Order $order Pedido
 * @return string HTML de la lista de productos
 */
function islabeya_email_render_products_list( $order ) {

    $groups = islabeya_email_group_products_by_delivery( $order );

    $has_shipping        = ! empty( $groups['shipping']['products'] );
    $has_pickup          = ! empty( $groups['pickup'] );
    $show_shipping_label = ( $has_shipping && $has_pickup );

    ob_start();
    ?>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">

      <!-- Cabecera: Producto / Cant. / Subtotal -->
      <tr>
        <td style="padding:18px 0 12px 0;font-size:12px;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:#56635A;border-bottom:1px solid #DCE8DF;font-family:Arial,Helvetica,sans-serif;">
          Producto
        </td>
        <td align="center" width="60" style="padding:18px 0 12px 0;font-size:12px;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:#56635A;border-bottom:1px solid #DCE8DF;font-family:Arial,Helvetica,sans-serif;">
          Cant.
        </td>
        <td align="right" width="100" style="padding:18px 0 12px 0;font-size:12px;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:#56635A;border-bottom:1px solid #DCE8DF;font-family:Arial,Helvetica,sans-serif;">
          Subtotal
        </td>
      </tr>

      <?php
      /* ---------- ENVÍO A DOMICILIO ---------- */
      if ( $has_shipping ) {

          if ( $show_shipping_label ) {
              $ship_address = '';
              if ( $order->get_shipping_address_1() ) {
                  $ship_address = $order->get_shipping_address_1();
                  if ( $order->get_shipping_city() ) {
                      $ship_address .= ', ' . $order->get_shipping_city();
                  }
                  if ( $order->get_shipping_state() ) {
                      $ship_address .= ', ' . $order->get_shipping_state();
                  }
              }
              echo islabeya_email_render_group_header( 'Envío a domicilio', $ship_address );
          }

          foreach ( $groups['shipping']['products'] as $product ) {
              echo islabeya_email_render_product_item( $product );
          }
      }

      /* ---------- RECOGIDA EN LOCAL (agrupado por dirección) ---------- */
      if ( $has_pickup ) {
          foreach ( $groups['pickup'] as $pickup_group ) {
              echo islabeya_email_render_group_header( 'Recogida en local', $pickup_group['address'] );

              foreach ( $pickup_group['products'] as $product ) {
                  echo islabeya_email_render_product_item( $product );
              }
          }
      }
      ?>

    </table>
    <?php
    return ob_get_clean();
}