<?php
// ===================================================
// AUTO-RELLENAR UBICACIÓN EN CHECKOUT (ENVÍO)
// Versión robusta con espera a que los selects existan
// ===================================================

add_action('wp_footer', 'islabeya_autofill_shipping_location', 30);
function islabeya_autofill_shipping_location() {
    if ( ! function_exists('is_checkout') || ! is_checkout() ) {
        if ( function_exists('is_wffn_funnel_page') && ! is_wffn_funnel_page() ) {
            return;
        }
    }

    // Iniciar sesión
    if ( ! session_id() && ! headers_sent() ) {
        @session_start();
    }

    // Obtener nombres de ubicación desde la sesión (guardados por el selector)
    $province_name = $_SESSION['prov_name'] ?? '';
    $municipality_name = $_SESSION['muni_name'] ?? '';

    if ( empty($province_name) || empty($municipality_name) ) {
        // No hay ubicación seleccionada
        return;
    }

    ?>
    <script type="text/javascript">
    (function() {
        // Función para esperar a que un elemento aparezca en el DOM
        function waitForElement(selector, callback, maxWait = 5000, interval = 200) {
            var startTime = Date.now();
            var timer = setInterval(function() {
                var element = document.querySelector(selector);
                if (element) {
                    clearInterval(timer);
                    callback(element);
                } else if (Date.now() - startTime >= maxWait) {
                    clearInterval(timer);
                    console.warn('Elemento no encontrado:', selector);
                }
            }, interval);
        }

        // Función para establecer provincia
        function setProvince(provinceName) {
            var provinciaSelect = document.getElementById('shipping_state_select');
            if (!provinciaSelect) return false;
            if (provinciaSelect.value !== provinceName) {
                provinciaSelect.value = provinceName;
                // Disparar evento change para que se carguen los municipios
                provinciaSelect.dispatchEvent(new Event('change', { bubbles: true }));
                console.log('✅ Provincia establecida:', provinceName);
                return true;
            }
            return false;
        }

        // Función para establecer municipio (después de que las opciones estén cargadas)
        function setMunicipality(municipalityName) {
            var municipioSelect = document.getElementById('shipping_city_select');
            if (!municipioSelect) return false;
            // Esperar a que haya opciones (más de una)
            var checkInterval = setInterval(function() {
                if (municipioSelect.options.length > 1) {
                    clearInterval(checkInterval);
                    if (municipioSelect.value !== municipalityName) {
                        municipioSelect.value = municipalityName;
                        // Disparar evento change
                        municipioSelect.dispatchEvent(new Event('change', { bubbles: true }));
                        // Refrescar el widget searchable si existe
                        if (municipioSelect.refreshSearchableSelect) {
                            municipioSelect.refreshSearchableSelect();
                        }
                        console.log('✅ Municipio establecido:', municipalityName);
                    }
                }
            }, 200);
            // Limitar tiempo de espera a 5 segundos
            setTimeout(function() { clearInterval(checkInterval); }, 5000);
            return true;
        }

        // Esperar a que exista el select de provincia
        waitForElement('#shipping_state_select', function() {
            setProvince('<?php echo esc_js($province_name); ?>');
            // Luego de setear provincia, esperamos un poco para que se carguen los municipios y luego seteamos municipio
            setTimeout(function() {
                setMunicipality('<?php echo esc_js($municipality_name); ?>');
            }, 1000);
        });
    })();
    </script>
    <?php
}