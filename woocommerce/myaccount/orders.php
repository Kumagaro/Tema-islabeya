<?php
/**
 * Listado de pedidos - IslaBeya
 * 
 * @package IslaBeya
 */

defined( 'ABSPATH' ) || exit;

$customer_orders = wc_get_orders( array(
    'customer' => get_current_user_id(),
    'limit'    => -1,
    'orderby'  => 'date',
    'order'    => 'DESC',
) );
?>

<div class="islabeya-orders-container">
    <h2 class="section-title"><?php esc_html_e( 'Mis Pedidos', 'islabeya' ); ?></h2>
    
    <?php if ( empty( $customer_orders ) ) : ?>
        <div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
            <p><?php esc_html_e( 'No has realizado ningún pedido todavía.', 'islabeya' ); ?></p>
            <a class="woocommerce-Button button" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
                <?php esc_html_e( 'Ir a la tienda', 'islabeya' ); ?>
            </a>
        </div>
    <?php else : ?>
        <div class="orders-table-wrapper">
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
                            <td>
                                <span class="order-status status-<?php echo esc_attr( $order->get_status() ); ?>">
                                    <?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" class="btn-view-order">
                                    <?php esc_html_e( 'Ver detalles', 'islabeya' ); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>