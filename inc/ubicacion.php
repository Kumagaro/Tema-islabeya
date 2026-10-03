<?php 
/**
 * Selector de Ubicación - Versión Integrada 1.6
 * Diseño original + Taxonomía 'provincia' + Selectores con búsqueda (archivos externos)
 */


if (!defined('ABSPATH')) exit;

define('UBICACION_CACHE_TIME', DAY_IN_SECONDS);
define('UBICACION_ASSETS_URL', get_template_directory_uri() . '/assets'); // Ajusta si usas plugin

/**
 * Inicializar sesión
 */
function ubicacion_init_session() {
    if (!session_id() && !headers_sent()) {
        @session_start();
    }
}
add_action('init', 'ubicacion_init_session', 1);

/**
 * Obtener provincias (Taxonomía: provincia)
 */
function ubicacion_get_provinces() {
    global $wpdb;
    $cache_key = 'ubicacion_provinces_sql';
    $provinces = get_transient($cache_key);
    if (false === $provinces) {
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT t.term_id, t.name, tt.parent 
             FROM {$wpdb->terms} t 
             INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id 
             WHERE tt.taxonomy = %s AND tt.parent = %d 
             ORDER BY t.name ASC",
            'provincia', 0
        ));
        if (!empty($results)) {
            $provinces = array_map(function($row) {
                $term = new stdClass();
                $term->term_id = $row->term_id;
                $term->name = $row->name;
                $term->parent = $row->parent;
                return $term;
            }, $results);
            set_transient($cache_key, $provinces, UBICACION_CACHE_TIME);
        } else {
            $provinces = array();
        }
    }
    return $provinces;
}

/**
 * Obtener todos los municipios agrupados
 */
function ubicacion_get_all_municipalities_grouped() {
    global $wpdb;
    $cache_key = 'ubicacion_all_muni_sql';
    $grouped = get_transient($cache_key);
    if (false === $grouped) {
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT t.term_id, t.name, tt.parent 
             FROM {$wpdb->terms} t 
             INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id 
             WHERE tt.taxonomy = %s AND tt.parent != %d 
             ORDER BY tt.parent ASC, t.name ASC",
            'provincia', 0
        ));
        if (!empty($results)) {
            $grouped = array();
            foreach ($results as $row) {
                $grouped[] = array(
                    'id' => $row->term_id,
                    'name' => $row->name,
                    'parent' => $row->parent
                );
            }
            set_transient($cache_key, $grouped, UBICACION_CACHE_TIME);
        } else {
            $grouped = array();
        }
    }
    return $grouped;
}

/**
 * Texto actual de ubicación
 */
function ubicacion_get_current_location_text($default = 'Seleccionar ubicación') {
    if (empty($_SESSION['muni_name']) || empty($_SESSION['prov_name'])) return $default;
    return $_SESSION['muni_name'] . ', ' . $_SESSION['prov_name'];
}

/**
 * Shortcode [ubicacion_selector] - DISEÑO ORIGINAL
 */
function ubicacion_selector_shortcode($atts) {
    $defaults = array(
        'text_empty' => 'Seleccionar ubicación',
        'class' => 'ubicacion-selector-btn',
        'btn_class' => '',
        'show_icon' => 'true',
        'show_text' => 'true',
        'modal_title' => 'Seleccionar ubicación',
    );
    $atts = shortcode_atts($defaults, $atts, 'ubicacion_selector');
    
    $ubicacion_texto = ubicacion_get_current_location_text($atts['text_empty']);
    $has_ubicacion = !empty($_SESSION['prov_id']);
    
    ob_start();
    ?>
    <button type="button" class="<?php echo esc_attr(trim($atts['class'] . ' ' . $atts['btn_class'] . ' ' . ($has_ubicacion ? 'has-location' : 'no-location'))); ?>" 
            id="btn-open-location-modal" 
            data-has-location="<?php echo $has_ubicacion ? '1' : '0'; ?>">
        <?php if ($atts['show_icon'] === 'true'): ?>
            <span class="icon-ubicacion"></span>
        <?php endif; ?>
        <?php if ($atts['show_text'] === 'true'): ?>
            <span class="ubicacion-text"><?php echo esc_html($ubicacion_texto); ?></span>
        <?php endif; ?>
    </button>
    <?php
    if (!defined('UBICACION_MODAL_ADDED')) {
        echo ubicacion_render_modal($atts['modal_title']);
        define('UBICACION_MODAL_ADDED', true);
    }
    return ob_get_clean();
}
add_shortcode('ubicacion_selector', 'ubicacion_selector_shortcode');

/**
 * Renderizado del Modal - DISEÑO ORIGINAL (con clase js-searchable-select)
 */
function ubicacion_render_modal($title = 'Selecciona tu ubicación') {
    $provinces = ubicacion_get_provinces();
    $all_municipalities = ubicacion_get_all_municipalities_grouped();
    $current_province = $_SESSION['prov_id'] ?? '';
    
    ob_start();
    ?>
    <div id="ubicacion-selector-modal" class="ubicacion-modal" style="display: none;" aria-hidden="true"
         data-all-municipalities='<?php echo json_encode($all_municipalities); ?>'>
        <div class="ubicacion-modal-content" role="dialog" aria-labelledby="ubicacion-modal-title">
            <button type="button" id="btn-close-ubicacion-modal" style="
            position: absolute;
            background: none;
            border: none;
            font-size: 30px;
            cursor: pointer;
            color: #000;
            z-index: 10;
            top: 15px;
            right: 20px;">&times;</button>
            
            <div class="ubicacion-modal-body">
                <h3 id="ubicacion-modal-title"><?php echo esc_html($title); ?></h3>
                <form id="ubicacion-selector-form" method="post" class="ubicacion-form">
                    <div class="ubicacion-form-group">
                        <label for="ubicacion-province-select" class="ubicacion-label">
                            <span class="icono-provincia icon-white"></span>
                        </label>
                        <select name="province" id="ubicacion-province-select" class="ubicacion-select js-searchable-select">
                            <option value="">Selecciona una provincia</option>
                            <?php foreach ($provinces as $province): ?>
                                <option value="<?php echo esc_attr($province->term_id); ?>" <?php selected($current_province, $province->term_id); ?>>
                                    <?php echo esc_html($province->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="ubicacion-form-group">
                        <label for="ubicacion-municipality-select" class="ubicacion-label">
                            <span class="icono-municipio icon-white"></span>
                        </label>
                        <select name="municipality" id="ubicacion-municipality-select" class="ubicacion-select js-searchable-select" <?php echo !$current_province ? 'disabled' : ''; ?>>
                            <option value="">Seleccionar municipio</option>
                        </select>
                    </div>
                    <div class="ubicacion-form-group ubication-form-submit">
                        <button type="submit" class="btn btn-second" id="btn-confirm-ubicacion">
                            <span class="ubicacion-submit-btn font-semibold">Confirmar ubicación</span>
                            <span class="btn-loader" style="display: none;">
                                <span class="spinner"></span>
                            </span>
                        </button>
                    </div>
                </form>
                <div id="ubicacion-message" class="ubicacion-message" style="display: none;" role="alert"></div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Handler AJAX - Almacenamiento de datos (Nombres y Descripción)
 */
function ubicacion_ajax_store_location() {
    // Verificar nonce y devolver error específico
    if (!check_ajax_referer('ubicacion_nonce', 'nonce', false)) {
        wp_send_json_error(array('message' => 'Nonce inválido. Recarga la página.'));
        return;
    }

    if (empty($_POST['form_data'])) {
        wp_send_json_error(array('message' => 'No se recibieron datos.'));
        return;
    }

    parse_str($_POST['form_data'], $form_data);
    if (empty($form_data['province']) || empty($form_data['municipality'])) {
        wp_send_json_error(array('message' => 'Faltan provincia o municipio.'));
        return;
    }

    $province_id = (int)$form_data['province'];
    $municipality_id = (int)$form_data['municipality'];

    $prov_term = get_term($province_id, 'provincia');
    $muni_term = get_term($municipality_id, 'provincia');

    if (!$prov_term || is_wp_error($prov_term) || !$muni_term || is_wp_error($muni_term)) {
        wp_send_json_error(array('message' => 'Provincia o municipio no encontrados.'));
        return;
    }

    // Iniciar sesión si no está activa
    if (!session_id()) session_start();
    $_SESSION['prov_id']   = $province_id;
    $_SESSION['muni_id']   = $municipality_id;
    $_SESSION['prov_name'] = $prov_term->name;
    $_SESSION['muni_name'] = $muni_term->name;
    $_SESSION['muni_desc'] = $muni_term->description;

    wp_send_json_success(array(
        'new_text' => $muni_term->name . ', ' . $prov_term->name
    ));
}
add_action('wp_ajax_ubicacion_store_location', 'ubicacion_ajax_store_location');
add_action('wp_ajax_nopriv_ubicacion_store_location', 'ubicacion_ajax_store_location');

/**
 * Encolar scripts y estilos externos
 */
function ubicacion_enqueue_scripts() {
    // Estilos
    wp_enqueue_style('ubicacion-styles', UBICACION_ASSETS_URL . '/css/ubicacion-styles.css', array(), '1.6');

    // Script del componente de búsqueda (vanilla JS)
    wp_enqueue_script('ubicacion-searchable', UBICACION_ASSETS_URL . '/js/ubicacion-searchable.js', array(), '1.6', true);

    // Script del modal (depende de jQuery y del script de búsqueda)
    wp_enqueue_script('ubicacion-modal', UBICACION_ASSETS_URL . '/js/ubicacion-modal.js', array('jquery', 'ubicacion-searchable'), '1.6', true);

    // Pasar datos de configuración al script del modal
    wp_localize_script('ubicacion-modal', 'ubicacionConfig', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('ubicacion_nonce'),
        'current_province' => $_SESSION['prov_id'] ?? '',
        'current_municipality' => $_SESSION['muni_id'] ?? '',
        'is_shop_page' => function_exists('is_shop') ? is_shop() : (strpos($_SERVER['REQUEST_URI'], '/tienda/') !== false),
        'i18n' => array(
            'select_muni' => 'Seleccionar municipio',
            'success' => '¡Ubicación guardada!',
            'error' => 'Error al guardar'
        )
    ));
}
add_action('wp_enqueue_scripts', 'ubicacion_enqueue_scripts', 20);