<?php
// SOLUCIÓN DOKAN - Diseño de Badge de Advertencia (Mismo estilo que Envío/Recogida)
add_action('woocommerce_before_add_to_cart_quantity', 'ocultar_cantidad_para_productos_unicos_dokan');
function ocultar_cantidad_para_productos_unicos_dokan() {
    global $product;
    
    if (!$product->is_type('simple') && !$product->is_type('variable')) return;
    
    $product_id = $product->get_id();
    
    // Verificación de unidad única (Dokan y WooCommerce)
    $es_unidad_unica = false;
    $dokan_limit = get_post_meta($product_id, '_dokan_max_per_order', true);
    $dokan_single = get_post_meta($product_id, '_dokan_single_purchase', true);
    
    if ($dokan_limit == '1' || $dokan_single === 'yes' || $dokan_single === '1' || $product->is_sold_individually()) {
        $es_unidad_unica = true;
    }

    if ($es_unidad_unica) {
        ?>
        <style>
            /* Ocultar selectores de cantidad */
            .woocommerce div.product form.cart div.quantity,
            .variations_button .quantity,
            .quantity label {
                display: none !important;
            }

            /* Estilo del nuevo Badge de Advertencia (Igual a Recogida Local) */
            .badge-limite-compra {
                display: flex;
                align-items: center;
                border-radius: 5px;
                color: #B84A2C; /* Mismo color de texto que recogida */
                padding: 0px;
   				max-height: 20px;
                font-family: inherit;
            }

            @media (max-width: 768px) {
                .woocommerce div.product form.cart {
                    display: flex !important;
                    flex-direction: column !important;
                }
                .badge-limite-compra {
                    order: -1; /* Aparece arriba del botón en móvil */
                }
            }
        </style>
        
        <div class="badge-limite-compra">
            <p>
                1 unidad por pedido
            </p>
        </div>
        
        <script>
            jQuery(document).ready(function($) {
                function forzarCantidadUnica() {
                    $('input.qty').val(1).prop('readonly', true);
                }
                forzarCantidadUnica();
                $(document).on('found_variation', function() { setTimeout(forzarCantidadUnica, 100); });
            });
        </script>
        <?php
    }
}

// Las funciones de validación de carrito permanecen iguales para asegurar el límite...