<?php
/**
 * Detalle de pedido - IslaBeya
 * 
 * @package IslaBeya
 */

defined( 'ABSPATH' ) || exit;

$order_id = absint( get_query_var( 'view-order' ) );
$order = wc_get_order( $order_id );

if ( ! $order || $order->get_customer_id() !== get_current_user_id() ) {
    wc_print_notice( esc_html__( 'Pedido no encontrado.', 'islabeya' ), 'error' );
    return;
}
?>

<div class="islabeya-view-order-container">
    <div class="order-header">
        <h2 class="section-title"><?php printf( esc_html__( 'Pedido #%s', 'islabeya' ), esc_html( $order->get_order_number() ) ); ?></h2>
        <p class="order-date"><?php printf( esc_html__( 'Fecha: %s', 'islabeya' ), wc_format_datetime( $order->get_date_created() ) ); ?></p>
    </div>

    <div class="order-status-info">
        <div class="status-badge status-<?php echo esc_attr( $order->get_status() ); ?>">
            <?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
        </div>
    </div>

    <div class="order-details-grid">
        <div class="order-items-section">
            <h3><?php esc_html_e( 'Productos', 'islabeya' ); ?></h3>
            <div class="order-items-table-contain">
                <table class="order-items-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Producto', 'islabeya' ); ?></th>
                            <th><?php esc_html_e( 'Cantidad', 'islabeya' ); ?></th>
                            <th><?php esc_html_e( 'Total', 'islabeya' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $order->get_items() as $item_id => $item ) : ?>
                            <tr>
                                <td><?php echo esc_html( $item->get_name() ); ?></td>
                                <td><?php echo esc_html( $item->get_quantity() ); ?></td>
                                <td><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="order-total">
                            <th colspan="2"><?php esc_html_e( 'Total', 'islabeya' ); ?></th>
                            <td><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="order-address-section">
            <h3><?php esc_html_e( 'Dirección de envío', 'islabeya' ); ?></h3>
            <div class="address-card">
                <?php echo wp_kses_post( $order->get_formatted_shipping_address() ?: esc_html__( 'No disponible', 'islabeya' ) ); ?>
            </div>
            
            <h3><?php esc_html_e( 'Dirección de facturación', 'islabeya' ); ?></h3>
            <div class="address-card">
                <?php echo wp_kses_post( $order->get_formatted_billing_address() ?: esc_html__( 'No disponible', 'islabeya' ) ); ?>
            </div>
        </div>
    </div>
    
    <div class="order-actions">
        <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>" class="btn-back-to-orders">
            ← <?php esc_html_e( 'Volver a mis pedidos', 'islabeya' ); ?>
        </a>
    </div>
</div>