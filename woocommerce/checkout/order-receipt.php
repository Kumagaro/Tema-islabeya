<?php
/**
 * Checkout Order Receipt Template — VERSIÓN IslaBeya (2 columnas)
 *
 * Columna izquierda: resumen del pedido (número, fecha, total, método de pago).
 * Columna derecha: productos y totales.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/order-receipt.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="islabeya-gracias__layout islabeya-gracias__receipt-wrap">

	<!-- ============ COLUMNA IZQUIERDA: DETALLES ============ -->
	<div class="islabeya-gracias__col-main">

		<div class="islabeya-gracias__mensaje">
			<p class="islabeya-gracias__mensaje-texto">
				<?php esc_html_e( 'Su pedido se está procesando', 'islabeya' ); ?>
			</p>
			<?php do_action( 'woocommerce_receipt_' . $order->get_payment_method(), $order->get_id() ); ?>
		</div>

		<div class="islabeya-gracias__receipt">
			<ul class="islabeya-gracias__receipt-ul">
				<li class="order">
					<?php esc_html_e( 'Número de pedido:', 'islabeya' ); ?>
					<strong><?php echo esc_html( $order->get_order_number() ); ?></strong>
				</li>
				<li class="date">
					<?php esc_html_e( 'Fecha:', 'islabeya' ); ?>
					<strong><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></strong>
				</li>
				<li class="total">
					<?php esc_html_e( 'Total:', 'islabeya' ); ?>
					<strong><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></strong>
				</li>
				<?php if ( $order->get_payment_method_title() ) : ?>
					<li class="method">
						<?php esc_html_e( 'Método de pago:', 'islabeya' ); ?>
						<strong><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></strong>
					</li>
				<?php endif; ?>
			</ul>

			<?php if ( function_exists( 'islabeya_gracias_direcciones' ) ) : ?>
				<?php islabeya_gracias_direcciones( $order ); ?>
			<?php endif; ?>
		</div>

	</div>

	<!-- ============ COLUMNA DERECHA: RESUMEN ============ -->
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
		</div>

	</aside>

</div>

<div class="clear"></div>
