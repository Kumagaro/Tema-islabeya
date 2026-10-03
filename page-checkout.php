<?php
/**
 * Template Name: Checkout Personalizado
 *
 * @package temaislabeya
 */

get_header('fullheight'); ?>

<div class="woocommerce-checkout-container">
    <?php
    // Verificar que WooCommerce está activo
    if ( class_exists( 'WooCommerce' ) ) {
        // Verificar si estamos en la página de checkout
        if ( is_checkout() ) {
            // Mostrar el checkout de WooCommerce
            echo do_shortcode( '[woocommerce_checkout]' );
        } else {
            // Si no es checkout, mostrar el contenido normal
            while ( have_posts() ) : the_post();
                the_content();
            endwhile;
        }
    } else {
        // Si WooCommerce no está activo, mostrar mensaje
        echo '<p>WooCommerce no está activo.</p>';
    }
    ?>
</div>

<?php get_footer('fullheight'); ?>
