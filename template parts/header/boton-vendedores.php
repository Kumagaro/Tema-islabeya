<button class="btn font-medium boton-tiendas">
    <span class="icon-shop"></span>
    <span class="boton-text">Tiendas</span>
    <span class="icon-arrow-down"></span>
</button>

<div id="nav-tiendas" class="tiendas-popover">
    <?php
    global $wpdb;
    
    // Consulta directa: vendedores con al menos un producto publicado
    $tiendas_activas = $wpdb->get_results(
        "SELECT DISTINCT u.ID, u.display_name 
         FROM {$wpdb->users} u
         INNER JOIN {$wpdb->usermeta} um ON u.ID = um.user_id
         INNER JOIN {$wpdb->posts} p ON u.ID = p.post_author
         WHERE um.meta_key = 'wp_capabilities' 
         AND um.meta_value LIKE '%seller%'
         AND p.post_type = 'product'
         AND p.post_status = 'publish'
         GROUP BY u.ID
         ORDER BY u.display_name ASC
         LIMIT 50"
    );
    
    if ( ! empty( $tiendas_activas ) ) :
        foreach ( $tiendas_activas as $tienda ) :
            // Obtener la URL de la tienda
            $store_url = dokan_get_store_url( $tienda->ID );
            $store_name = ! empty( $tienda->display_name ) ? $tienda->display_name : get_user_meta( $tienda->ID, 'dokan_store_name', true );
            ?>
            <a href="<?php echo esc_url( $store_url ); ?>" class="tienda-elemento">
                <h3><?php echo esc_html( $store_name ); ?></h3>
            </a>
        <?php endforeach;
    else : ?>
        <div class="tienda-elemento">No hay tiendas con productos</div>
    <?php endif; ?>
</div>