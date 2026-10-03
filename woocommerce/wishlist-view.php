<?php
/**
 * Wishlist page template - Custom Grid Layout for IslaBeya
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\WooCommerce\Wishlist\Templates\Wishlist\View
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
    exit;
}

do_action( 'yith_wcwl_before_wishlist_form', $wishlist );
?>

<div class="yith-wcwl-form">
    <div class="wishlist-grid-container">
        <?php
        if ( $wishlist && $wishlist->has_items() ) :
            echo('<div class="productos-wishlist">');
            foreach ( $wishlist_items as $item ) :
                /**
                 * Each of the wishlist items
                 *
                 * @var $item \YITH_WCWL_Wishlist_Item
                 */
                global $product;
                $product = $item->get_product();

                if ( $product && $product->exists() ) :
                    // Configurar el post global para que funcionen las funciones de WooCommerce
                    $post = get_post( $product->get_id() );
                    setup_postdata( $post );
                    ?>
                    
                    <div class="producto-moto">
                        <!-- Columna Principal -->
                        <div class="columnas-principal">
                            <!-- Imagen del Producto -->
                            <figure>
                                <a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>">
                                    <?php
                                    if ( function_exists( 'woocommerce_get_product_thumbnail' ) ) {
                                        echo woocommerce_get_product_thumbnail( 'woocommerce_thumbnail' );
                                    } else {
                                        echo get_the_post_thumbnail( $product->get_id(), 'woocommerce_thumbnail' );
                                    }
                                    ?>
                                </a>
                                <figcaption></figcaption>
                            </figure>
                            
                            <!-- Título del Producto -->
                            <h3 class="font-medium">
                                <a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>">
                                    <?php echo esc_html( $product->get_title() ); ?>
                                </a>
                            </h3>
                            
                            <!-- Badge de Tienda / Vendedor con Dokan -->
                            <?php
                            $seller_id = get_post_field( 'post_author', $product->get_id() );
                            if ( function_exists( 'dokan_get_store_info' ) ) {
                                $store_info = dokan_get_store_info( $seller_id );
                                if ( ! empty( $store_info['store_name'] ) ) : 
                                    $store_url = dokan_get_store_url( $seller_id );
                                    ?>
                                    <div class="badge-tieda" onclick="window.location='<?php echo esc_url( $store_url ); ?>'">
                                        <img class="icon-tienda" src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/icons/Tienda.svg' ) ); ?>" style="width: auto; height: 0.8rem; flex-shrink: 0;">
                                        <p class="label-text" style="line-height: 1; color: #66391f; margin: 0; font-size: 0.625rem;">
                                            <?php echo esc_html( $store_info['store_name'] ); ?>
                                        </p>
                                        <img class="icon-chocolate" src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/icons/Alt-Arrow-Right.svg' ) ); ?>" style="width: 0.6rem; height: auto; flex-shrink: 0;">
                                    </div>
                                <?php endif;
                            }
                            ?>
                            
                            <!-- Wishlist (YITH) - Botón para eliminar de favoritos -->
                            <div class="yith-wcwl-add-to-wishlist">
                                <a href="<?php echo esc_url( $item->get_remove_url() ); ?>" class="remove_from_wishlist button" title="<?php esc_html_e( 'Remove this product', 'yith-woocommerce-wishlist' ); ?>">
                                    &times;
                                </a>
                            </div>
                            
                            <!-- Badge de Envío -->
                            <?php echo do_shortcode('[badge_envio]'); ?>
                        </div>
                        
                        <!-- Columna de Precio y Carrito -->
                        <div class="columna-precio">
                            <h3 class="precio-dinamico">
                                <?php
                                $regular_price = $product->get_regular_price();
                                $sale_price    = $product->get_sale_price();
                                
                                if ( $sale_price && $regular_price != $sale_price ) {
                                    $discount_percent = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );
                                    
                                    echo '<del aria-hidden="true" class="precio-original">';
                                    echo islabeya_formatear_precio_con_partes( $regular_price );
                                    echo '<span class="ahorro-badge">- ' . $discount_percent . '%</span>';
                                    echo '</del>';
                                    
                                    echo '<ins aria-hidden="true" class="precio-actual">';
                                    echo islabeya_formatear_precio_con_partes( $sale_price );
                                    echo '</ins>';
                                } else {
                                    echo '<span class="precio-normal">';
                                    echo islabeya_formatear_precio_con_partes( $product->get_price() );
                                    echo '</span>';
                                }
                                ?>
                            </h3>
                            
                            <div class="btn btn-second">
                                <?php woocommerce_template_loop_add_to_cart( array( 'quantity' => $show_quantity ? $item->get_quantity() : 1 ) ); ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php
                    wp_reset_postdata();
                endif;
            endforeach;
            echo('</div>');
        else : ?>
            <div class="wishlist-empty-message">
                <span class="icon-heart"></span>
                <p><?php echo esc_html( apply_filters( 'yith_wcwl_no_product_to_remove_message', __( 'Se encuentra vacio', 'yith-woocommerce-wishlist' ), $wishlist ) ); ?></p>
                <!-- Parte de plantilla: Tienda -->
                <?php get_template_part( 'template parts/header/boton-tienda' ); ?>
                <!-- Fin -->
            </div>
            <div class="wishlist-empty-message-2">
                <h2>Dale un corazón</h2>
                <p>Guarda todo lo que te gusta en un solo lugar</p>
                <ul>
                    <li>
                    Piénsalo antes de comprar.
                    </li>
                    <li>
                    Recibe notificaciones cuando un artículo vuelva estar disponible.
                    </li>
                </ul>
            </div>
        <?php endif; ?>
    </div>

    <?php if ( ! empty( $page_links ) ) : ?>
        <div class="wishlist-pagination">
            <?php echo wp_kses_post( $page_links ); ?>
        </div>
    <?php endif; ?>
</div>

<?php do_action( 'yith_wcwl_after_wishlist_form', $wishlist ); ?>