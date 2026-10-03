<?php
/**
 * Review order table - VERSIÓN IslaBeya (resumen de pedido con imágenes)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="shop_table woocommerce-checkout-review-order-table islabeya-review-table">

	<div class="islabeya-review-table__productos">
		<?php
		do_action( 'woocommerce_review_order_before_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				?>
				<div class="islabeya-review-table__item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
					<div class="islabeya-review-table__thumb">
						<?php
						$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_gallery_thumbnail' ), $cart_item, $cart_item_key );
						echo wp_kses_post( $thumbnail );
						?>
						<span class="islabeya-review-table__qty"><?php echo esc_html( $cart_item['quantity'] ); ?></span>
					</div>
					<div class="islabeya-review-table__info">
						<div class="islabeya-review-table__name">
							<?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ); ?>
						</div>
						<div class="islabeya-review-table__meta">
							<?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					</div>
					<div class="islabeya-review-table__price">
						<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				</div>
				<?php
			}
		}

		do_action( 'woocommerce_review_order_after_cart_contents' );
		?>
	</div>

	<div class="islabeya-review-table__totales">

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<div class="islabeya-review-table__fila cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<span class="islabeya-review-table__label"><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
				<span class="islabeya-review-table__valor"><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
			</div>
		<?php endforeach; ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<div class="islabeya-review-table__fila fee">
				<span class="islabeya-review-table__label"><?php echo esc_html( $fee->name ); ?></span>
				<span class="islabeya-review-table__valor"><?php wc_cart_totals_fee_html( $fee ); ?></span>
			</div>
		<?php endforeach; ?>

		<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
			<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
				<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
					<div class="islabeya-review-table__fila tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
						<span class="islabeya-review-table__label"><?php echo esc_html( $tax->label ); ?></span>
						<span class="islabeya-review-table__valor"><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
					</div>
				<?php endforeach; ?>
			<?php else : ?>
				<div class="islabeya-review-table__fila tax-total">
					<span class="islabeya-review-table__label"><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></span>
					<span class="islabeya-review-table__valor"><?php wc_cart_totals_taxes_total_html(); ?></span>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

		<div class="islabeya-review-table__fila islabeya-review-table__fila--total order-total">
			<span class="islabeya-review-table__label"><?php esc_html_e( 'Total', 'woocommerce' ); ?></span>
			<span class="islabeya-review-table__valor"><?php wc_cart_totals_order_total_html(); ?></span>
		</div>

		<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

	</div>
</div>

