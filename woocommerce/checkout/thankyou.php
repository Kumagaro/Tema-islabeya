<?php
/**
 * Thankyou page — VERSIÓN IslaBeya (2 columnas)
 *
 * Columna izquierda: mensaje de éxito, overview, direcciones, método de entrega y acciones.
 * Columna derecha: resumen del pedido (productos y totales).
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.1.0
 *
 * @var WC_Order $order
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="woocommerce-order">

	<?php
	if ( $order ) :

		do_action( 'woocommerce_before_thankyou', $order->get_id() );
		?>

		<?php if ( $order->has_status( 'failed' ) ) : ?>

			<div class="islabeya-gracias__layout islabeya-gracias__layout--fallo">
				<div class="islabeya-gracias__col-main">
					<div class="islabeya-gracias__fallo">
						<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>

						<div class="islabeya-gracias__fallo-acciones">
							<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php esc_html_e( 'Pay', 'woocommerce' ); ?></a>
							<?php if ( is_user_logged_in() ) : ?>
								<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php esc_html_e( 'My account', 'woocommerce' ); ?></a>
							<?php endif; ?>
						</div>
					</div>
				</div>

				<?php if ( function_exists( 'islabeya_gracias_productos' ) ) : ?>
					<aside class="islabeya-gracias__col-resumen">
						<div class="islabeya-checkout-resumen__inner">
							<h3 class="islabeya-checkout-resumen__titulo"><?php esc_html_e( 'Resumen del pedido', 'islabeya' ); ?></h3>
							<?php islabeya_gracias_productos( $order ); ?>
							<?php if ( function_exists( 'islabeya_gracias_totales' ) ) : ?>
								<?php islabeya_gracias_totales( $order ); ?>
							<?php endif; ?>
						</div>
					</aside>
				<?php endif; ?>
			</div>

		<?php else : ?>

			<div class="islabeya-gracias__layout">

				<!-- ============ COLUMNA IZQUIERDA: CONFIRMACIÓN ============ -->
				<div class="islabeya-gracias__col-main">

					<div class="islabeya-gracias__mensaje">
						<p class="islabeya-gracias__mensaje-texto">
							<span class="islabeya-gracias__check" aria-hidden="true">✓</span>
							<?php esc_html_e( '¡Gracias por tu compra! Hemos recibido tu pedido', 'islabeya' ); ?>
						</p>
					</div>

					<?php wc_get_template( 'checkout/order-received.php', array( 'order' => $order ) ); ?>

					<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details islabeya-gracias__overview">
 
						<li class="woocommerce-order-overview__order order">
							<?php esc_html_e( 'Número de pedido:', 'islabeya' ); ?>
							<strong><?php echo $order->get_order_number(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
						</li>

						<li class="woocommerce-order-overview__date date">
							<?php esc_html_e( 'Fecha:', 'islabeya' ); ?>
							<strong><?php echo wc_format_datetime( $order->get_date_created() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
						</li>

						<?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
							<li class="woocommerce-order-overview__email email">
								<?php esc_html_e( 'Email:', 'islabeya' ); ?>
								<strong><?php echo $order->get_billing_email(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
							</li>
						<?php endif; ?>

						<li class="woocommerce-order-overview__total total">
							<?php esc_html_e( 'Total:', 'islabeya' ); ?>
							<strong><?php echo $order->get_formatted_order_total(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
						</li>

						<?php if ( $order->get_payment_method_title() ) : ?>
							<li class="woocommerce-order-overview__payment-method method">
								<?php esc_html_e( 'Método de pago:', 'islabeya' ); ?>
								<strong><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></strong>
							</li>
						<?php endif; ?>

					</ul>

					<?php if ( function_exists( 'islabeya_gracias_direcciones' ) ) : ?>
						<?php islabeya_gracias_direcciones( $order ); ?>
					<?php endif; ?>

					<div class="islabeya-gracias__acciones">
						<a class="islabeya-gracias__accion islabeya-gracias__accion--secundaria" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
							<span aria-hidden="true">←</span>
							<?php esc_html_e( 'Seguir comprando', 'islabeya' ); ?>
						</a>
						<a class="islabeya-gracias__accion islabeya-gracias__accion--principal" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">
							<?php esc_html_e( 'Ver mi pedido', 'islabeya' ); ?>
							<span aria-hidden="true">→</span>
						</a>
					</div>

				</div>

				<!-- ============ COLUMNA DERECHA: RESUMEN DEL PEDIDO ============ -->
				<aside class="islabeya-gracias__col-resumen">

					<div class="islabeya-checkout-resumen__inner">
						<h3 class="islabeya-checkout-resumen__titulo"><?php esc_html_e( 'Resumen del pedido', 'islabeya' ); ?></h3>

						<?php if ( function_exists( 'islabeya_gracias_productos' ) ) : ?>
							<?php islabeya_gracias_productos( $order ); ?>
						<?php endif; ?>

						<?php if ( function_exists( 'islabeya_gracias_totales' ) ) : ?>
							<?php islabeya_gracias_totales( $order ); ?>
						<?php endif; ?>
					</div>

					<?php if ( function_exists( 'islabeya_gracias_resumen_envio' ) ) : ?>
						<?php islabeya_gracias_resumen_envio( $order ); ?>
					<?php endif; ?>

				</aside>

			</div>

		<?php endif; ?>

	<?php else : ?>

		<?php wc_get_template( 'checkout/order-received.php', array( 'order' => false ) ); ?>

	<?php endif; ?>

</div>
