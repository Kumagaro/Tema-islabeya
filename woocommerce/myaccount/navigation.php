<?php
/**
 * Plantilla reutilizable para el menú de navegación de Mi Cuenta
 * 
 * @package IslaBeya
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_navigation' );

// Obtener todos los elementos del menú
$menu_items = wc_get_account_menu_items();

// Definir los endpoints que queremos excluir
$excluded_endpoints = array(
    'downloads',        // Descargar
    'payment-methods',  // Métodos de Pago
    'edit-address',     // Direcciones (incluye envío y facturación)
);

// Filtrar el menú eliminando los elementos excluidos
foreach ( $excluded_endpoints as $endpoint ) {
    if ( isset( $menu_items[ $endpoint ] ) ) {
        unset( $menu_items[ $endpoint ] );
    }
}

// Mapeo de iconos para cada endpoint
$endpoint_icons = array(
    'dashboard'        => 'icon-dashboard',
    'orders'           => 'icon-notes',
    'edit-account'     => 'icon-user-id',
    'customer-logout'  => 'icon-power',
);

// Obtener el endpoint actual (forma correcta)
global $wp;
$current_endpoint = '';

// Verificar si hay un endpoint en la URL actual
if ( isset( $wp->query_vars ) ) {
    foreach ( $menu_items as $key => $value ) {
        if ( isset( $wp->query_vars[ $key ] ) ) {
            $current_endpoint = $key;
            break;
        }
    }
}

// Si no hay endpoint (estamos en el dashboard), usar 'dashboard'
if ( empty( $current_endpoint ) ) {
    $current_endpoint = 'dashboard';
}
?>
<button class="icon-principal" onclick="toggleSidebar()" id="toggle-btn">
    <span class="icon-hamburger icon-menu"></span>
</button>
<nav id="sidebar" class="woocommerce-MyAccount-navigation islabeya-account-nav">
    
    <ul>
        <li class="menu-cuenta-movil"> 
            <button class="menu-cuenta-movil" onclick="toggleSidebar()" id="toggle-btn">
                <span class="icon-hamburger icon-menu"></span>
            </button>
        </li>
        <li class="menu-cuenta-movil">
            <a href="<?php echo home_url(); ?>">
                <span class="icon-arrow-left icon-menu"></span>
                <span>Regresar</span>
            </a>
        </li>
        <?php foreach ( $menu_items as $endpoint => $label ) : ?>
            <?php
            // Determinar si este elemento es el activo
            $is_active = false;
            
            // Caso especial para dashboard
            if ( $endpoint === 'dashboard' && $current_endpoint === 'dashboard' ) {
                $is_active = true;
            }
            // Para otros endpoints, comparar directamente
            elseif ( $endpoint === $current_endpoint ) {
                $is_active = true;
            }
            
            $active_class = $is_active ? 'is-active' : '';
            
            // Obtener la clase del icono para este endpoint
            $icon_class = isset( $endpoint_icons[ $endpoint ] ) ? $endpoint_icons[ $endpoint ] : '';
            ?>
            <li class="<?php echo esc_attr( $active_class ); ?>">
                <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>">
                    <span class="<?php echo esc_attr( $icon_class ); ?> icon-menu"></span>
                    <span><?php echo esc_html( $label ); ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>