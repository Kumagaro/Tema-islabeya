<?php
/**
 * BOTÓN COMPRAR AHORA MEJORADO PARA FUNNELKIT - SIMPLES Y VARIABLES
 * CON CANTIDAD SELECCIONADA
 * Versión adaptada para tema clásico IslaBeya
 */

// Agregar botón "Comprar Ahora" en productos individuales
add_action('woocommerce_after_add_to_cart_button', 'islabeya_agregar_boton_comprar_ahora_funnelkit');
function islabeya_agregar_boton_comprar_ahora_funnelkit() {
    global $product;
    
    // Solo para productos simples y variables
    if (!$product->is_type('simple') && !$product->is_type('variable')) return;
    
    $product_id = $product->get_id();
    
    // URL directa al checkout
    $checkout_url = wc_get_checkout_url();
    
    // Para productos variables, necesitamos un data attribute especial
    $is_variable = $product->is_type('variable');
    $data_attr = $is_variable ? 'data-is-variable="true"' : '';
    
    echo '
    <div class="comprar-ahora-container" style="display: flex; align-items: center; width: 100%;">
        <button 
            type="button" 
            class="button comprar-ahora-btn-fk"
            data-product-id="' . esc_attr($product_id) . '"
            data-redirect-url="' . esc_url($checkout_url) . '"
            ' . $data_attr . '
            style="
                width: auto;
                padding: 10px 5px;
                font-size: 14px;
                font-weight: 600 !important;
                border-radius: 4px;
                overflow: hidden;
                z-index: 1;
                box-sizing: border-box;
            "
        >
            <span class="btn-content" style="display: flex; align-items: center; justify-content: center !important;">
                <span class="text">COMPRAR AHORA</span>
            </span>
            <span class="btn-loadre-comprar-ahora" style="display: none; align-items: center; justify-content: center;">
                <span>Procesando</span>
            </span>
        </button>
    </div>';
}

// AJAX: Añadir al carrito y redirigir
add_action('wp_ajax_islabeya_comprar_ahora_funnelkit', 'islabeya_comprar_ahora_funnelkit_handler');
add_action('wp_ajax_nopriv_islabeya_comprar_ahora_funnelkit', 'islabeya_comprar_ahora_funnelkit_handler');
function islabeya_comprar_ahora_funnelkit_handler() {
    // Verificar nonce para seguridad
    check_ajax_referer('islabeya_comprar_ahora_nonce', 'security');
    
    $product_id = intval($_POST['product_id']);
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $redirect_url = isset($_POST['redirect_url']) ? esc_url_raw($_POST['redirect_url']) : wc_get_checkout_url();

    // Validación extra: contenedores
    if ( $product_id && has_term( 'contenedor', 'product_cat', $product_id ) ) {
        $tipo_cliente = isset($_POST['islabeya_tipo_cliente']) ? sanitize_text_field($_POST['islabeya_tipo_cliente']) : '';
        $impocaribe   = isset($_POST['islabeya_impocaribe']) ? sanitize_text_field($_POST['islabeya_impocaribe']) : '';

        if ( empty($tipo_cliente) || empty($impocaribe) ) {
            wp_send_json_error(array(
                'message' => '⚠️ Debes aceptar TODOS los requisitos obligatorios: Ser PYME/TCP y estar inscrito en Impocaribe.'
            ));
        }
    }

    // Variables para productos variables
    $variation_id = isset($_POST['variation_id']) ? intval($_POST['variation_id']) : 0;
    $variation_data = isset($_POST['variation_data']) ? (array) $_POST['variation_data'] : array();
    
    // Validar cantidad mínima
    if ($quantity < 1) {
        $quantity = 1;
    }
    
    // Limpiar carrito actual (para compra directa)
    if (isset($_POST['clear_cart']) && $_POST['clear_cart'] === 'true') {
        WC()->cart->empty_cart();
    }

    
    // Añadir producto al carrito
    if ($variation_id > 0) {
        // Para productos variables
        $added = WC()->cart->add_to_cart(
            $product_id,
            $quantity,
            $variation_id,
            $variation_data
        );
    } else {
        // Para productos simples
        $added = WC()->cart->add_to_cart($product_id, $quantity);
    }
    
    if ($added) {
        wp_send_json_success(array(
            'redirect' => $redirect_url,
            'message' => 'Producto añadido al carrito',
            'cart_count' => WC()->cart->get_cart_contents_count(),
            'quantity_added' => $quantity
        ));
    } else {
        wp_send_json_error(array(
            'message' => 'Error al añadir el producto al carrito. Asegúrate de seleccionar todas las opciones requeridas.'
        ));
    }
    
    wp_die();
}

// CSS y JavaScript mejorados para simples y variables
add_action('wp_footer', 'islabeya_comprar_ahora_funnelkit_scripts_styles');
function islabeya_comprar_ahora_funnelkit_scripts_styles() {
    // Solo en página de producto
    if (!is_product()) return;
    
    global $product;
    $is_variable = $product && $product->is_type('variable');
    ?>
    <style>
    /* ESTILOS DEL BOTÓN - IslaBeya */
    .comprar-ahora-btn-fk {
        background: white !important;
        color: black !important;
        border: 2px solid #15AD3C !important;
        position: relative !important;
        transition: all 0.3s ease !important;
    }
    
    .comprar-ahora-btn-fk:hover {
        background: #15AD3C !important;
        color: white !important;
    }
    
    .comprar-ahora-btn-fk:active {
        transform: translateY(0) !important;
    }
    
    .comprar-ahora-btn-fk.disabled,
    .comprar-ahora-btn-fk:disabled {
        opacity: 0.5 !important;
        cursor: not-allowed !important;
        background: #f5f5f5 !important;
        border-color: #ccc !important;
        color: #999 !important;
    }
    
    .comprar-ahora-btn-fk.disabled:hover,
    .comprar-ahora-btn-fk:disabled:hover {
        background: #f5f5f5 !important;
        color: #999 !important;
        transform: none !important;
        box-shadow: none !important;
    }
    
    .comprar-ahora-btn-fk.loading .btn-content {
        display: none !important;
    }
    
    .comprar-ahora-btn-fk.loading .btn-loadre-comprar-ahora {
        display: flex !important;
        align-items: center;
        justify-content: center;
    }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        // Función para obtener la cantidad seleccionada
        function islabeyaObtenerCantidad() {
            var cantidad = 1;
            
            // Buscar input de cantidad (selector unificado de IslaBeya)
            var $inputCantidad = $('.quantity input.qty, .quantity input[type="number"]').first();
            
            if ($inputCantidad.length) {
                var valor = parseInt($inputCantidad.val());
                if (!isNaN(valor) && valor > 0) {
                    cantidad = valor;
                }
                
                var max = parseInt($inputCantidad.attr('max'));
                var min = parseInt($inputCantidad.attr('min'));
                
                if (!isNaN(max) && cantidad > max) cantidad = max;
                if (!isNaN(min) && cantidad < min) cantidad = min;
            }
            
            return cantidad;
        }
        
        // Función para obtener datos de variación seleccionada
        function islabeyaObtenerDatosVariacion() {
            var variationData = {};
            var variationId = 0;
            
            if ($('.variations_form').length) {
                variationId = $('input[name="variation_id"]').val();
                
                $('.variations select').each(function() {
                    var attributeName = $(this).data('attribute_name') || $(this).attr('name');
                    var attributeValue = $(this).val();
                    if (attributeValue) {
                        variationData[attributeName] = attributeValue;
                    }
                });
            }
            
            return { variationId: variationId, variationData: variationData };
        }
        
        // Función para verificar si se puede comprar (lógica de producto)
        function islabeyaPuedeComprarAhora() {
            if (!$('.variations_form').length) {
                return islabeyaObtenerCantidad() > 0;
            }
            
            var variacion = islabeyaObtenerDatosVariacion();
            return variacion.variationId > 0 && islabeyaObtenerCantidad() > 0;
        }

        // Función para verificar requisitos contenedor (2 checkboxes)
        function islabeyaContenedorRequisitosOk() {
            // Si NO existe la UI de contenedor, no aplica (mantener comportamiento actual)
            if (!$('.islabeya-contenedor-validacion').length) {
                return true;
            }

            var tipoOk = $('.islabeya-requisito-checkbox[data-requisito="tipo_cliente"]').is(':checked');
            var impOk  = $('.islabeya-requisito-checkbox[data-requisito="impocaribe"]').is(':checked');

            // Exigir ambas condiciones
            return tipoOk && impOk;
        }

        // Actualizar estado del botón
        function islabeyaActualizarEstadoBoton() {
            var $button = $('.comprar-ahora-btn-fk');
            var puedeComprar = islabeyaPuedeComprarAhora();
            var requisitosOk = islabeyaContenedorRequisitosOk();
            var habilitar = puedeComprar && requisitosOk;

            if (habilitar) {
                $button.prop('disabled', false).removeClass('disabled');
            } else {
                $button.prop('disabled', true).addClass('disabled');
            }
        }
        
        // Inicializar
        islabeyaActualizarEstadoBoton();
        
        // Eventos para cantidad
        $('.quantity input.qty, .quantity input[type="number"]').on('change keyup', function() {
            setTimeout(islabeyaActualizarEstadoBoton, 50);
        });
        
        // Eventos para variaciones
        $(document).on('change', '.variations select', function() {
            setTimeout(islabeyaActualizarEstadoBoton, 100);
        });
        
        $(document).on('found_variation reset_image', function() {
            setTimeout(islabeyaActualizarEstadoBoton, 100);
        });
        
        // Manejar clic en botón "Comprar Ahora"
        $(document).on('click', '.comprar-ahora-btn-fk', function(e) {

            e.preventDefault();
            
            var $button = $(this);
            
            if ($button.prop('disabled') || $button.hasClass('disabled')) {
                alert('Por favor, selecciona todas las opciones del producto antes de comprar.');
                return false;
            }
            
            var productId = $button.data('product-id');
            var redirectUrl = $button.data('redirect-url');
            var cantidad = islabeyaObtenerCantidad();
            var variacion = islabeyaObtenerDatosVariacion();
            
            $button.addClass('loading').prop('disabled', true);
            $button.find('.btn-content').hide();
            $button.find('.btn-loadre-comprar-ahora').show();
            
            // Validación UX extra para contenedor (si existe el bloque de checkboxes)
            // Nota: si el botón está deshabilitado por contenedor, NO llegamos aquí.
            var requisitosOk = islabeyaContenedorRequisitosOk();
            if (!requisitosOk) {
                alert('⚠️ Debes aceptar TODOS los requisitos para poder comprar este contenedor.');
                $button.removeClass('loading').prop('disabled', true);
                $button.find('.btn-content').show();
                $button.find('.btn-loadre-comprar-ahora').hide();
                return false;
            }


            // Pasar flags de validación contenedor (si aplica)
            var tipo_cliente_val = $('input[name="islabeya_tipo_cliente"]').val();
            var impocaribe_val = $('input[name="islabeya_impocaribe"]').val();

            // Fallback si aún no existen hidden inputs
            if (typeof tipo_cliente_val === 'undefined' && $('.islabeya-requisito-checkbox[data-requisito="tipo_cliente"]').length) {
                tipo_cliente_val = $('.islabeya-requisito-checkbox[data-requisito="tipo_cliente"]').is(':checked') ? 'pyme_tcp' : '';
            }
            if (typeof impocaribe_val === 'undefined' && $('.islabeya-requisito-checkbox[data-requisito="impocaribe"]').length) {
                impocaribe_val = $('.islabeya-requisito-checkbox[data-requisito="impocaribe"]').is(':checked') ? 'inscrito' : '';
            }

            $.ajax({


                url: '<?php echo admin_url('admin-ajax.php'); ?>',

                type: 'POST',
                data: {
                    action: 'islabeya_comprar_ahora_funnelkit',
                    product_id: productId,
                    quantity: cantidad,
                    redirect_url: redirectUrl,
                    variation_id: variacion.variationId,
                    variation_data: variacion.variationData,
                    clear_cart: 'true',
                    islabeya_tipo_cliente: tipo_cliente_val,
                    islabeya_impocaribe: impocaribe_val,
                    security: '<?php echo wp_create_nonce('islabeya_comprar_ahora_nonce'); ?>'
                },

                success: function(response) {
                    if (response.success) {
                        window.location.href = response.data.redirect;
                    } else {
                        alert(response.data.message || 'Error en la compra');
                        $button.removeClass('loading').prop('disabled', false);
                        $button.find('.btn-content').show();
                        $button.find('.btn-loadre-comprar-ahora').hide();
                        islabeyaActualizarEstadoBoton();
                    }
                },
                error: function() {
                    alert('Error de conexión. Intenta de nuevo.');
                    $button.removeClass('loading').prop('disabled', false);
                    $button.find('.btn-content').show();
                    $button.find('.btn-loadre-comprar-ahora').hide();
                    islabeyaActualizarEstadoBoton();
                }
            });
        });
    });
    </script>
    <?php
}