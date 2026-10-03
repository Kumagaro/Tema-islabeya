<?php
// ===================================================
// CHECKOUT: CONVERTIR CAMPOS DE PROVINCIA Y MUNICIPIO
// CON SELECCIÓN ESTILO MODAL (SEARCHABLE SELECT)
// VERSIÓN CON PRECARGA DE MUNICIPIOS (SIN AJAX)
// ===================================================

add_action('wp_footer', 'islabeya_checkout_shipping_fields_with_searchable_select', 20);
function islabeya_checkout_shipping_fields_with_searchable_select() {
    // Solo en checkout o páginas de funnel
    if ( ! function_exists('is_checkout') || ! is_checkout() ) {
        if ( function_exists('is_wffn_funnel_page') && ! is_wffn_funnel_page() ) {
            return;
        }
    }

    // Obtener todas las provincias (nivel 0)
    $provincias_terms = get_terms(array(
        'taxonomy' => 'provincia',
        'parent'   => 0,
        'hide_empty' => false,
        'orderby' => 'name',
        'order'   => 'ASC'
    ));
    $provincias = array();
    if ( ! empty($provincias_terms) && ! is_wp_error($provincias_terms) ) {
        foreach ($provincias_terms as $term) {
            $provincias[ $term->name ] = $term->name;
        }
    }

    // Obtener TODOS los municipios agrupados por provincia (nombre de provincia)
    $todos_municipios = array();
    foreach ($provincias as $provincia_nombre) {
        $term_prov = get_term_by('name', $provincia_nombre, 'provincia');
        if ($term_prov) {
            $children = get_terms(array(
                'taxonomy' => 'provincia',
                'parent'   => $term_prov->term_id,
                'hide_empty' => false,
                'orderby' => 'name',
                'order'   => 'ASC'
            ));
            if (!empty($children) && !is_wp_error($children)) {
                $municipios = array();
                foreach ($children as $child) {
                    $municipios[] = $child->name;
                }
                $todos_municipios[$provincia_nombre] = $municipios;
            } else {
                $todos_municipios[$provincia_nombre] = array();
            }
        }
    }

    $options_provincias = '<option value="">Selecciona una provincia</option>';
    foreach ($provincias as $nombre) {
        $options_provincias .= '<option value="' . esc_attr($nombre) . '">' . esc_html($nombre) . '</option>';
    }

    $json_municipios = json_encode($todos_municipios);
    ?>
    <script type="text/javascript">
    (function() {
        // Datos precargados de todos los municipios (sin necesidad de AJAX)
        var allMunicipios = <?php echo $json_municipios; ?>;

        function init() {
            if (typeof makeSearchableSelect === 'undefined') {
                setTimeout(init, 200);
                return;
            }

            if (document.getElementById('shipping_state_select')) return;

            // --- 1. Reemplazar shipping_state (input) por select de provincias ---
            var provinciaInput = document.getElementById('shipping_state');
            if (provinciaInput && provinciaInput.tagName === 'INPUT') {
                var provinciaSelect = document.createElement('select');
                provinciaSelect.id = 'shipping_state_select';
                provinciaSelect.name = provinciaInput.name;
                provinciaSelect.className = provinciaInput.className + ' js-searchable-select select-provincia';
                provinciaSelect.required = provinciaInput.required;
                provinciaSelect.innerHTML = '<?php echo $options_provincias; ?>';
                if (provinciaInput.value) provinciaSelect.value = provinciaInput.value;

                provinciaInput.parentNode.replaceChild(provinciaSelect, provinciaInput);
                makeSearchableSelect(provinciaSelect);
            }

            // --- 2. Reemplazar shipping_city (input) por select dependiente ---
            var municipioInput = document.getElementById('shipping_city');
            if (municipioInput && municipioInput.tagName === 'INPUT' && !document.getElementById('shipping_city_select')) {
                var municipioSelect = document.createElement('select');
                municipioSelect.id = 'shipping_city_select';
                municipioSelect.name = municipioInput.name;
                municipioSelect.className = municipioInput.className + ' js-searchable-select select-municipio';
                municipioSelect.required = municipioInput.required;
                municipioSelect.disabled = true;
                municipioSelect.innerHTML = '<option value="">Primero selecciona provincia</option>';

                municipioInput.parentNode.replaceChild(municipioSelect, municipioInput);
                makeSearchableSelect(municipioSelect);

                // Función para actualizar municipios localmente (sin AJAX)
                function cargarMunicipiosLocal(provincia) {
                    if (!provincia) {
                        municipioSelect.innerHTML = '<option value="">Primero selecciona provincia</option>';
                        municipioSelect.disabled = true;
                        if (municipioSelect.refreshSearchableSelect) municipioSelect.refreshSearchableSelect();
                        return;
                    }
                    var municipios = allMunicipios[provincia] || [];
                    var html = '<option value="">Selecciona municipio</option>';
                    for (var i = 0; i < municipios.length; i++) {
                        html += '<option value="' + municipios[i] + '">' + municipios[i] + '</option>';
                    }
                    municipioSelect.innerHTML = html;
                    municipioSelect.disabled = false;
                    if (municipioSelect.refreshSearchableSelect) municipioSelect.refreshSearchableSelect();
                }

                var provinciaSelectElem = document.getElementById('shipping_state_select');
                if (provinciaSelectElem) {
                    provinciaSelectElem.addEventListener('change', function() {
                        cargarMunicipiosLocal(this.value);
                    });
                    if (provinciaSelectElem.value) {
                        cargarMunicipiosLocal(provinciaSelectElem.value);
                    }
                }
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
    </script> 
    <?php
}

// ===================================================
// Validación y guardado de los campos modificados
// ===================================================
add_filter('woocommerce_checkout_posted_data', 'islabeya_ajustar_post_provincia_municipio');
function islabeya_ajustar_post_provincia_municipio($data) {
    if (isset($_POST['shipping_state_select'])) {
        $data['shipping_state'] = sanitize_text_field($_POST['shipping_state_select']);
    }
    if (isset($_POST['shipping_city_select'])) {
        $data['shipping_city'] = sanitize_text_field($_POST['shipping_city_select']);
    }
    return $data;
}

add_action('woocommerce_checkout_update_order_meta', 'islabeya_guardar_provincia_municipio', 10, 1);
function islabeya_guardar_provincia_municipio($order_id) {
    if (isset($_POST['shipping_state_select'])) {
        update_post_meta($order_id, '_shipping_state', sanitize_text_field($_POST['shipping_state_select']));
    }
    if (isset($_POST['shipping_city_select'])) {
        update_post_meta($order_id, '_shipping_city', sanitize_text_field($_POST['shipping_city_select']));
    }
}