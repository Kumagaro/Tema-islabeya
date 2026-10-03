<?php
/**
 * Single Product Image
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.0.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Obtener ID de la imagen principal
$post_thumbnail_id = $product->get_image_id();

// Obtener todas las imágenes de la galería
$attachment_ids = $product->get_gallery_image_ids();

// Encolar estilos y scripts SOLO en esta página
add_action( 'wp_enqueue_scripts', 'islabeya_product_gallery_assets' );
function islabeya_product_gallery_assets() {
    if ( is_product() ) {
        wp_enqueue_style( 'islabeya-product-gallery', get_template_directory_uri() . '/assets/css/product-gallery.css', array(), '1.0.0' );
        wp_enqueue_script( 'islabeya-product-gallery', get_template_directory_uri() . '/assets/js/product-gallery.js', array( 'jquery' ), '1.0.0', true );
        
        // Estilos nativos de WooCommerce para lightbox
        wp_enqueue_style( 'photoswipe', WC()->plugin_url() . '/assets/css/photoswipe/photoswipe.min.css', array(), WC_VERSION );
        wp_enqueue_style( 'photoswipe-default-skin', WC()->plugin_url() . '/assets/css/photoswipe/default-skin/default-skin.min.css', array(), WC_VERSION );
        
        // Scripts nativos de WooCommerce para lightbox
        wp_enqueue_script( 'photoswipe', WC()->plugin_url() . '/assets/js/photoswipe/photoswipe.min.js', array(), WC_VERSION, true );
        wp_enqueue_script( 'photoswipe-ui-default', WC()->plugin_url() . '/assets/js/photoswipe/photoswipe-ui-default.min.js', array( 'photoswipe' ), WC_VERSION, true );
    }
}
?>

<div class="woocommerce-product-gallery" data-columns="4">
    
    <!-- ========================================== -->
    <!-- 1. IMAGEN PRINCIPAL (con zoom/lightbox) -->
    <!-- ========================================== -->
    <div class="product-gallery__main-image">
        <?php if ( $post_thumbnail_id ) : ?>
            <?php
            $image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'woocommerce_single' );
            $full_image       = wp_get_attachment_image_src( $post_thumbnail_id, 'full' );
            $caption          = get_post_field( 'post_excerpt', $post_thumbnail_id );
            ?>
            <div class="main-image-wrapper" data-image-id="<?php echo esc_attr( $post_thumbnail_id ); ?>">
                <a href="<?php echo esc_url( $full_image[0] ); ?>" 
                   class="woocommerce-main-image zoom" 
                   data-fancybox="product-gallery"
                   data-caption="<?php echo esc_attr( $caption ); ?>">
                    <img src="<?php echo esc_url( $image_attributes[0] ); ?>" 
                         alt="<?php echo esc_attr( get_post_meta( $post_thumbnail_id, '_wp_attachment_image_alt', true ) ); ?>"
                         class="wp-post-image"
                         data-full-image="<?php echo esc_url( $full_image[0] ); ?>"
                         data-full-width="<?php echo esc_attr( $full_image[1] ); ?>"
                         data-full-height="<?php echo esc_attr( $full_image[2] ); ?>">
                </a>
            </div>
        <?php else : ?>
            <div class="main-image-wrapper">
                <img src="<?php echo esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ); ?>" 
                     alt="<?php esc_html_e( 'Product image placeholder', 'woocommerce' ); ?>">
            </div>
        <?php endif; ?>
    </div>

    <!-- ========================================== -->
    <!-- 2. MINIATURAS DE LA GALERÍA (horizontal con wrap) -->
    <!-- ========================================== -->
    <?php if ( ! empty( $attachment_ids ) || $post_thumbnail_id ) : ?>
        <div class="product-gallery__thumbnails-wrapper">
            <div class="product-gallery__thumbnails">
                <?php
                // Incluir la imagen principal como primera miniatura
                if ( $post_thumbnail_id ) :
                    $thumbnail_url = wp_get_attachment_image_src( $post_thumbnail_id, 'woocommerce_gallery_thumbnail' );
                    $full_image    = wp_get_attachment_image_src( $post_thumbnail_id, 'full' );
                    $caption       = get_post_field( 'post_excerpt', $post_thumbnail_id );
                    ?>
                    <div class="thumbnail-item active" data-image-id="<?php echo esc_attr( $post_thumbnail_id ); ?>">
                        <a href="<?php echo esc_url( $full_image[0] ); ?>" 
                           data-fancybox="product-gallery"
                           data-caption="<?php echo esc_attr( $caption ); ?>">
                            <img src="<?php echo esc_url( $thumbnail_url[0] ); ?>" 
                                 alt="<?php echo esc_attr( get_post_meta( $post_thumbnail_id, '_wp_attachment_image_alt', true ) ); ?>"
                                 data-full-image="<?php echo esc_url( $full_image[0] ); ?>"
                                 data-full-width="<?php echo esc_attr( $full_image[1] ); ?>"
                                 data-full-height="<?php echo esc_attr( $full_image[2] ); ?>">
                        </a>
                    </div>
                <?php endif; ?>
                
                <?php
                // Mostrar todas las imágenes de la galería
                foreach ( $attachment_ids as $attachment_id ) :
                    $thumbnail_url = wp_get_attachment_image_src( $attachment_id, 'woocommerce_gallery_thumbnail' );
                    $full_image    = wp_get_attachment_image_src( $attachment_id, 'full' );
                    $caption       = get_post_field( 'post_excerpt', $attachment_id );
                    ?>
                    <div class="thumbnail-item" data-image-id="<?php echo esc_attr( $attachment_id ); ?>">
                        <a href="<?php echo esc_url( $full_image[0] ); ?>" 
                           data-fancybox="product-gallery"
                           data-caption="<?php echo esc_attr( $caption ); ?>">
                            <img src="<?php echo esc_url( $thumbnail_url[0] ); ?>" 
                                 alt="<?php echo esc_attr( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ); ?>"
                                 data-full-image="<?php echo esc_url( $full_image[0] ); ?>"
                                 data-full-width="<?php echo esc_attr( $full_image[1] ); ?>"
                                 data-full-height="<?php echo esc_attr( $full_image[2] ); ?>">
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    
</div>

<!-- ========================================== -->
<!-- 3. TEMPLATE DE PHOTOSWIPE (lightbox nativo de WooCommerce) -->
<!-- ========================================== -->
<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="pswp__bg"></div>
    <div class="pswp__scroll-wrap">
        <div class="pswp__container">
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
        </div>
        <div class="pswp__ui pswp__ui--hidden">
            <div class="pswp__top-bar">
                <div class="pswp__counter"></div>
                <button class="pswp__button pswp__button--close" title="Cerrar (Esc)"></button>
                <button class="pswp__button pswp__button--share" title="Compartir"></button>
                <button class="pswp__button pswp__button--fs" title="Pantalla completa"></button>
                <button class="pswp__button pswp__button--zoom" title="Zoom"></button>
                <div class="pswp__preloader">
                    <div class="pswp__preloader__icn">
                        <div class="pswp__preloader__cut">
                            <div class="pswp__preloader__donut"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                <div class="pswp__share-tooltip"></div>
            </div>
            <button class="pswp__button pswp__button--arrow--left" title="Anterior"></button>
            <button class="pswp__button pswp__button--arrow--right" title="Siguiente"></button>
            <div class="pswp__caption">
                <div class="pswp__caption__center"></div>
            </div>
        </div>
    </div>
</div>