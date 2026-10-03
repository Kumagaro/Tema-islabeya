<?php
/**
 * Checkout shipping information form - VERSIÓN IslaBeya
 *
 * - SIEMPRE muestra la dirección de envío (sin checkbox "¿Enviar a otra dirección?").
 * - Sin notas de pedido (campos adicionales eliminados).
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-shipping.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 * @global WC_Checkout $checkout
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce-shipping-fields">
	<?php if ( true === WC()->cart->needs_shipping_address() ) : ?>

		<?php
		/**
		 * Obligatorio para WooCommerce: sin este flag marcado, el core y el JS
		 * tratan el envío como igual a la facturación y sobrescriben
		 * shipping_country con billing_country (p. ej. CR).
		 * El checkout de IslaBeya siempre usa dirección de envío separada (Cuba).
		 */
		?>
		<div id="ship-to-different-address" class="islabeya-ship-to-different">
			<input
				id="ship-to-different-address-checkbox"
				class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox"
				type="checkbox"
				name="ship_to_different_address"
				value="1"
				checked="checked"
				readonly
				aria-hidden="true"
				tabindex="-1"
			/>
		</div>

		<div class="shipping_address">

			<?php do_action( 'woocommerce_before_checkout_shipping_form', $checkout ); ?>

			<div class="woocommerce-shipping-fields__field-wrapper">
				<?php
				// Usar campos nativos de WooCommerce con priorities asignados en functions.php
				$fields = $checkout->get_checkout_fields( 'shipping' );
				
				foreach ( $fields as $key => $field ) {
					woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
				}
				?>
			</div>

			<?php do_action( 'woocommerce_after_checkout_shipping_form', $checkout ); ?>

		</div>

	<?php endif; ?>
</div>

