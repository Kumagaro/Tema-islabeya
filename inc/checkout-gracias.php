<?php
/**
 * ============================================
 * CHECKOUT IslaBeya — Páginas de pago y confirmación
 * Helpers para form-pay, order-receipt, thankyou / order-received
 * ============================================
 *
 * - Resumen de productos a partir de un WC_Order (minitarjetas)
 * - Totales en estilo tarjetas
 * - Direcciones de facturación / envío
 * - Método de entrega (envío a domicilio / recogida en local) + tiendas de recogida
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 1) Resumen de productos de un pedido (mini-tarjetas con foto, cantidad, precio).
 */
function islabeya_gracias_productos( $order, $thumb_size = 58 ) {
	if ( ! $order ) {
		return;
	}
	echo '<div class="islabeya-review-table__productos islabeya-gracias__productos">';
	foreach ( $order->get_items() as $item_id => $item ) {
		if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
			continue;
		}
		$product = $item->get_product();
		$thumb   = $product ? $product->get_image( array( $thumb_size, $thumb_size ) ) : wc_placeholder_img( $thumb_size );
		$meta    = wc_display_item_meta( $item, array( 'echo' => false ) );

		echo '<div class="islabeya-review-table__item">';
		echo '<div class="islabeya-review-table__thumb">' . $thumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<span class="islabeya-review-table__qty">' . esc_html( $item->get_quantity() ) . '</span>';
		echo '</div>';
		echo '<div class="islabeya-gracias__item-info">';
		echo '<div class="islabeya-review-table__name">' . esc_html( $item->get_name() ) . '</div>';
		if ( $meta ) {
			echo '<div class="islabeya-review-table__meta">' . wp_kses_post( $meta ) . '</div>';
		}
		echo '</div>';
		echo '<div class="islabeya-review-table__price">' . wp_kses_post( $order->get_formatted_line_subtotal( $item ) ) . '</div>';
		echo '</div>';
	}
	echo '</div>';
}

/**
 * 2) Totales del pedido en tarjetas estilo resumen.
 */
function islabeya_gracias_totales( $order ) {
	if ( ! $order ) {
		return;
	}
	$totals = $order->get_order_item_totals();
	if ( empty( $totals ) ) {
		return;
	}
	echo '<div class="islabeya-review-table__totales islabeya-gracias__totales">';
	foreach ( $totals as $total ) {
		echo '<div class="islabeya-review-table__fila">';
		echo '<span class="islabeya-review-table__label">' . wp_kses_post( $total['label'] ) . '</span>';
		echo '<span class="islabeya-review-table__valor">' . wp_kses_post( $total['value'] ) . '</span>';
		echo '</div>';
	}
	echo '</div>';
}

/**
 * 3) Direcciones del pedido (facturación y envío).
 */
function islabeya_gracias_direcciones( $order ) {
	if ( ! $order ) {
		return;
	}
	echo '<div class="islabeya-gracias__direcciones">';

	echo '<div class="islabeya-gracias__direccion">';
	echo '<h4 class="islabeya-gracias__direccion-titulo">' . esc_html__( 'Dirección de facturación', 'islabeya' ) . '</h4>';
	$billing_address = $order->get_formatted_billing_address();
	echo '<address>' . wp_kses_post( $billing_address ? $billing_address : esc_html__( 'No disponible', 'islabeya' ) ) . '</address>';
	echo '</div>';

	if ( $order->has_shipping_address() && apply_filters( 'islabeya_gracias_mostrar_envio', true, $order ) ) {
		echo '<div class="islabeya-gracias__direccion">';
		echo '<h4 class="islabeya-gracias__direccion-titulo">' . esc_html__( 'Dirección de envío', 'islabeya' ) . '</h4>';
		echo '<address>' . wp_kses_post( $order->get_formatted_shipping_address() ) . '</address>';
		echo '</div>';
	}

	echo '</div>';
}

/**
 * 4) Método de entrega (envío a domicilio / recogida en local) + tiendas de recogida.
 *    Analiza el meta _shipping_type de cada producto del pedido.
 */
function islabeya_gracias_resumen_envio( $order ) {
	if ( ! $order ) {
		return;
	}

	// Reutiliza el helper centralizado de envío por producto (inc/checkout-pasos.php)
	// para mostrar el mismo detalle rico (motos→Aerovaradero, Comboxpress→Villa Clara, etc.).
	if ( function_exists( 'islabeya_checkout_obtener_info_envio_producto' ) && function_exists( 'islabeya_checkout_render_info_envio_producto' ) ) {
		echo '<div class="islabeya-resumen-envio islabeya-resumen-envio--detallado islabeya-gracias__entrega">';
		echo '<div class="islabeya-resumen-envio__titulo">' . esc_html__( 'Método de entrega', 'islabeya' ) . '</div>';

		foreach ( $order->get_items() as $item_id => $item ) {
			$product_id = $item->get_product_id();
			$info       = islabeya_checkout_obtener_info_envio_producto( $product_id );

			echo '<div class="islabeya-resumen-envio__producto">';
			echo '<div class="islabeya-resumen-envio__producto-cabecera">';
			echo '<span class="islabeya-resumen-envio__producto-nombre">' . esc_html( $item->get_name() ) . '</span>';
			echo '<span class="islabeya-resumen-envio__producto-qty">&times; ' . esc_html( $item->get_quantity() ) . '</span>';
			echo '</div>';

			$es_envio = in_array( $info['tipo'], array( 'moto_aerovaradero', 'moto_gratis', 'combo_gratis', 'gratis', 'gratis_provincia' ), true );
			echo '<div class="islabeya-resumen-envio__badge islabeya-resumen-envio__badge--' . ( $es_envio ? 'envio' : 'recogida' ) . '">';
			
			// Para gratis-provincia, mostrar imágenes + nombre de provincia
			if ( $info['tipo'] === 'gratis_provincia' ) {
				$primera_imagen = get_parent_theme_file_uri( '/assets/img/Gratis provincia.svg' );
				$segunda_imagen = get_parent_theme_file_uri( '/assets/img/Delivery.svg' );
				echo '<img src="' . esc_url( $primera_imagen ) . '" alt="" style="width: auto; height: 1rem; margin-right: 4px;">';
				echo '<img src="' . esc_url( $segunda_imagen ) . '" alt="" style="width: auto; height: 0.8rem; margin-right: 4px;">';
				echo esc_html( $info['etiqueta'] );
			} else {
				echo esc_html( $info['etiqueta'] );
			}
			
			echo '</div>';

			islabeya_checkout_render_info_envio_producto( $product_id );

			echo '</div>';
		}

		echo '</div>';
		return;
	}

	// Fallback: versión simplificada (envío / recogida).
	$gratuito_products = array();
	$recogida_products = array();
	$vendor_locations  = array();

	foreach ( $order->get_items() as $item_id => $item ) {
		$product_id    = $item->get_product_id();
		$shipping_type = get_post_meta( $product_id, '_shipping_type', true );
		$data          = array(
			'name'     => $item->get_name(),
			'quantity' => $item->get_quantity(),
		);

		if ( 'recogida' === $shipping_type ) {
			$recogida_products[] = $data;
			if ( function_exists( 'dokan_get_vendor_by_product' ) ) {
				$vendor_id = dokan_get_vendor_by_product( $product_id, true );
				if ( $vendor_id && ! isset( $vendor_locations[ $vendor_id ] ) ) {
					$tienda_info = function_exists( 'obtener_direccion_dokan_checkout' ) ? obtener_direccion_dokan_checkout( $vendor_id ) : false;
					if ( $tienda_info && ! empty( $tienda_info['store_name'] ) ) {
						$vendor_locations[ $vendor_id ] = array(
							'store_name'   => $tienda_info['store_name'],
							'address_html' => $tienda_info['html_direccion'],
							'phone'        => $tienda_info['phone'],
							'email'        => $tienda_info['email'],
						);
					}
				}
			}
		} else {
			$gratuito_products[] = $data;
		}
	}

	$has_envio    = ! empty( $gratuito_products );
	$has_recogida = ! empty( $recogida_products );

	if ( ! $has_envio && ! $has_recogida ) {
		return;
	}

	echo '<div class="islabeya-resumen-envio islabeya-gracias__entrega">';
	echo '<div class="islabeya-resumen-envio__titulo">' . esc_html__( 'Método de entrega', 'islabeya' ) . '</div>';

	if ( $has_envio && $has_recogida ) {
		echo '<div class="islabeya-resumen-envio__bloque islabeya-resumen-envio__bloque--gratuito">';
		echo '<div class="islabeya-resumen-envio__subtitulo">' . esc_html__( 'Envío a domicilio', 'islabeya' ) . '</div>';
		foreach ( $gratuito_products as $p ) {
			echo '<div class="islabeya-resumen-envio__item"><span>' . esc_html( $p['name'] ) . ' &times; ' . esc_html( $p['quantity'] ) . '</span><span class="islabeya-resumen-envio__tag">' . esc_html__( 'Enviar', 'islabeya' ) . '</span></div>';
		}
		echo '</div>';

		echo '<div class="islabeya-resumen-envio__bloque islabeya-resumen-envio__bloque--recogida">';
		echo '<div class="islabeya-resumen-envio__subtitulo">' . esc_html__( 'Recogida en local', 'islabeya' ) . '</div>';
		foreach ( $recogida_products as $p ) {
			echo '<div class="islabeya-resumen-envio__item"><span>' . esc_html( $p['name'] ) . ' &times; ' . esc_html( $p['quantity'] ) . '</span><span class="islabeya-resumen-envio__tag">' . esc_html__( 'Recoger', 'islabeya' ) . '</span></div>';
		}
		islabeya_gracias_render_tiendas( $vendor_locations );
		echo '</div>';
	} elseif ( $has_envio ) {
		echo '<div class="islabeya-resumen-envio__bloque islabeya-resumen-envio__bloque--gratuito">';
		echo '<span aria-hidden="true">🚚</span>';
		echo '<span>' . esc_html__( 'Envío gratuito a domicilio', 'islabeya' ) . '</span>';
		echo '</div>';
	} elseif ( $has_recogida ) {
		echo '<div class="islabeya-resumen-envio__bloque islabeya-resumen-envio__bloque--recogida">';
		echo '<span aria-hidden="true">📦</span>';
		echo '<span>' . esc_html__( 'Recogida en el local', 'islabeya' ) . '</span>';
		echo '</div>';
		islabeya_gracias_render_tiendas( $vendor_locations );
	}

	echo '</div>';
}

/**
 * 4b) Tiendas donde recoger la compra (direcciones Dokan).
 */
function islabeya_gracias_render_tiendas( $vendor_locations ) {
	if ( empty( $vendor_locations ) ) {
		return;
	}
	echo '<div class="islabeya-resumen-envio__tiendas">';
	foreach ( $vendor_locations as $vendor_data ) {
		echo '<div class="islabeya-resumen-envio__tienda">';
		echo '<div class="islabeya-resumen-envio__tienda-nombre">' . esc_html( $vendor_data['store_name'] ) . '</div>';
		echo '<div class="islabeya-resumen-envio__tienda-direccion">' . wp_kses_post( $vendor_data['address_html'] ) . '</div>';
		if ( ! empty( $vendor_data['phone'] ) ) {
			echo '<div class="islabeya-resumen-envio__tienda-contacto">' . esc_html( $vendor_data['phone'] ) . '</div>';
		}
		if ( ! empty( $vendor_data['email'] ) ) {
			echo '<div class="islabeya-resumen-envio__tienda-contacto">' . esc_html( $vendor_data['email'] ) . '</div>';
		}
		echo '</div>';
	}
	echo '</div>';
}

