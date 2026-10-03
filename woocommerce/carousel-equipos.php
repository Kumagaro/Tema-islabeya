<?php temaislabeya_wrapper_start(); ?>
    <section class="productos-equipos">
        <div class="seccion-productos-equipos">
            <div class="dots-container-equipos"></div>
            <?php
            $args = array(
                'limit'            => 10,
                'return'           => 'objects',
                'visibility'       => 'visible',
                'tax_query'        => array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => 'product_cat',
                        'field'    => 'slug',
                        'terms'    => 'electrodomesticos',
                        'operator' => 'IN',
                    ),
                    array(
                        'taxonomy' => 'product_cat',
                        'field'    => 'slug',
                        'terms'    => 'contenedor',
                        'operator' => 'NOT IN',
                    ),
                ),
                'exclude'          => array(296, 290, 291, 288, 289, 280, 281, 282, 277, 278, 279),
            );
            
            $destacados = wc_get_products( $args );
            ?>
            <div class="carrusel-equipos">
                <div class="textos">
                    <h2>Ofertas en equipos</h2>
                    <?php if ( ! empty( $destacados ) ) : ?>
                        <a href="<?php echo esc_url( get_term_link( 'electrodomesticos', 'product_cat' ) ); ?>">ver todas las ofertas</a>   
                    <?php endif; ?>
                </div>
                <div class="carrusel-productos-equipos">
                    <?php
                    if ( ! empty( $destacados ) ) :
                        // Dividir los productos en grupos de 5
                        $grupos = array_chunk( $destacados, 5 );
                        
                        foreach ( $grupos as $grupo ) :
                            echo '<div class="grupo-equipos">';
                            woocommerce_product_loop_start();
                            
                            global $post;
                            foreach ( $grupo as $producto ) :
                                $post = get_post( $producto->get_id() );
                                setup_postdata( $post );
                                wc_get_template_part( 'content', 'product' );
                            endforeach;
                            
                            woocommerce_product_loop_end();
                            echo '</div>';
                        endforeach;
                        
                        wp_reset_postdata();
                    else :
                        echo '<p>No hay productos disponibles. ¡Volverán pronto!</p>';
                    endif;
                    ?>
                </div>
            </div>
            <span class="btn-left-equipos">
                <span class="icon-arrow-left"></span>
            </span>
            <span class="btn-right-equipos">
                <span class="icon-arrow-right"></span>
            </span>
            <div class="carrusel-equipos-movil">
                <div class="textos">
                    <h2>Ofertas en equipos</h2>
                    <?php if ( ! empty( $destacados ) ) : ?>
                        <a href="<?php echo esc_url( get_term_link( 'electrodomesticos', 'product_cat' ) ); ?>">ver todas las ofertas</a>   
                    <?php endif; ?>
                </div>
                <?php
                    if ( ! empty( $destacados ) ) :
                        woocommerce_product_loop_start();
                        
                        global $post;
                        
                        foreach ( $destacados as $producto ) :
                            $post = get_post( $producto->get_id() );
                            setup_postdata( $post );
                            
                            wc_get_template_part( 'content', 'product' );
                            
                        endforeach;
                        
                        wp_reset_postdata();
                        
                        woocommerce_product_loop_end();
                    else :
                        echo '<p>No hay productos disponibles. ¡Volverán pronto!</p>';
                endif;
                ?>
                </div>
            </div>
        </div>
    </section>
<?php temaislabeya_wrapper_end(); ?>