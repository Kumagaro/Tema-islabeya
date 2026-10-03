<?php
/**
 * Agregar botón "Destacar" en la tabla de productos de Dokan (versión React)
 * Funciona con Dokan Lite 5.0.1 y la nueva interfaz DataViews
 */
add_action( 'dokan_dashboard_content_after', 'islabeya_add_featured_button_dokan_react' );

function islabeya_add_featured_button_dokan_react() {
    if ( ! dokan_is_seller_dashboard() ) {
        return;
    }
    
    $current_page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';
    if ( $current_page !== 'products' ) {
        return;
    }
    ?>
    <script>
    (function() {
        'use strict';
        
        // Función para obtener el ID del producto desde la URL de edición
        function getProductIdFromRow(row) {
            var editLink = row.querySelector('a[href*="/edit"]');
            if (editLink) {
                var match = editLink.href.match(/products\/(\d+)\/edit/);
                if (match) return match[1];
            }
            return null;
        }
        
        // Función para verificar si un producto está destacado
        function isProductFeatured(row) {
            // Buscar si la fila tiene alguna clase o indicador de destacado
            // Por ahora, haremos una petición AJAX para obtener el estado real
            return false; // Por defecto, no destacado
        }
        
        // Función para agregar el botón a cada fila
        function addStarButtonsToRows() {
            var rows = document.querySelectorAll('.dataviews-view-table tbody tr');
            
            rows.forEach(function(row) {
                // Evitar duplicados
                if (row.querySelector('.islabeya-star-btn')) return;
                
                var productId = getProductIdFromRow(row);
                if (!productId) return;
                
                // Buscar la celda de acciones (última celda)
                var actionsCell = row.querySelector('td:last-child');
                if (!actionsCell) return;
                
                // Crear la celda para la estrella
                var starCell = document.createElement('td');
                starCell.className = 'dataviews-view-table__cell-featured islabeya-star-cell';
                starCell.style.cssText = 'text-align: center; vertical-align: middle; width: 50px;';
                
                // Crear el botón estrella
                var starBtn = document.createElement('button');
                starBtn.innerHTML = '☆';
                starBtn.className = 'islabeya-star-btn';
                starBtn.setAttribute('data-product-id', productId);
                starBtn.setAttribute('data-featured', 'no');
                starBtn.style.cssText = 'background: none; border: none; cursor: pointer; font-size: 18px; color: #ccc; transition: 0.2s;';
                
                // Eventos hover
                starBtn.onmouseenter = function() { this.style.transform = 'scale(1.2)'; };
                starBtn.onmouseleave = function() { this.style.transform = 'scale(1)'; };
                
                // Evento click
                starBtn.onclick = function(e) {
                    e.preventDefault();
                    var btn = this;
                    var productId = btn.getAttribute('data-product-id');
                    var isFeatured = btn.getAttribute('data-featured') === 'yes';
                    var newStatus = isFeatured ? 'no' : 'yes';
                    
                    btn.style.opacity = '0.5';
                    btn.disabled = true;
                    
                    // Verificar si tenemos el nonce
                    var nonce = '';
                    var nonceElement = document.querySelector('#dokan-products-data-view input[name="_wpnonce"]');
                    if (nonceElement) {
                        nonce = nonceElement.value;
                    }
                    
                    fetch(dokan.ajaxurl || '<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({
                            action: 'islabeya_toggle_product_featured',
                            product_id: productId,
                            featured: newStatus,
                            nonce: nonce
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (newStatus === 'yes') {
                                btn.innerHTML = '★';
                                btn.style.color = '#ffc107';
                                btn.setAttribute('data-featured', 'yes');
                                btn.title = 'Quitar destacado';
                            } else {
                                btn.innerHTML = '☆';
                                btn.style.color = '#ccc';
                                btn.setAttribute('data-featured', 'no');
                                btn.title = 'Destacar';
                            }
                            // Mostrar notificación si existe
                            if (typeof dokan_sweetalert === 'function') {
                                dokan_sweetalert({ text: data.data.message, icon: 'success' });
                            }
                        } else {
                            alert(data.data.message || 'Error al actualizar');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error de conexión');
                    })
                    .finally(() => {
                        btn.style.opacity = '1';
                        btn.disabled = false;
                    });
                };
                
                starCell.appendChild(starBtn);
                
                // Insertar la celda antes de la celda de acciones
                actionsCell.parentNode.insertBefore(starCell, actionsCell);
            });
        }
        
        // Función para obtener el estado inicial de los productos destacados
        function loadFeaturedStatus() {
            var rows = document.querySelectorAll('.dataviews-view-table tbody tr');
            var productIds = [];
            
            rows.forEach(function(row) {
                var productId = getProductIdFromRow(row);
                if (productId) productIds.push(productId);
            });
            
            if (productIds.length === 0) return;
            
            fetch(dokan.ajaxurl || '<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'islabeya_get_products_featured_status',
                    product_ids: JSON.stringify(productIds),
                    nonce: '<?php echo wp_create_nonce('islabeya_featured_status'); ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.statuses) {
                    rows.forEach(function(row) {
                        var productId = getProductIdFromRow(row);
                        if (productId && data.data.statuses[productId]) {
                            var starBtn = row.querySelector('.islabeya-star-btn');
                            if (starBtn && data.data.statuses[productId] === 'yes') {
                                starBtn.innerHTML = '★';
                                starBtn.style.color = '#ffc107';
                                starBtn.setAttribute('data-featured', 'yes');
                                starBtn.title = 'Quitar destacado';
                            }
                        }
                    });
                }
            })
            .catch(error => console.error('Error loading featured status:', error));
        }
        
        // Inicializar
        function init() {
            addStarButtonsToRows();
            loadFeaturedStatus();
        }
        
        // Observar cambios en la tabla (paginación, filtros)
        var observer = new MutationObserver(function() {
            addStarButtonsToRows();
            loadFeaturedStatus();
        });
        
        var tableContainer = document.querySelector('.dataviews-layout__container');
        if (tableContainer) {
            observer.observe(tableContainer, { childList: true, subtree: true });
        }
        
        // Ejecutar al cargar
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
    </script>
    <style>
        .islabeya-star-btn {
            background: none !important;
            border: none !important;
            cursor: pointer;
            font-size: 18px;
            transition: 0.2s;
        }
        .islabeya-star-btn:disabled {
            opacity: 0.5;
            cursor: wait;
        }
        .islabeya-star-cell {
            width: 50px;
            text-align: center;
        }
    </style>
    <?php
}

/**
 * AJAX Handler para cambiar el estado destacado del producto
 */
add_action( 'wp_ajax_islabeya_toggle_product_featured', 'islabeya_ajax_toggle_product_featured' );

function islabeya_ajax_toggle_product_featured() {
    // Verificar que el usuario es vendedor
    if ( ! is_user_logged_in() || ! dokan_is_user_seller( get_current_user_id() ) ) {
        wp_send_json_error( array( 'message' => 'No tienes permiso para realizar esta acción' ) );
    }
    
    $product_id = intval( $_POST['product_id'] );
    $featured = sanitize_text_field( $_POST['featured'] );
    
    // Verificar que el producto existe y pertenece al vendedor
    $product = wc_get_product( $product_id );
    if ( ! $product || $product->get_author_id() != get_current_user_id() ) {
        wp_send_json_error( array( 'message' => 'Este producto no te pertenece' ) );
    }
    
    $new_featured = ( $featured === 'yes' );
    $product->set_featured( $new_featured );
    $product->save();
    
    wp_send_json_success( array(
        'message' => $new_featured ? 'Producto destacado' : 'Producto ya no está destacado',
        'featured' => $new_featured ? 'yes' : 'no'
    ) );
}

/**
 * AJAX Handler para obtener el estado destacado de múltiples productos
 */
add_action( 'wp_ajax_islabeya_get_products_featured_status', 'islabeya_ajax_get_products_featured_status' );

function islabeya_ajax_get_products_featured_status() {
    // Verificar nonce
    if ( ! wp_verify_nonce( $_POST['nonce'], 'islabeya_featured_status' ) ) {
        wp_send_json_error( array( 'message' => 'Nonce inválido' ) );
    }
    
    $product_ids = json_decode( stripslashes( $_POST['product_ids'] ), true );
    if ( ! is_array( $product_ids ) ) {
        wp_send_json_error( array( 'message' => 'IDs inválidos' ) );
    }
    
    $statuses = array();
    foreach ( $product_ids as $product_id ) {
        $product = wc_get_product( $product_id );
        if ( $product && $product->get_author_id() == get_current_user_id() ) {
            $statuses[ $product_id ] = $product->get_featured() ? 'yes' : 'no';
        }
    }
    
    wp_send_json_success( array( 'statuses' => $statuses ) );
}