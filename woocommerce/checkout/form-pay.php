<?php
/**
 * Pay for order form — VERSIÓN IslaBeya (2 columnas)
 *
 * Columna izquierda: métodos de pago y botón "Pagar + Total".
 * Columna derecha: resumen del pedido (productos, totales, método de entrega).
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-pay.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.2.0
 */

defined( 'ABSPATH' ) || exit;

$totals = $order->get_order_item_totals(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

// Texto del botón "Pagar + Total" (igual que el checkout).
$pay_total   = $order->get_total();
$pay_total_html = wp_kses_post( wc_price( $pay_total ) );
$pay_button_text = sprintf( __( 'Pagar %s', 'islabeya' ), $pay_total_html );
?>
<div class="islabeya-gracias__layout islabeya-pay-order">

	<!-- ============ COLUMNA IZQUIERDA: PAGO ============ -->
	<div class="islabeya-gracias__col-main">

		<div class="islabeya-gracias__mensaje islabeya-pay-order__cabecera">
			<p class="islabeya-gracias__mensaje-texto">
				<span class="islabeya-gracias__check islabeya-gracias__check--info" aria-hidden="true">!</span>
				<?php esc_html_e( 'Finaliza el pago de tu pedido', 'islabeya' ); ?>
			</p>
		</div>

		<form id="order_review" method="post" class="islabeya-pay-order__payment">

			<?php
			/**
			 * Triggered from within the checkout/form-pay.php template, immediately before the payment section.
			 *
			 * @since 8.2.0
			 */
			do_action( 'woocommerce_pay_order_before_payment' );
			?>

			<div id="payment">
				<?php if ( $order->needs_payment() ) : ?>
					<ul class="wc_payment_methods payment_methods methods">
						<?php
						if ( ! empty( $available_gateways ) ) {
							foreach ( $available_gateways as $gateway ) {
								wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
							}
						} else {
							echo '<li>';
							wc_print_notice( apply_filters( 'woocommerce_no_available_payment_methods_message', esc_html__( 'Sorry, it seems that there are no available payment methods for your location. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) ), 'notice' ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
							echo '</li>';
						}
						?>
					</ul>
				<?php endif; ?>
				<div class="form-row">
					<input type="hidden" name="woocommerce_pay" value="1" />

					<?php wc_get_template( 'checkout/terms.php' ); ?>

					<?php do_action( 'woocommerce_pay_order_before_submit' ); ?>

					<?php echo apply_filters( 'woocommerce_pay_order_button_html', '<button type="submit" class="button alt' . esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ) . '" id="place_order" value="' . esc_attr( $pay_button_text ) . '" data-value="' . esc_attr( $pay_button_text ) . '">' . wp_kses_post( $pay_button_text ) . '</button>' ); // @codingStandardsIgnoreLine ?>

					<?php do_action( 'woocommerce_pay_order_after_submit' ); ?>

					<?php wp_nonce_field( 'woocommerce-pay', 'woocommerce-pay-nonce' ); ?>
				</div>
			</div>

		</form>

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

			<?php if ( function_exists( 'islabeya_gracias_resumen_envio' ) ) : ?>
				<?php islabeya_gracias_resumen_envio( $order ); ?>
			<?php endif; ?>

			<a class="islabeya-gracias__enlace islabeya-gracias__enlace--tienda" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
				<span class="islabeya-checkout-resumen__seguir-comprando-arrow" aria-hidden="true">←</span>
				<?php esc_html_e( 'Seguir comprando', 'islabeya' ); ?>
			</a>
		</div>

	</aside>

</div>

