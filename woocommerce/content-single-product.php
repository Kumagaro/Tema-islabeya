<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>
<div class="producto" id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>
	<div class="imagen">
		<?php
		/**
		 * Hook: woocommerce_before_single_product_summary.
		 *
		 * @hooked woocommerce_show_product_sale_flash - 10
		 * @hooked woocommerce_show_product_images - 20
		 */
		woocommerce_show_product_images();
		?>
	</div>

	<div class="summary">
		<div class="sumary-primary">
			<!-- Badge de Tienda / Vendedor con Dokan -->
			<?php
			global $product;
			
			// 1. Obtener el ID del vendedor a partir del ID del producto
			$seller_id = get_post_field( 'post_author', $product->get_id() );
			
			// 2. Usar la función oficial de Dokan para obtener la información de la tienda
			$store_info = dokan_get_store_info( $seller_id );
			
			// 3. Verificar si la tienda tiene un nombre y mostrarla
			if ( ! empty( $store_info['store_name'] ) ) : 
				
				// Opcional: Obtener la URL de la tienda del vendedor
				$store_url = dokan_get_store_url( $seller_id );
				?>
				<div class="badge-tieda" style="display: inline-flex;" onclick="window.location='<?php echo esc_url( $store_url ); ?>'">
					<img width="140" height="42" decoding="async" class="icon-tienda" src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/icons/Tienda.svg' ) ); ?>"  style="width: auto; height: 0.8rem; flex-shrink: 0;">
					<p aria-hidden="true" class="label-text" style="line-height: 1; color: #66391f; margin: 0; font-size: 0.625rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px; min-width: 30px; flex-shrink: 1; flex-grow: 0;">
						<?php echo esc_html( $store_info['store_name'] ); ?>
					</p>
					<img width="24" height="24" decoding="async" class="icon-chocolate" src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/icons/Alt-Arrow-Right.svg' ) ); ?>" alt="Flecha" style="width: 0.6rem; height: auto; flex-shrink: 0;">
				</div>
			<?php endif; ?>
			<?php
			/**
			 * Hook: woocommerce_single_product_summary.
			 *
			 * @hooked woocommerce_template_single_title - 5
			 * @hooked woocommerce_template_single_rating - 10
			 * @hooked woocommerce_template_single_price - 10
			 * @hooked woocommerce_template_single_excerpt - 20
			 * @hooked woocommerce_template_single_add_to_cart - 30
			 * @hooked woocommerce_template_single_meta - 40
			 * @hooked woocommerce_template_single_sharing - 50
			 * @hooked WC_Structured_Data::generate_product_data() - 60
			 */
			woocommerce_template_single_title();
			?>
			<!-- Precio formateado con la misma función que en content-product -->
			<div class="price-wrapper-single">
				<?php
				global $product;
				
				$regular_price = $product->get_regular_price();
				$sale_price = $product->get_sale_price();
				
				if ( $sale_price && $regular_price != $sale_price ) {
					// Calcular porcentaje de descuento
					$discount_percent = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );
					
					// Precio original tachado
					echo '<del aria-hidden="true">';
					echo islabeya_formatear_precio_con_partes( $regular_price );
					echo '<span class="ahorro-badge">-' . $discount_percent . '%</span>';
					echo '</del> ';
					
					// Precio actual con descuento
					echo '<ins aria-hidden="true">';
					echo islabeya_formatear_precio_con_partes( $sale_price );
					echo '</ins>';
					
					// Texto oculto para lectores de pantalla (accesibilidad)
					echo '<span class="screen-reader-text">El precio original era: ' . wc_price( $regular_price ) . '</span>';
					echo '<span class="screen-reader-text">El precio actual es: ' . wc_price( $sale_price ) . '</span>';
				} else {
					// Sin descuento
					echo islabeya_formatear_precio_con_partes( $product->get_price() );
				}
				?>
			</div>
			<?php
			// Mostrar la descripción completa del producto (sin el hook del shortcode)
			global $product;
			$description = $product->get_description();
			if ( ! empty( $description ) ) {
				echo '<div class="woocommerce-product-details__description">';
				echo apply_filters( 'woocommerce_product_description', $description );
				echo '</div>';
			}
			?>
			<?php echo do_shortcode('[badge_envio_producto]');?>
		</div>
		<div class="anadir-carrito">
			<?php woocommerce_template_single_add_to_cart(); ?>
			<div class="badge-envio-shortcode badge-envio-gratis " style="--font-color: #66391F; --background-color: rgba(243, 210, 171, 0.25); display: inline-flex; align-items: center; gap: 4px; border-radius: 5px; background-color: #f4f9ec !important;">
				<img width="123" height="42" src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/Pagos-1.svg' ) ); ?>" style="width: auto; height: 1.2rem !important;" data-lazy-src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/Pagos-1.svg' ) ); ?>" data-ll-status="loaded" class="entered lazyloaded">
				<img width="24" height="24" src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/icons/Lock-Keyhole.svg' ) ); ?>" style="width: auto; height: 1rem;" data-lazy-src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/icons/Lock-Keyhole.svg' ) ); ?>" data-ll-status="loaded" class="entered lazyloaded">
				<p style="margin: 0; font-size: .625rem; font-weight: 600; text-transform: uppercase; color: #66391F; padding-right: 10px;">seguros y privados</p>
			</div>
			<!-- SECCIÓN DE INFORMACIÓN DE LA TIENDA VENDEDORA -->
			<div class="vendor-info-section">
				<?php
				global $product;
				
				// Obtener el ID del vendedor (autor del producto)
				$vendor_id = get_post_field( 'post_author', $product->get_id() );
				
				if ( $vendor_id && function_exists( 'dokan_get_store_info' ) ) :
					$store_info = dokan_get_store_info( $vendor_id );
					$store_url  = dokan_get_store_url( $vendor_id );
					$store_name = ! empty( $store_info['store_name'] ) ? $store_info['store_name'] : get_the_author_meta( 'display_name', $vendor_id );
					$store_avatar = get_avatar_url( $vendor_id, array( 'size' => 60 ) );
					$store_rating = dokan_get_seller_rating( $vendor_id );
					$store_phone  = ! empty( $store_info['phone'] ) ? $store_info['phone'] : '';
					?>
					
					<div class="vendor-info-card">
						<div class="vendor-header">
							<div class="vendor-avatar">
								<img src="<?php echo esc_url( $store_avatar ); ?>" alt="<?php echo esc_attr( $store_name ); ?>">
							</div>
							<div class="vendor-details">
								<h3 class="vendor-name">
									<a href="<?php echo esc_url( $store_url ); ?>"><?php echo esc_html( $store_name ); ?></a>
								</h3>
								<?php if ( $store_rating['rating'] > 0 ) : ?>
									<div class="vendor-rating">
										<div class="stars">
											<?php echo wc_get_rating_html( $store_rating['rating'] ); ?>
										</div>
										<span class="rating-count">(<?php echo esc_html( $store_rating['count'] ); ?> valoraciones)</span>
									</div>
								<?php endif; ?>
								<?php if ( ! empty( $store_phone ) ) : ?>
									<div class="vendor-phone">
										<span class="icon-phone"></span>
										<span><?php echo esc_html( $store_phone ); ?></span>
									</div>
								<?php endif; ?>
							</div>
						</div> 
					</div>
					
				<?php elseif ( $vendor_id ) : ?>
					<div class="vendor-info-card">
						<div class="vendor-header">
							<div class="vendor-avatar">
								<img src="<?php echo esc_url( get_avatar_url( $vendor_id, array( 'size' => 60 ) ) ); ?>" alt="<?php echo esc_attr( get_the_author_meta( 'display_name', $vendor_id ) ); ?>">
							</div>
							<div class="vendor-details">
								<h3 class="vendor-name">
									<a href="<?php echo esc_url( get_author_posts_url( $vendor_id ) ); ?>"><?php echo esc_html( get_the_author_meta( 'display_name', $vendor_id ) ); ?></a>
								</h3>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
<div class="producto"> 
	<div class="valoraciones">
		<?php islabeya_mostrar_valoraciones_producto(); ?>
	</div>
</div>
<div class="producto"> 
	<?php woocommerce_output_related_products(); ?>
</div>
<?php
/**
 * Hook: woocommerce_after_single_product_summary.
 *
 * @hooked woocommerce_output_product_data_tabs - 10
 * @hooked woocommerce_upsell_display - 15
 * @hooked woocommerce_output_related_products - 20
 * 
 * @hooked islabeya_mostrar_descripcion_producto
 * @hooked islabeya_mostrar_valoraciones_producto
 */
?>
