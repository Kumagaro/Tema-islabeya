<?php
// ===================================================
// 1. AJAX PARA OBTENER CÓDIGO POSTAL DESDE MUNICIPIO
// ===================================================
add_action('wp_ajax_obtener_cp_municipio', 'islabeya_obtener_cp_municipio_ajax');
add_action('wp_ajax_nopriv_obtener_cp_municipio', 'islabeya_obtener_cp_municipio_ajax');
function islabeya_obtener_cp_municipio_ajax() {
    $municipio = sanitize_text_field($_POST['municipio']);
    $cp = '';
    
    if (!empty($municipio)) {
        // Buscar el término en la taxonomía 'provincia' (donde están los municipios)
        $term = get_term_by('name', $municipio, 'provincia');
        if ($term && !empty($term->description)) {
            // Buscar 5 dígitos en la descripción (formato: 12345)
            if (preg_match('/\b(\d{5})\b/', $term->description, $match)) {
                $cp = $match[1];
            }
        }
    }
    
    wp_send_json(array('cp' => $cp));
}

// ===================================================
// 2. JAVASCRIPT PARA AUTOCOMPLETAR CP EN CHECKOUT
// ===================================================
add_action('wp_footer', 'islabeya_autocompletar_cp_js');
function islabeya_autocompletar_cp_js() {
    // Solo cargar en páginas de checkout (incluyendo FunnelKit)
    if ( ! function_exists('is_checkout') || ! is_checkout() ) {
        // También permitir en páginas de FunnelKit (si tienen clase .wfacp-form)
        if ( ! function_exists('is_wffn_funnel_page') || ! is_wffn_funnel_page() ) {
            return;
        }
    }
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // Función para autocompletar el código postal
        function autocompletarCP(municipio, tipo) {
            if (!municipio) return;
            
            var $campoCP = $('#' + tipo + '_postcode');
            if (!$campoCP.length) return;
            
            // Mostrar estado de carga
            $campoCP.val('Buscando...').prop('readonly', true);
            
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'obtener_cp_municipio',
                    municipio: municipio
                },
                success: function(response) {
                    if (response.cp) {
                        $campoCP.val(response.cp);
                        // Mantener editable después de 2 segundos
                        setTimeout(function() {
                            $campoCP.prop('readonly', false);
                        }, 2000);
                    } else {
                        $campoCP.val('').prop('readonly', false);
                    }
                },
                error: function() {
                    $campoCP.val('').prop('readonly', false);
                }
            });
        }
        
        // Escuchar cambios en el campo de municipio de facturación (input text)
        $(document).on('change', '#billing_city', function() {
            var municipio = $(this).val();
            if (municipio && municipio.trim() !== '') {
                autocompletarCP(municipio, 'billing');
            }
        });
        
        // Escuchar cambios en el campo de municipio de envío (input text)
        $(document).on('change', '#shipping_city', function() {
            var municipio = $(this).val();
            if (municipio && municipio.trim() !== '') {
                autocompletarCP(municipio, 'shipping');
            }
        });
        
        // También manejar si hay selects con id #billing_city_select o #shipping_city_select (para futuras mejoras)
        $(document).on('change', '#billing_city_select, #shipping_city_select', function() {
            var municipio = $(this).val();
            var tipo = $(this).attr('id') === 'billing_city_select' ? 'billing' : 'shipping';
            if (municipio) {
                autocompletarCP(municipio, tipo);
            }
        });
        
        // Autocompletar si ya hay municipio seleccionado al cargar la página
        setTimeout(function() {
            var billingMunicipio = $('#billing_city').val();
            if (billingMunicipio && billingMunicipio.trim() !== '') {
                autocompletarCP(billingMunicipio, 'billing');
            }
            
            var shippingMunicipio = $('#shipping_city').val();
            if (shippingMunicipio && shippingMunicipio.trim() !== '') {
                autocompletarCP(shippingMunicipio, 'shipping');
            }
        }, 1500);
    });
    </script>
    
    <style>
    /* Estilo para campo de código postal mientras se autocompleta */
    input[name="billing_postcode"]:read-only,
    input[name="shipping_postcode"]:read-only {
        background-color: #f0f9f2;
        border-color: #15AD3C;
        cursor: wait;
    }
    </style>
    <?php
}