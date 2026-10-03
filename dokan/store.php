<?php
/**
 * The Template for displaying all single posts.
 *
 * @package dokan
 * @package dokan - 2014 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$store_user   = dokan()->vendor->get( get_query_var( 'author' ) );
$store_info   = $store_user->get_shop_info();
$map_location = $store_user->get_location();
$layout       = get_theme_mod( 'store_layout', ';eft' );

// Detectar si es la tienda SDexpress por slug
if ( $store_user ) {
    // Intentar obtener el slug de diferentes formas
    $store_slug_method = $store_user->get_shop_slug();
    $store_user_id = $store_user->get_id();
    $user_data = get_userdata( $store_user_id );
    $user_nicename = $user_data ? $user_data->user_nicename : '';
    $store_slug_meta = get_user_meta( $store_user_id, 'dokan_store_slug', true );
    
    // Usar el slug que no esté vacío, priorizando el user_nicename
    $store_slug = ! empty( $store_slug_method ) ? $store_slug_method : 
                  ( ! empty( $store_slug_meta ) ? $store_slug_meta : $user_nicename );
    
    if ( 'sdexpress' === $store_slug ) {
        // Cargar plantilla personalizada para SDexpress
        include locate_template( 'dokan/store-sdexpress.php' );
        return;
    }
}

get_header();?>

<div class="dokan-store-wrap ">

    <div id="dokan-primary" class="dokan-single-store">
        <div id="dokan-content" class="store-page-wrap woocommerce" role="main">

            <?php dokan_get_template_part( 'store-header' ); ?>

            <?php do_action( 'dokan_store_profile_frame_after', $store_user->data, $store_info ); ?>

            <?php
            // Separamos los productos de la tienda en:
            // 1) Destacados (featured)
            // 2) Todos los demás (excluyendo los destacados)
            $store_user_id = method_exists( $store_user, 'get_id' ) ? $store_user->get_id() : 0;

            // Si no se puede determinar el ID del vendedor, caemos al comportamiento original.
            if ( ! $store_user_id ) {
                if ( have_posts() ) {
                    // Limpieza: en este fallback usamos el comportamiento original de Dokan.

                    echo '<div class="seller-items">';
                    woocommerce_product_loop_start();
                    while ( have_posts() ) :
                        the_post();
                        wc_get_template_part( 'content', 'product' );
                    endwhile;
                    woocommerce_product_loop_end();
                    echo '</div>';
                    dokan_content_nav( 'nav-below' );
                } else {
                    echo '<p class="dokan-info">' . esc_html__( 'No products were found of this vendor!', 'dokan-lite' ) . '</p>';
                }
            } else {
                $featured_products = new WP_Query([
                    'post_type'      => 'product',
                    'post_status'    => 'publish',
                    'posts_per_page' => -1,
                    // Aseguramos que el query no pierda 'author' por filtros externos.
                    'ignore_sticky_posts' => 1,
                    'author'         => $store_user_id,
                    // En esta instalación WooCommerce guarda “Destacado” en la taxonomía product_visibility.
                    // Ejemplo detectado: term slug = featured (term_id = 11).
                    'tax_query'      => [
                        [
                            'taxonomy' => 'product_visibility',
                            'field'    => 'slug',
                            'terms'    => [ 'featured' ],
                        ],
                    ],
                    'no_found_rows'  => true,
                    'update_post_meta_cache' => false,
                    'update_post_term_cache' => false,
                ]);


                $featured_ids = array_map( 'intval', wp_list_pluck( $featured_products->posts, 'ID' ) );

                $paged = max( 1, absint( get_query_var( 'paged', 1 ) ) );
                if ( ! $paged ) {
                    $paged = 1;
                }

                $per_page = 20; // Ajusta cuántos productos “resto” por página

                $others_products = new WP_Query([
                    'post_type'      => 'product',
                    'post_status'    => 'publish',
                    'posts_per_page' => $per_page,
                    'paged'          => $paged,
                    'author'         => $store_user_id,
                    'post__not_in'   => $featured_ids,
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                    'no_found_rows'  => false,
                    'update_post_meta_cache' => false,
                    'update_post_term_cache' => false,
                ]);


                echo '<div class="seller-featured-items">';
                if ( $featured_products->have_posts() ) {
                    // Para depurar si el query no trae nada (descomenta si hace falta):
                    // echo '<!-- debug featured posts: '. esc_html( (string) $featured_products->found_posts ) .' -->';
                    echo '<h2 class="seller-items__title">' . esc_html__( 'Destacados de esta tienda', 'dokan-lite' ) . '</h2>';
                    woocommerce_product_loop_start();
                    while ( $featured_products->have_posts() ) :
                        $featured_products->the_post();
                        wc_get_template_part( 'content', 'product' );
                    endwhile;
                    woocommerce_product_loop_end();
                }
                echo '</div>';

                echo '<div class="seller-items">';
                if ( $others_products->have_posts() ) {
                    echo '<h2 class="seller-items__title seller-items__title--all">' . esc_html__( 'Productos de la tienda', 'dokan-lite' ) . '</h2>';
                    woocommerce_product_loop_start();
                    while ( $others_products->have_posts() ) :
                        $others_products->the_post();
                        wc_get_template_part( 'content', 'product' );
                    endwhile;
                    woocommerce_product_loop_end();


                } else {
                    // Si no hay “otros”, pero sí hay destacados, no mostramos mensaje (ya se verá la sección de destacados).
                    if ( ! $featured_products->have_posts() ) {
                        echo '<p class="dokan-info">' . esc_html__( 'No products were found of this vendor!', 'dokan-lite' ) . '</p>';
                    } else {
                        // Mantener coherencia visual: título “Productos de la tienda” solo aparece si hay productos.
                        // (Aquí no hacemos nada adicional)
                    }

                }
                echo '</div>';
                
                // Paginación solo para la sección “Productos de la tienda”
                echo '<div class="seller-items-pagination">';
                echo paginate_links([
                    'base'      => preg_replace( '/\?.*/', '', esc_url( get_pagenum_link( 1 ) ) ) . '%_%',
                    'format'    => 'page/%#%/',
                    'current'   => $paged,
                    'total'     => (int) $others_products->max_num_pages,
                    'type'      => 'plain',
                    'prev_text' => '&laquo;',
                    'next_text' => '&raquo;',
                ]);
                echo '</div>';

                wp_reset_postdata();
                wp_reset_query();
            }
            ?>
        </div>

    </div><!-- .dokan-single-store -->

    <?php if ( 'right' === $layout ) { ?>
        <?php
        dokan_get_template_part(
            'store', 'sidebar', [
                'store_user'   => $store_user,
                'store_info'   => $store_info,
                'map_location' => $map_location,
            ]
        );
        ?>
    <?php } ?>

</div><!-- .dokan-store-wrap -->

<?php do_action( 'woocommerce_after_main_content' ); ?>

<?php get_footer(); ?>
