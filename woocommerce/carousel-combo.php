<?php temaislabeya_wrapper_start(); ?>
    <section class="productos-combo">
        <div class="seccion-productos-combo">
            <div class="dots-container-combo"></div>
            <?php
            $args = array(
                'limit'            => 10,
                'category'         => array( 'combo' ),
                'exclude'          => array(), // Aquí puedes agregar IDs a excluir si es necesario
                'return'           => 'objects',
                'visibility'       => 'visible',
            );
            
            $destacados = wc_get_products( $args );
            ?>
            <div class="carrusel-combo">
                <div class="textos">
                    <h2>Ofertas en combos</h2>
                    <?php if ( ! empty( $destacados ) ) : ?>
                        <a href="<?php echo esc_url( get_term_link( 'combo', 'product_cat' ) ); ?>">ver todas las ofertas</a>   
                    <?php endif; ?>
                </div>
                <div class="carrusel-productos-combo">
                    <?php
                    if ( ! empty( $destacados ) ) :
                        // Dividir los productos en grupos de 5
                        $grupos = array_chunk( $destacados, 5 );
                        
                        foreach ( $grupos as $grupo ) :
                            echo '<div class="grupo-combo">';
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
            <span class="btn-left-combo">
                <span class="icon-arrow-left"></span>
            </span>
            <span class="btn-right-combo">
                <span class="icon-arrow-right"></span>
            </span>
            <div class="carrusel-combo-movil">
                <div class="textos">
                    <h2>Ofertas en combos</h2>
                    <?php if ( ! empty( $destacados ) ) : ?>
                        <a href="<?php echo esc_url( get_term_link( 'combo', 'product_cat' ) ); ?>">ver todas las ofertas</a>   
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
    </section>
<?php temaislabeya_wrapper_end(); ?>