<?php
/**
 * Dashboard de Mi Cuenta - IslaBeya
 * 
 * @package IslaBeya
 */

defined( 'ABSPATH' ) || exit;

$current_user = wp_get_current_user();
$customer_orders = wc_get_orders( array(
    'customer' => get_current_user_id(),
    'limit'    => 5,
    'orderby'  => 'date',
    'order'    => 'DESC',
) );
?>

<div class="islabeya-dashboard-container">
    <div class="dashboard-welcome-card">
        <div class="welcome-avatar">
            <?php echo get_avatar( $current_user->ID, 80 ); ?>
        </div>
        <div class="welcome-text">
            <h3><?php echo sprintf( esc_html__( '¡Bienvenido de vuelta, %s!', 'islabeya' ), esc_html( $current_user->display_name ) ); ?></h3>
            <p><?php esc_html_e( 'Desde tu panel de control puedes gestionar tus pedidos y datos de cuenta.', 'islabeya' ); ?></p>
        </div>
    </div>

    <div class="dashboard-stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div class="stat-number"><?php echo wc_get_customer_order_count( get_current_user_id() ); ?></div>
            <div class="stat-label"><?php esc_html_e( 'Pedidos realizados', 'islabeya' ); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-number"><?php echo wc_price( wc_get_customer_total_spent( get_current_user_id() ) ); ?></div>
            <div class="stat-label"><?php esc_html_e( 'Total gastado', 'islabeya' ); ?></div>
        </div>
    </div>

    <?php if ( ! empty( $customer_orders ) ) : ?>
        <div class="dashboard-recent-orders">
            <h4><?php esc_html_e( 'Pedidos recientes', 'islabeya' ); ?></h4>
            <div class="recent-orders-table">
                <table class="woocommerce-orders-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Pedido', 'islabeya' ); ?></th>
                            <th><?php esc_html_e( 'Fecha', 'islabeya' ); ?></th>
                            <th><?php esc_html_e( 'Total', 'islabeya' ); ?></th>
                            <th><?php esc_html_e( 'Estado', 'islabeya' ); ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $customer_orders as $order ) : ?>
                            <tr>
                                <td>#<?php echo esc_html( $order->get_order_number() ); ?></td>
                                <td><?php echo wc_format_datetime( $order->get_date_created() ); ?></td>
                                <td><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></td>
                                <td><span class="order-status status-<?php echo esc_attr( $order->get_status() ); ?>"><?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></span></td>
                                <td><a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" class="btn-view-order"><?php esc_html_e( 'Ver', 'islabeya' ); ?></a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="dashboard-view-all">
                <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>" class="btn-view-all">
                    <?php esc_html_e( 'Ver todos los pedidos →', 'islabeya' ); ?>
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>