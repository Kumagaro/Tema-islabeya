<?php
/**
 * "Order received" message — VERSIÓN IslaBeya.
 *
 * Como el layout de 2 columnas se construye en thankyou.php, este archivo
 * solo define el mensaje de confirmación personalizado.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/order-received.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.8.0
 *
 * @var WC_Order|false $order
 */

defined( 'ABSPATH' ) || exit;
?>

<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received islabeya-gracias__recibido">
	<?php
	/**
	 * Filter the message shown after a checkout is complete.
	 *
	 * @since 2.2.0
	 *
	 * @param string         $message The message.
	 * @param WC_Order|false $order   The order created during checkout, or false if order data is not available.
	 */
	$message = apply_filters(
		'woocommerce_thankyou_order_received_text',
		sprintf(
			/* translators: %s: order number */
			__( '¡Gracias por tu compra! Tu pedido nº %s ha sido recibido.', 'islabeya' ),
			$order ? $order->get_order_number() : ''
		),
		$order
	);

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $message;
	?>
</p>
