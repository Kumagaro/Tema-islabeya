<?php temaislabeya_wrapper_start(); ?>
    <!-- Productos Destacados -->
    <section class="productos">
        <div class="seccion-productos">
            <div class="efecto-destacado">
                <div class="moto-destacada">
                    <h2>Oferta del Día</h2>
                    <?php
                    // Producto destacado: MOTO TORVAN TRN 250CC
                    $producto_destacado = wc_get_products( array( 
                        'name' => 'Moto Torvan Z-MAX 180cc',
                        'limit' => 1,
                        'visibility' => 'visible',
                    ) );
                    
                    if ( ! empty( $producto_destacado ) ) :
                        $producto_oferta = $producto_destacado[0];
                        woocommerce_product_loop_start();
                        
                        global $post;
                        $post = get_post( $producto_oferta->get_id() );
                        setup_postdata( $post );
                        wc_get_template_part( 'content', 'product' );
                        wp_reset_postdata();
                        
                        woocommerce_product_loop_end();
                    else :
                        echo '<p>No hay productos destacados en esta categoría.</p>';
                        $producto_oferta = null;
                    endif;
                    ?>
                </div>
            </div>
            <div class="dots-container-moto"></div>
            <div class="carrusel-motos">
                <div class="textos">
                    <h2>Ofertas en motos</h2>
                    <?php if ( ! empty( $todas_las_motos ) ) : ?>
                        <a href="<?php echo esc_url( get_term_link( 'motos', 'product_cat' ) ); ?>">ver todas las ofertas</a>  
                    <?php endif; ?>
                </div>
                <div class="carrusel-productos-motos">
                    <?php
                    // ==============================================
                    // 1. OBTENER TODAS LAS MOTOS (SIN LÍMITE)
                    // ==============================================
                    $oferta_id = $producto_oferta ? $producto_oferta->get_id() : 0;
                    
                    $args_motos = array(
                        'limit'            => -1, // Obtenemos todas para filtrar
                        'category'         => array( 'motos' ),
                        'return'           => 'objects',
                        'exclude'          => $oferta_id > 0 ? array( $oferta_id ) : array(),
                        'visibility'       => 'visible',
                    );
                    
                    $todas_las_motos = wc_get_products( $args_motos );
                    
                    if ( ! empty( $todas_las_motos ) ) :
                        // ==============================================
                        // 2. SEPARAR TORVAN DEL RESTO
                        // ==============================================
                        $motos_torvan = array();
                        $otras_motos = array();
                        
                        foreach ( $todas_las_motos as $moto ) {
                            $terms = wp_get_object_terms( $moto->get_id(), 'product_brand' );
                            $is_torvan = false;
                            
                            foreach ( $terms as $term ) {
                                if ( strtolower( trim( $term->name ) ) === 'torvan' ) {
                                    $is_torvan = true;
                                    break;
                                }
                            }
                            
                            if ( $is_torvan ) {
                                $motos_torvan[] = $moto;
                            } else {
                                $otras_motos[] = $moto;
                            }
                        }
                        
                        // ==============================================
                        // 3. COMBINAR Y LIMITAR A 20 PRODUCTOS
                        // ==============================================
                        // Primero unimos Torvan + resto
                        $motos_ordenadas = array_merge( $motos_torvan, $otras_motos );
                        
                        // Luego tomamos SOLO los primeros 20
                        $motos_limitadas = array_slice( $motos_ordenadas, 0, 20 );
                        
                        // ==============================================
                        // 4. MOSTRAR EN GRUPOS DE 4 (ESCRITORIO)
                        // ==============================================
                        $grupos = array_chunk( $motos_limitadas, 4 );
                        
                        foreach ( $grupos as $grupo ) :
                            echo '<div class="grupo-motos">';
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
            <span class="btn-left-motos">
                <span class="icon-arrow-left"></span>
            </span>
            <span class="btn-right-motos">
                <span class="icon-arrow-right"></span>
            </span>
            
            <!-- ============================================== -->
            <!-- CARRUSEL MÓVIL - SOLO MOTOS TORVAN (limitado a 20) -->
            <!-- ============================================== -->
            <div class="carrusel-motos-movil">
                <div class="textos">
                    <h2>Ofertas en motos</h2>
                    <?php if ( ! empty( $motos_torvan ) ) : ?>
                        <a href="<?php echo esc_url( get_term_link( 'motos', 'product_cat' ) ); ?>">ver todas las ofertas</a>   
                    <?php endif; ?>
                </div>
                <?php
                // ==============================================
                // USAR LAS MOTOS TORVAN YA FILTRADAS (limitado a 20)
                // ==============================================
                if ( ! empty( $motos_torvan ) ) :
                    // Tomar solo los primeros 20 productos Torvan
                    $motos_torvan_limitadas = array_slice( $motos_torvan, 0, 20 );
                    
                    woocommerce_product_loop_start();
                    
                    global $post;
                    foreach ( $motos_torvan_limitadas as $producto ) :
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