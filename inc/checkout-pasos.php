<?php
/**
 * ============================================
 * CHECKOUT DE 2 COLUMNAS CON 3 PASOS - IslaBeya
 * Helpers PHP para el nuevo checkout
 * ============================================
 *
 * - Detecta si el carrito necesita envío a domicilio (productos "gratuito").
 * - Render del cupón en la columna derecha (con AJAX de WooCommerce).
 * - Render del bloque de resumen de envío (mixto: recogida + envío gratuito).
 * - Hace opcionales los campos de envío cuando no hay productos "gratuito".
 * - Elimina el cupón del hook superior de WooCommerce.
 * - Inyecta fragmento extra en update_order_review para refrescar el cupón.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 1) ¿El carrito contiene productos de tipo envío a domicilio ("gratuito")?
 *    Si NO hay ningún producto "gratuito" (todo es recogida), el paso 2 no se muestra.
 */
function islabeya_carrito_necesita_envio_domicilio() {
	if ( ! WC()->cart || WC()->cart->is_empty() ) {
		return false;
	}

	foreach ( WC()->cart->get_cart() as $cart_item ) {
		// Usar variation_id si existe, sino product_id
		$product_id = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];
		$shipping_type = get_post_meta( $product_id, '_shipping_type', true );
		
		// Si es variación y no tiene shipping_type, buscar en el padre
		if ( empty( $shipping_type ) && ! empty( $cart_item['variation_id'] ) ) {
			$product = wc_get_product( $product_id );
			if ( $product && $product->is_type( 'variation' ) ) {
				$parent_id = $product->get_parent_id();
				if ( $parent_id ) {
					$shipping_type = get_post_meta( $parent_id, '_shipping_type', true );
				}
			}
		}
		
		// Default: si el meta no existe, se considera "gratuito" (según asignar-envio-pordefecto.php).
		if ( 'recogida' !== $shipping_type ) {
			return true;
		}
	}

	return false;
}

/**
 * 2) ¿El carrito contiene productos de tipo "recogida" (en local)?
 */
function islabeya_carrito_tiene_recogida() {
	if ( ! WC()->cart || WC()->cart->is_empty() ) {
		return false;
	}

	foreach ( WC()->cart->get_cart() as $cart_item ) {
		// Usar variation_id si existe, sino product_id
		$product_id = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];
		$shipping_type = get_post_meta( $product_id, '_shipping_type', true );
		
		// Si es variación y no tiene shipping_type, buscar en el padre
		if ( empty( $shipping_type ) && ! empty( $cart_item['variation_id'] ) ) {
			$product = wc_get_product( $product_id );
			if ( $product && $product->is_type( 'variation' ) ) {
				$parent_id = $product->get_parent_id();
				if ( $parent_id ) {
					$shipping_type = get_post_meta( $parent_id, '_shipping_type', true );
				}
			}
		}
		
		if ( 'recogida' === $shipping_type ) {
			return true;
		}
	}

	return false;
}

/**
 * 3) Quitar el toggle de cupón que WooCommerce muestra arriba del formulario.
 *    El cupón vivirá en la columna derecha (resumen del pedido).
 */
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );


/**
 * 5) Render del CUPÓN en la columna derecha.
 *    Formulario propio (NO anidado dentro de form.checkout para evitar HTML inválido).
 *    Usa los endpoints AJAX nativos de WooCommerce (apply_coupon / remove_coupon).
 */
function islabeya_checkout_cupon_columna_derecha() {
	if ( ! wc_coupons_enabled() ) {
		return;
	}
	?>
	<div class="islabeya-checkout-coupon">
		<div class="islabeya-checkout-coupon__titulo">
			<span class="icon icon-tag" aria-hidden="true"></span>
			<span><?php esc_html_e( '¿Tienes un cupón?', 'islabeya' ); ?></span>
		</div>

		<form class="islabeya-checkout-coupon__form" id="islabeya-coupon-form" method="post">
			<div class="islabeya-checkout-coupon__row">
				<input
					type="text"
					name="coupon_code"
					id="islabeya-coupon-code"
					class="input-text islabeya-coupon-input"
					placeholder="<?php esc_attr_e( 'Código de cupón', 'islabeya' ); ?>"
					value=""
				/>
				<button type="submit" class="button islabeya-coupon-btn" name="apply_coupon" value="Aplicar">
					<?php esc_html_e( 'Aplicar', 'islabeya' ); ?>
				</button>
			</div>
			<div class="islabeya-checkout-coupon__mensaje" id="islabeya-coupon-message" role="alert"></div>
		</form>

		<?php
		// Mostrar cupones ya aplicados con opción de eliminar.
		if ( ! empty( WC()->cart->get_applied_coupons() ) ) :
			?>
			<ul class="islabeya-checkout-coupon__applied">
				<?php foreach ( WC()->cart->get_applied_coupons() as $code ) : ?>
					<li class="islabeya-checkout-coupon__item" data-coupon="<?php echo esc_attr( $code ); ?>">
						<span class="islabeya-checkout-coupon__code"><?php echo esc_html( $code ); ?></span>
						<a href="#" class="islabeya-checkout-coupon__remove" data-coupon="<?php echo esc_attr( $code ); ?>" aria-label="<?php esc_attr_e( 'Eliminar cupón', 'islabeya' ); ?>">&times;</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * 5b) Fecha estimada de entrega (rango de 60-90 días desde hoy).
 */
function islabeya_checkout_rango_fecha_estimada() {
	$timezone = new DateTimeZone( get_option( 'timezone_string' ) ?: 'UTC' );
	$hoy      = new DateTime( 'now', $timezone );
	$futuroIdeal = clone $hoy;
	$futuroIdeal->modify( '+60 days' );
	$futuroReal   = clone $hoy;
	$futuroReal->modify( '+90 days' );
	return date_i18n( 'j M', $futuroIdeal->getTimestamp() ) . ' - ' . date_i18n( 'j M', $futuroReal->getTimestamp() );
}

/**
 * 5c) Determinar el tipo de envío REAL de un producto del carrito/pedido,
 *     reutilizando la misma lógica del badge (inc/badge-envio-info-producto.php).
 *
 * Casos:
 *  - Moto con cobertura Aerovaradero  -> 'moto_aerovaradero' (envío gratis + detalles Aerovaradero)
 *  - Moto sin cobertura                -> 'moto_gratis' (envío gratis a domicilio)
 *  - Comboxpress (ubicación Villa Clara)-> 'combo_gratis' (envío gratis a Villa Clara)
 *  - Comboxpress (otra ubicación)       -> 'combo_recogida' (recogida en tienda Villa Clara)
 *  - Producto 'recogida'                -> 'recogida' (recogida en local)
 *  - Producto 'gratuito' con provincia que coincide con el vendedor -> 'gratis'
 *  - Producto 'gratuito' con provincia distinta -> 'recogida' (recogida en local)
 *
 * @param int|WC_Product $product_id ID o producto.
 * @return array{ tipo:string, etiqueta:string, tienda:array }
 */
function islabeya_checkout_obtener_info_envio_producto( $product_id ) {
	if ( $product_id instanceof WC_Product ) {
		$product_id = $product_id->get_id();
	}
	$product_id = (int) $product_id;
	if ( ! $product_id ) {
		return array(
			'tipo'     => 'gratis',
			'etiqueta' => __( 'Envío gratis a domicilio', 'islabeya' ),
			'tienda'   => array(),
		);
	}

	// Si es una variación, intentar obtener el shipping_type de la variación primero
	$shipping_type = get_post_meta( $product_id, '_shipping_type', true );
	if ( empty( $shipping_type ) ) {
		// Si no hay en la variación, buscar en el producto padre
		$product = wc_get_product( $product_id );
		if ( $product && $product->is_type( 'variation' ) ) {
			$parent_id = $product->get_parent_id();
			if ( $parent_id ) {
				$shipping_type = get_post_meta( $parent_id, '_shipping_type', true );
				// Actualizar product_id al padre para usar en el resto de la función
				$product_id = $parent_id;
			}
		}
	}

	// Provincia del vendedor / tienda.
	$provincia_vendedor = function_exists( 'obtener_provincia_tienda' ) ? obtener_provincia_tienda( $product_id ) : '';
	$tienda_info        = array();
	if ( function_exists( 'obtener_info_tienda_completa' ) ) {
		$tienda_info = obtener_info_tienda_completa( $product_id );
	}

	// ============ PRIORIDAD: TIPO DE ENVÍO DEL PRODUCTO ============
	// Primero verificamos el shipping_type directamente (como en badge y card)
	
	if ( 'recogida' === $shipping_type ) {
		return array(
			'tipo'     => 'recogida',
			'etiqueta' => __( 'Recogida en local', 'islabeya' ),
			'tienda'   => $tienda_info,
		);
	}

	// gratis-provincia → envío gratis solo a provincias específicas
	if ( 'gratis-provincia' === $shipping_type ) {
		// Obtener provincias permitidas usando taxonomía nativa
		$provincias_terms = wp_get_post_terms( $product_id, 'provincia' );
		$provincia_nombre = '';
		
		if ( ! empty( $provincias_terms ) && ! is_wp_error( $provincias_terms ) ) {
			$provincia_nombre = $provincias_terms[0]->name;
		} else {
			$provincia_nombre = __( 'Provincia específica', 'islabeya' );
		}
		
		return array(
			'tipo'     => 'gratis_provincia',
			'etiqueta' => $provincia_nombre,
			'tienda'   => $tienda_info,
			'provincia' => $provincia_nombre,
		);
	}

	// ============ LÓGICA ESPECIAL PARA MOTOS Y COMBOXPRESS ============
	// Solo si no hay shipping_type específico, aplicar lógica especial
	
	$es_moto = function_exists( 'has_term' ) && has_term( 'motos', 'product_cat', $product_id );
	$es_comboxpress = function_exists( 'es_tienda_comboxpress' ) && es_tienda_comboxpress( $product_id );

	// Provincia seleccionada en sesión (ubicación).
	$provincia_seleccionada = '';
	if ( isset( $_SESSION['province'] ) && intval( $_SESSION['province'] ) !== 0 ) {
		$location_term = get_term( intval( $_SESSION['province'] ), 'location' );
		if ( $location_term && ! is_wp_error( $location_term ) ) {
			$provincia_seleccionada = $location_term->name;
		}
	}

	$coinciden = false;
	if ( ! empty( $provincia_vendedor ) && ! empty( $provincia_seleccionada ) ) {
		$coinciden = ( strtolower( trim( $provincia_vendedor ) ) === strtolower( trim( $provincia_seleccionada ) ) );
	}

	// ============ MOTOS ============
	if ( $es_moto ) {
		// Todas las motos tienen envío GRATIS.
		// Con cobertura Aerovaradero (coincide provincia vendedor) se muestran los detalles de Aerovaradero.
		if ( $coinciden && ! empty( $provincia_seleccionada ) ) {
			return array(
				'tipo'     => 'moto_aerovaradero',
				'etiqueta' => __( 'Envío a través de Aerovaradero S.A.', 'islabeya' ),
				'tienda'   => $tienda_info,
			);
		}

		// Sin cobertura o sin ubicación: envío gratis a domicilio.
		return array(
			'tipo'     => 'moto_gratis',
			'etiqueta' => __( 'Envío gratis a domicilio', 'islabeya' ),
			'tienda'   => $tienda_info,
		);
	}

	// ============ COMBOXPRESS ============
	if ( $es_comboxpress ) {
		$es_villa_clara = ( strtolower( trim( $provincia_seleccionada ) ) === 'villa clara' );
		if ( $es_villa_clara ) {
			return array(
				'tipo'     => 'combo_gratis',
				'etiqueta' => __( 'Envío gratis a Villa Clara', 'islabeya' ),
				'tienda'   => $tienda_info,
			);
		}

		return array(
			'tipo'     => 'combo_recogida',
			'etiqueta' => __( 'Recogida en tienda (Villa Clara)', 'islabeya' ),
			'tienda'   => $tienda_info,
		);
	}

	// 'gratuito' (por defecto) → envío gratis si la provincia coincide con el vendedor, si no recogida.
	if ( $coinciden && ! empty( $provincia_seleccionada ) ) {
		return array(
			'tipo'     => 'gratis',
			'etiqueta' => __( 'Envío gratis a domicilio', 'islabeya' ),
			'tienda'   => $tienda_info,
		);
	}

	return array(
		'tipo'     => 'recogida',
		'etiqueta' => __( 'Recogida en local', 'islabeya' ),
		'tienda'   => $tienda_info,
	);
}

/**
 * 5d) Render del detalle de envío de un producto (HTML).
 *
 * @param int|WC_Product $product_id ID o producto.
 */
function islabeya_checkout_render_info_envio_producto( $product_id ) {
	$info = islabeya_checkout_obtener_info_envio_producto( $product_id );
	$tipo = $info['tipo'];
	$tienda = $info['tienda'];

	$rango = islabeya_checkout_rango_fecha_estimada();

	echo '<div class="islabeya-envio-detalle islabeya-envio-detalle--' . esc_attr( $tipo ) . '">';

	switch ( $tipo ) {
		case 'moto_aerovaradero':
			echo '<div class="islabeya-envio-detalle__bloque islabeya-envio-detalle__bloque--verde">';
			echo '<span class="islabeya-envio-detalle__titulo">' . esc_html__( 'Envío a través de Aerovaradero S.A.', 'islabeya' ) . '</span>';
			echo '<p class="islabeya-envio-detalle__linea">' . esc_html__( 'Empresa cubana especializada en carga aérea con más de 32 años de experiencia, certificada ISO 9001:2015.', 'islabeya' ) . '</p>';
			echo '<p class="islabeya-envio-detalle__linea"><strong>' . esc_html__( 'Importante:', 'islabeya' ) . '</strong> ' . esc_html__( 'Las motos se entregan únicamente en municipios cabecera de provincia. Todas las provincias aplican.', 'islabeya' ) . '</p>';
			echo '<p class="islabeya-envio-detalle__linea islabeya-envio-detalle__fecha"><strong>' . esc_html__( 'Entrega estimada:', 'islabeya' ) . '</strong> ' . esc_html( $rango ) . '</p>';
			echo '<p class="islabeya-envio-detalle__linea islabeya-envio-detalle__exito"><span>81,73%</span> ' . esc_html__( 'llegan antes de este rango de días', 'islabeya' ) . '</p>';
			echo '</div>';
			break;

		case 'moto_gratis':
			echo '<div class="islabeya-envio-detalle__bloque islabeya-envio-detalle__bloque--verde">';
			echo '<span class="islabeya-envio-detalle__titulo">' . esc_html__( 'Envío gratis a domicilio', 'islabeya' ) . '</span>';
			echo '<p class="islabeya-envio-detalle__linea"><strong>' . esc_html__( 'Todas las motos se envían gratis a domicilio.', 'islabeya' ) . '</strong></p>';
			echo '<p class="islabeya-envio-detalle__linea islabeya-envio-detalle__fecha"><strong>' . esc_html__( 'Entrega estimada:', 'islabeya' ) . '</strong> ' . esc_html( $rango ) . '</p>';
			echo '<p class="islabeya-envio-detalle__linea islabeya-envio-detalle__exito"><span>81,73%</span> ' . esc_html__( 'llegan antes de este rango de días', 'islabeya' ) . '</p>';
			echo '</div>';
			break;

		case 'combo_gratis':
			echo '<div class="islabeya-envio-detalle__bloque islabeya-envio-detalle__bloque--verde">';
			echo '<span class="islabeya-envio-detalle__titulo">' . esc_html__( 'Envío gratis a Villa Clara', 'islabeya' ) . '</span>';
			echo '<p class="islabeya-envio-detalle__linea islabeya-envio-detalle__fecha"><strong>' . esc_html__( 'Entrega estimada:', 'islabeya' ) . '</strong> ' . esc_html( $rango ) . '</p>';
			echo '<p class="islabeya-envio-detalle__linea islabeya-envio-detalle__exito"><span>81,73%</span> ' . esc_html__( 'llegan antes de este rango de días', 'islabeya' ) . '</p>';
			echo '</div>';
			break;

		case 'combo_recogida':
			echo '<div class="islabeya-envio-detalle__bloque islabeya-envio-detalle__bloque--naranja">';
			echo '<span class="islabeya-envio-detalle__titulo">' . esc_html__( 'Recogida en tienda (Villa Clara)', 'islabeya' ) . '</span>';
			echo '<p class="islabeya-envio-detalle__linea">' . esc_html__( 'Disponible solo con envío gratis para VILLA CLARA. Para otras provincias, recoger en tienda física ubicada en Villa Clara.', 'islabeya' ) . '</p>';
			if ( ! empty( $tienda['nombre'] ) ) {
				echo '<p class="islabeya-envio-detalle__linea"><strong>' . esc_html__( 'Recoger en:', 'islabeya' ) . '</strong> ' . esc_html( $tienda['nombre'] ) . ( ! empty( $tienda['direccion'] ) ? ' - ' . esc_html( $tienda['direccion'] ) : '' ) . '</p>';
			}
			echo '<p class="islabeya-envio-detalle__linea islabeya-envio-detalle__aviso">' . esc_html__( 'Importante: Presentar identificación y comprobante de compra.', 'islabeya' ) . '</p>';
			echo '</div>';
			break;

		case 'recogida':
			echo '<div class="islabeya-envio-detalle__bloque islabeya-envio-detalle__bloque--naranja">';
			echo '<span class="islabeya-envio-detalle__titulo">' . esc_html__( 'Recogida en local', 'islabeya' ) . '</span>';
			if ( ! empty( $tienda['nombre'] ) ) {
				echo '<p class="islabeya-envio-detalle__linea"><strong>' . esc_html__( 'Recoger en:', 'islabeya' ) . '</strong> ' . esc_html( $tienda['nombre'] ) . ( ! empty( $tienda['direccion'] ) ? ' - ' . esc_html( $tienda['direccion'] ) : '' ) . '</p>';
			} else {
				echo '<p class="islabeya-envio-detalle__linea">' . esc_html__( 'Recoge el producto en la tienda física del vendedor.', 'islabeya' ) . '</p>';
			}
			echo '<p class="islabeya-envio-detalle__linea islabeya-envio-detalle__aviso">' . esc_html__( 'Importante: Presentar identificación y comprobante de compra.', 'islabeya' ) . '</p>';
			echo '</div>';
			break;

		case 'gratis_provincia':
			$provincia_nombre = isset( $info['provincia'] ) ? $info['provincia'] : '';
			echo '<div class="islabeya-envio-detalle__bloque islabeya-envio-detalle__bloque--morado">';
			echo '<span class="islabeya-envio-detalle__titulo">' . esc_html__( 'Envío gratis y entrega solo a', 'islabeya' ) . ' ' . esc_html( $provincia_nombre ) . '</span>';
			echo '<p class="islabeya-envio-detalle__linea">' . esc_html__( 'Este producto solo se envía y se entrega a su domicilio en', 'islabeya' ) . ' ' . esc_html( $provincia_nombre ) . '. ' . esc_html__( 'A otras provincias no se hace entrega.', 'islabeya' ) . '</p>';
			echo '<p class="islabeya-envio-detalle__linea islabeya-envio-detalle__fecha"><strong>' . esc_html__( 'Entrega estimada:', 'islabeya' ) . '</strong> ' . esc_html( $rango ) . '</p>';
			echo '<p class="islabeya-envio-detalle__linea islabeya-envio-detalle__exito"><span>81,73%</span> ' . esc_html__( 'llegan antes de este rango de días', 'islabeya' ) . '</p>';
			echo '</div>';
			break;

		case 'gratis':
		default:
			echo '<div class="islabeya-envio-detalle__bloque islabeya-envio-detalle__bloque--verde">';
			echo '<span class="islabeya-envio-detalle__titulo">' . esc_html__( 'Envío gratis a domicilio', 'islabeya' ) . '</span>';
			echo '<p class="islabeya-envio-detalle__linea islabeya-envio-detalle__fecha"><strong>' . esc_html__( 'Entrega estimada:', 'islabeya' ) . '</strong> ' . esc_html( $rango ) . '</p>';
			echo '<p class="islabeya-envio-detalle__linea islabeya-envio-detalle__exito"><span>81,73%</span> ' . esc_html__( 'llegan antes de este rango de días', 'islabeya' ) . '</p>';
			echo '</div>';
			break;
	}

	echo '</div>';
}

/**
 * 6) Render del bloque de RESUMEN DE ENVÍO (mixto recogida / envío gratuito).
 *    Reutiliza la lógica de `obtener_direccion_dokan_checkout()` (inc/obtener-direccion-tienda.php).
 */
function islabeya_checkout_resumen_envio() {
	if ( ! WC()->cart || WC()->cart->is_empty() ) {
		return;
	}

	$has_envio    = islabeya_carrito_necesita_envio_domicilio();
	$has_recogida = islabeya_carrito_tiene_recogida();

	if ( ! $has_envio && ! $has_recogida ) {
		return;
	}

	echo '<div class="islabeya-resumen-envio islabeya-resumen-envio--detallado">';
	echo '<div class="islabeya-resumen-envio__titulo">' . esc_html__( 'Método de entrega', 'islabeya' ) . '</div>';

	foreach ( WC()->cart->get_cart() as $cart_item ) {
		// Usar variation_id si existe, sino product_id (para productos variables)
		$product_id = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];
		$product    = $cart_item['data'] ? $cart_item['data'] : wc_get_product( $product_id );
		$info       = islabeya_checkout_obtener_info_envio_producto( $product_id );

		echo '<div class="islabeya-resumen-envio__producto">';
		echo '<div class="islabeya-resumen-envio__producto-cabecera">';
		echo '<span class="islabeya-resumen-envio__producto-nombre">' . esc_html( $product ? $product->get_name() : '' ) . '</span>';
		echo '<span class="islabeya-resumen-envio__producto-qty">&times; ' . esc_html( $cart_item['quantity'] ) . '</span>';
		echo '</div>';

		// Banda de estado (color verde=envío, naranja=recogida).
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

		// Detalle rico por producto.
		islabeya_checkout_render_info_envio_producto( $product_id );

		echo '</div>';
	}

	echo '</div>';
}

/**
 * 6b) Render de las tiendas donde recoger (dirección Dokan).
 */
function islabeya_checkout_render_tiendas_recogida( $vendor_locations ) {
	if ( empty( $vendor_locations ) ) {
		return;
	}
	echo '<div class="islabeya-resumen-envio__tiendas">';
	foreach ( $vendor_locations as $vendor_data ) {
		echo '<div class="islabeya-resumen-envio__tienda">';
		echo '<div class="islabeya-resumen-envio__tienda-nombre"><span class="icon icon-shop" aria-hidden="true"></span> ' . esc_html( $vendor_data['store_name'] ) . '</div>';
		echo '<div class="islabeya-resumen-envio__tienda-direccion">' . wp_kses_post( $vendor_data['address_html'] ) . '</div>';
		if ( ! empty( $vendor_data['phone'] ) ) {
			echo '<div class="islabeya-resumen-envio__tienda-contacto"><span class="icon icon-phone" aria-hidden="true"></span> ' . esc_html( $vendor_data['phone'] ) . '</div>';
		}
		if ( ! empty( $vendor_data['email'] ) ) {
			echo '<div class="islabeya-resumen-envio__tienda-contacto"><span class="icon icon-letter" aria-hidden="true"></span> ' . esc_html( $vendor_data['email'] ) . '</div>';
		}
		echo '</div>';
	}
	echo '</div>';
}

/**
 * 7) Inyectar fragmento extra en `update_order_review` para refrescar el área del cupón
 *    y el bloque de resumen de envío tras cada recálculo AJAX de WooCommerce.
 */
add_filter( 'woocommerce_update_order_review_fragments', 'islabeya_checkout_fragmento_cupon_resumen' );
function islabeya_checkout_fragmento_cupon_resumen( $fragments ) {
	// Cupón (columna derecha).
	ob_start();
	islabeya_checkout_cupon_columna_derecha();
	$fragments['.islabeya-checkout-coupon'] = ob_get_clean();

	// Resumen de envío (columna derecha).
	ob_start();
	islabeya_checkout_resumen_envio();
	$fragments['.islabeya-resumen-envio'] = ob_get_clean();

	return $fragments;
}

/**
 * 8) Pasar variables de configuración al JS del checkout (nonces, URL ajax, flags).
 */
add_action( 'wp_enqueue_scripts', 'islabeya_checkout_pasos_localize', 100 );
function islabeya_checkout_pasos_localize() {
	if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
		return;
	}

wp_localize_script(
		'islabeya-checkout-nuevo-js',
		'islabeyaCheckoutPasos',
		array(
			'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
			'wcAjaxUrl'        => WC_AJAX::get_endpoint( '%%endpoint%%' ),
			'applyCouponNonce' => wp_create_nonce( 'apply-coupon' ),
			'removeCouponNonce'=> wp_create_nonce( 'remove-coupon' ),
			'needsShipping'    => islabeya_carrito_necesita_envio_domicilio(),
			'hasShippingStep'  => islabeya_carrito_necesita_envio_domicilio(),
			'i18n'             => array(
				'fillRequired' => __( 'Completa los campos obligatorios antes de continuar.', 'islabeya' ),
				'couponApplied'=> __( 'Cupón aplicado correctamente.', 'islabeya' ),
				'couponError'  => __( 'No se pudo aplicar el cupón.', 'islabeya' ),
			),
		)
	);
}

/**
 * 9) Forzar ocultación del cupón superior de WooCommerce también vía CSS (respaldo).
 */
add_action( 'wp_head', 'islabeya_checkout_ocultar_cupon_superior_css' );
function islabeya_checkout_ocultar_cupon_superior_css() {
	if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
		return;
	}
	echo '<style>.woocommerce-form-coupon-toggle, form.checkout_coupon { display:none !important; }</style>';
}

/**
 * 10) País de envío SIEMPRE Cuba.
 *
 * WooCommerce, si no recibe ship_to_different_address, copia billing → shipping
 * y calcula zonas con el país de facturación (p. ej. CR). Esta tienda solo envía a Cuba.
 */
add_action( 'woocommerce_checkout_update_order_review', 'islabeya_forzar_s_country_cuba_en_review', 1 );
function islabeya_forzar_s_country_cuba_en_review( $posted_data ) {
	$_POST['s_country'] = 'CU';
}

add_filter( 'woocommerce_cart_shipping_packages', 'islabeya_forzar_destino_envio_cuba_en_paquetes', 20 );
function islabeya_forzar_destino_envio_cuba_en_paquetes( $packages ) {
	if ( empty( $packages ) || ! is_array( $packages ) ) {
		return $packages;
	}

	foreach ( $packages as $index => $package ) {
		if ( ! isset( $packages[ $index ]['destination'] ) || ! is_array( $packages[ $index ]['destination'] ) ) {
			$packages[ $index ]['destination'] = array();
		}
		$packages[ $index ]['destination']['country'] = 'CU';
	}

	return $packages;
}

add_filter( 'woocommerce_checkout_posted_data', 'islabeya_forzar_pais_envio_cuba_posted', 5 );
function islabeya_forzar_pais_envio_cuba_posted( $data ) {
	// Marcar dirección de envío distinta para que WC no vuelva a copiar billing → shipping.
	if ( WC()->cart && WC()->cart->needs_shipping_address() && ! wc_ship_to_billing_address_only() ) {
		$data['ship_to_different_address'] = true;
	}

	$data['shipping_country'] = 'CU';

	return $data;
}

/**
 * 11) Pedido solo con RECOGIDA: el paso 2 (envío) no se muestra.
 *     Para que la validación de WooCommerce no falle, copiamos facturación -> envío
 *     y limpiamos los errores relacionados con envío.
 */
add_filter( 'woocommerce_checkout_posted_data', 'islabeya_checkout_rellenar_envio_recogida', 20 );
function islabeya_checkout_rellenar_envio_recogida( $data ) {
	if ( islabeya_carrito_necesita_envio_domicilio() ) {
		// Aun con envío a domicilio, el país de envío nunca puede salir de Cuba.
		$data['shipping_country'] = 'CU';
		return $data;
	}

	$copy_fields = array( 'first_name', 'last_name', 'address_1', 'address_2', 'city', 'state', 'postcode', 'country', 'phone', 'email' );
	foreach ( $copy_fields as $field ) {
		$shipping_key = 'shipping_' . $field;
		$billing_key  = 'billing_' . $field;
		if ( empty( $data[ $shipping_key ] ) && ! empty( $data[ $billing_key ] ) ) {
			$data[ $shipping_key ] = $data[ $billing_key ];
		}
	}

	// El país de envío debe ser siempre Cuba para esta tienda.
	$data['shipping_country'] = 'CU';

	if ( empty( $data['shipping_identity_card'] ) && ! empty( $data['billing_identity_card'] ) ) {
		$data['shipping_identity_card'] = $data['billing_identity_card'];
	}
	if ( empty( $data['shipping_house_number'] ) && ! empty( $data['billing_house_number'] ) ) {
		$data['shipping_house_number'] = $data['billing_house_number'];
	}
	if ( empty( $data['shipping_phone_full'] ) && ! empty( $data['billing_phone_full'] ) ) {
		$data['shipping_phone_full'] = $data['billing_phone_full'];
	}

	if ( empty( $data['shipping_postcode'] ) ) {
		$data['shipping_postcode'] = '00000';
	}
	if ( empty( $data['shipping_state'] ) ) {
		$data['shipping_state'] = 'Ciudad de La Habana';
	}

	return $data;
}

add_action( 'woocommerce_after_checkout_validation', 'islabeya_checkout_limpiar_errores_envio_recogida', 10, 2 );
function islabeya_checkout_limpiar_errores_envio_recogida( $data, $errors ) {
	if ( ! $errors || ! is_wp_error( $errors ) ) {
		return;
	}
	if ( islabeya_carrito_necesita_envio_domicilio() ) {
		return;
	}

	// Eliminar errores de envío (en pedidos solo recogida no aplican).
	foreach ( $errors->get_error_codes() as $code ) {
		if ( 'shipping' === $code || 0 === strpos( $code, 'shipping_' ) ) {
			$errors->remove( $code );
		}
	}
}

