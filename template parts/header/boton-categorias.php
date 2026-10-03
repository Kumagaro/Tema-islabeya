<button class="btn font-medium boton-categorias">
    <span class="icon-box"></span>
    <span class="boton-text">Categorias</span>
    <span class="icon-arrow-down"></span>
</button>

<div id="nav-categorias" class="categorias-popover">
    <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="categoria-elemento">
        <h3>Todos los productos</h3>    
    </a>
    <?php
        $categorias = get_terms( array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => true,
            'parent'     => 0,
            'number'     => 20,
        ) );
        
        if ( ! empty( $categorias ) ) :
            foreach ( $categorias as $categoria ) :
                ?>
                <a href="<?php echo get_term_link( $categoria ); ?>" class="categoria-elemento">
                    <h3><?php echo esc_html( $categoria->name ); ?></h3>
                </a>
            <?php endforeach;
        endif;
    ?>
</div>