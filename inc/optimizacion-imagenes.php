<?php
/**
 * ============================================
 * OPTIMIZACIÓN DE IMÁGENES - SRCSET/SIZES
 * ============================================
 *
 * Funciones helper para generar srcset y sizes optimizados
 * Mejora el LCP y reduce el tamaño de descarga de imágenes
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Genera srcset/sizes para banners del carrusel móvil
 * 
 * @param string $image_path Ruta de la imagen relativa a /assets/img/
 * @param bool $is_first Es la primera imagen (LCP)
 * @return string Atributos HTML
 */
function islabeya_banner_movil_srcset($image_path, $is_first = false) {
    $theme_uri = get_template_directory_uri();
    $image_url = $theme_uri . '/assets/img/' . $image_path;
    
    // Para banners móviles: 371px es el tamaño mostrado
    $srcset = array(
        $image_url . ' 371w',
        $image_url . ' 480w',
        $image_url . ' 768w',
    );
    
    $sizes = array(
        '(max-width: 480px) 371px',
        '(max-width: 768px) 480px',
        '371px',
    );
    
    $loading = $is_first ? 'eager' : 'lazy';
    $fetchpriority = $is_first ? 'fetchpriority="high"' : '';
    
    return sprintf(
        'src="%s" srcset="%s" sizes="%s" loading="%s" %s',
        esc_url($image_url),
        esc_attr(implode(', ', $srcset)),
        esc_attr(implode(', ', $sizes)),
        $loading,
        $fetchpriority
    );
}

/**
 * Genera srcset/sizes para poster de video
 * 
 * @param string $image_path Ruta de la imagen relativa a /assets/img/
 * @return string Atributos HTML
 */
function islabeya_video_poster_srcset($image_path) {
    $theme_uri = get_template_directory_uri();
    $image_url = $theme_uri . '/assets/img/' . $image_path;
    
    // Poster video: 331x186 mostrado
    $srcset = array(
        $image_url . ' 331w',
        $image_url . ' 480w',
        $image_url . ' 853w',
    );
    
    $sizes = array(
        '(max-width: 480px) 331px',
        '(max-width: 768px) 480px',
        '331px',
    );
    
    return sprintf(
        'srcset="%s" sizes="%s"',
        esc_attr(implode(', ', $srcset)),
        esc_attr(implode(', ', $sizes))
    );
}

/**
 * Genera srcset/sizes para iconos de categorías
 * 
 * @param int $attachment_id ID del attachment
 * @param int $display_size Tamaño mostrado (default 70)
 * @return string Atributos HTML
 */
function islabeya_categoria_icon_srcset($attachment_id, $display_size = 70) {
    if (empty($attachment_id)) {
        return '';
    }
    
    $srcset = wp_get_attachment_image_srcset($attachment_id, 'full');
    $sizes = wp_get_attachment_image_sizes($attachment_id, array($display_size, $display_size));
    
    if (empty($srcset) || empty($sizes)) {
        return '';
    }
    
    return sprintf(
        'srcset="%s" sizes="%s" loading="lazy"',
        esc_attr($srcset),
        esc_attr($sizes)
    );
}

/**
 * Configurar tamaños de imagen WooCommerce más pequeños
 * Reduce el tamaño de las imágenes de productos
 */
add_action('after_setup_theme', 'islabeya_configurar_tamano_imagenes_woocommerce');

function islabeya_configurar_tamano_imagenes_woocommerce() {
    // Tamaño thumbnail actual: 300x300 → mostrado a 160x160
    // Reducir a 180x180 para ahorrar espacio
    add_image_size('woocommerce_thumbnail_optimized', 180, 180, true);
    
    // Sobrescribir el tamaño thumbnail de WooCommerce
    add_filter('woocommerce_get_image_size_thumbnail', function($size) {
        return array(
            'width' => 180,
            'height' => 180,
            'crop' => 1,
        );
    });
}

/**
 * Forzar regeneración de imágenes al activar el tema
 * Nota: Esto requiere un plugin como Regenerate Thumbnails
 */
add_action('admin_notices', 'islabeya_notice_regenerar_imagenes');

function islabeya_notice_regenerar_imagenes() {
    if (get_transient('islabeya_imagenes_regeneradas')) {
        return;
    }
    
    $screen = get_current_screen();
    if ($screen && $screen->id !== 'appearance_page_islabeya-theme-settings') {
        return;
    }
    
    echo '<div class="notice notice-info is-dismissible">';
    echo '<p><strong>IslaBeya:</strong> Para optimizar las imágenes de productos, ejecuta el plugin "Regenerate Thumbnails" o "Force Regenerate Thumbnails".</p>';
    echo '</div>';
}
