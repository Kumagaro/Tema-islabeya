<?php temaislabeya_wrapper_start(); ?>
    <section class="productos-despensa">
        <div class="seccion-productos-despensa">
            <div class="dots-container-despensa"></div>
            <?php
            $args = array(
                'limit'            => 10,
                'category'         => array( 'despensa' ),
                'exclude'          => array(250, 251, 252, 253, 242, 243, 244, 245, 234, 235, 236, 237),
                'return'           => 'objects',
                'visibility'       => 'visible',
            );
            
            $destacados = wc_get_products( $args );
            ?>
            <div class="carrusel-despensas">
                <div class="textos">
                    <h2>Ofertas en despensas</h2>
                    <?php if ( ! empty( $destacados ) ) : ?>
                        <a href="<?php echo esc_url( get_term_link( 'despensa', 'product_cat' ) ); ?>">ver todas las ofertas</a>   
                    <?php endif; ?>
                </div>
                <div class="carrusel-productos-despensas">
                    <?php
                    if ( ! empty( $destacados ) ) :
                        // Dividir los productos en grupos de 5
                        $grupos = array_chunk( $destacados, 5 );
                        
                        foreach ( $grupos as $grupo ) :
                            echo '<div class="grupo-despensas">';
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
            <span class="btn-left-despensas">
                <span class="icon-arrow-left"></span>
            </span>
            <span class="btn-right-despensas">
                <span class="icon-arrow-right"></span>
            </span>
            <div class="carrusel-despensas-movil">
                <div class="textos">
                    <h2>Ofertas en despensas</h2>
                    <?php if ( ! empty( $destacados ) ) : ?>
                        <a href="<?php echo esc_url( get_term_link( 'despensa', 'product_cat' ) ); ?>">ver todas las ofertas</a>   
                    <?php endif; ?>
                </div>
                <?php
                    if ( ! empty( $destacados ) ) :
                        woocommerce_product_loop_start();
                        
                        global $post; // ← Necesario para setup_postdata
                        
                        foreach ( $destacados as $producto ) :
                            // Configurar el $post global con el producto actual
                            $post = get_post( $producto->get_id() );
                            setup_postdata( $post ); // ← ESTO ES CLAVE
                            
                            // Ahora wc_get_template_part funcionará correctamente
                            wc_get_template_part( 'content', 'product' );
                            
                        endforeach;
                        
                        wp_reset_postdata(); // ← Restaurar el $post original
                        
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