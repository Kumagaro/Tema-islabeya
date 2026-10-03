<?php
/**
 * The template for displaying product content within loops
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility
if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}
?>

<div class="producto-moto">
    <!-- Columna Principal -->
    <div class="columnas-principal">
        <!-- Imagen del Producto -->
        <figure>
            <a href="<?php the_permalink(); ?>">
                <?php
                // Función nativa de WooCommerce para la thumbnail
                if ( function_exists( 'woocommerce_get_product_thumbnail' ) ) {
                    echo woocommerce_get_product_thumbnail( 'woocommerce_thumbnail' );
                } else {
                    echo get_the_post_thumbnail( get_the_ID(), 'woocommerce_thumbnail' );
                }
                ?>
            </a>
            <figcaption></figcaption>
        </figure>
        <!-- Título del Producto -->
        <h3 class="font-medium">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
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
            <div class="badge-tieda" onclick="window.location='<?php echo esc_url( $store_url ); ?>'">
                <img width="140" height="42" decoding="async" class="icon-tienda" src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/icons/Tienda.svg' ) ); ?>"  style="width: auto; height: 0.8rem; flex-shrink: 0;">
                <p aria-hidden="true" class="label-text" style="line-height: 1; color: #66391f; margin: 0; font-size: 0.625rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px; min-width: 30px; flex-shrink: 1; flex-grow: 0;">
                    <?php echo esc_html( $store_info['store_name'] ); ?>
                </p>
                <img width="24" height="24" decoding="async" class="icon-chocolate" src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/icons/Alt-Arrow-Right.svg' ) ); ?>" alt="Flecha" style="width: 0.6rem; height: auto; flex-shrink: 0;">
            </div>
        <?php endif; ?>
        <!-- Wishlist (YITH) -->
        <?php echo do_shortcode( '[yith_wcwl_add_to_wishlist]' ); ?>
        <!-- Envio Gratis -->
        <?php echo do_shortcode('[badge_envio]'); ?>
    </div>
    
    <!-- Columna de Precio y Carrito -->
    <div class="columna-precio">
        <h3 class="precio-dinamico">
            <?php
            global $product;
            
            $regular_price = $product->get_regular_price();
            $sale_price    = $product->get_sale_price();
            
            if ( $sale_price && $regular_price != $sale_price ) {
                $discount_percent = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );
                
                // Precio original tachado
                echo '<del aria-hidden="true" class="precio-original">';
                echo islabeya_formatear_precio_con_partes( $regular_price ); // ← Llama a la función de functions.php
                echo '<span class="ahorro-badge">- ' . $discount_percent . '%</span>';
                echo '</del>';
                
                // Precio actual con descuento
                echo '<ins aria-hidden="true" class="precio-actual">';
                echo islabeya_formatear_precio_con_partes( $sale_price ); // ← Llama a la función de functions.php
                echo '</ins>';
            } else {
                echo '<span class="precio-normal">';
                echo islabeya_formatear_precio_con_partes( $product->get_price() ); // ← Llama a la función de functions.php
                echo '</span>';
            }
            ?>
        </h3>
        
        <div class="btn btn-second">
            <?php woocommerce_template_loop_add_to_cart(); ?>
        </div>
    </div>
</div>