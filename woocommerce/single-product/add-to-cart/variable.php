<?php
/**
 * Variable product add to cart
 * Versión que mantiene la estructura original de WooCommerce
 */

defined( 'ABSPATH' ) || exit;

global $product;

$attribute_keys  = array_keys( $attributes );
$variations_json = wp_json_encode( $available_variations );
$variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );

do_action( 'woocommerce_before_add_to_cart_form' );
?>

<form class="variations_form cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo $variations_attr; ?>">
	<?php do_action( 'woocommerce_before_variations_form' ); ?>

	<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
		<p class="stock out-of-stock"><?php echo esc_html( apply_filters( 'woocommerce_out_of_stock_message', __( 'This product is currently out of stock and unavailable.', 'woocommerce' ) ) ); ?></p>
	<?php else : ?>
		<table class="variations" cellspacing="0" role="presentation">
			<tbody>
				<?php foreach ( $attributes as $attribute_name => $options ) : ?>
					<tr>
						<th class="label"><label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>"><?php echo wc_attribute_label( $attribute_name ); ?></label></th>
						<td class="value">
							<?php
								wc_dropdown_variation_attribute_options(
									array(
										'options'   => $options,
										'attribute' => $attribute_name,
										'product'   => $product,
									)
								);
								
								// === SELECTORES VISUALES ===
								echo '<div class="islabeya-swatches" data-attribute="' . esc_attr( sanitize_title( $attribute_name ) ) . '">';
								
								foreach ( $options as $option ) {
									$term = get_term_by( 'slug', $option, $attribute_name );
									$term_name = $term ? $term->name : $option;
									$term_slug = $term ? $term->slug : $option;
									
									$color_value = islabeya_get_color_hex( $term_name );
									
									if ( $color_value ) {
										echo '<span class="swatch swatch-color" data-value="' . esc_attr( $term_slug ) . '" data-attribute="' . esc_attr( sanitize_title( $attribute_name ) ) . '" data-tooltip="' . esc_attr( $term_name ) . '" style="background-color: ' . esc_attr( $color_value ) . ';"></span>';
									} else {
										echo '<span class="swatch swatch-text" data-value="' . esc_attr( $term_slug ) . '" data-attribute="' . esc_attr( sanitize_title( $attribute_name ) ) . '">' . esc_html( $term_name ) . '</span>';
									}
								}
								
								echo '</div>';
							?>
							<?php echo end( $attribute_keys ) === $attribute_name ? wp_kses_post( apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__( 'Clear', 'woocommerce' ) . '</a>' ) ) : ''; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<?php do_action( 'woocommerce_after_variations_table' ); ?>

		<div class="single_variation_wrap">
			<?php
				do_action( 'woocommerce_before_single_variation' );
				do_action( 'woocommerce_single_variation' );
				do_action( 'woocommerce_after_single_variation' );
			?>
		</div>
	<?php endif; ?>

	<?php do_action( 'woocommerce_after_variations_form' ); ?>
</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>