<?php
/**
 * Template Name: Mis Productos Favoritos
 *
 * @package IslaBeya
 */

get_header(); // Incluye el encabezado de tu tema.
?>
<div class="main-wishlist">
    <div class="contenedor-wishlist">
        <div class="entry-header">
            <h1 class="entry-title">Favoritos</h1>
        </div>

        <div class="entry-content">
            <?php
            // Aquí es donde el shortcode del plugin hará su magia
            echo do_shortcode( '[yith_wcwl_wishlist]' );
            ?>
        </div>
    </div>
</div>


<?php
get_footer(); // Incluye el pie de página de tu tema.
?>