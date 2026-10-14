<?php 
/**
 * 1. Desvincular los correos de "Nuevo Pedido" predeterminados.
 * Esto evita que se envíen automáticamente al crear el pedido (pending).
 */
add_action( 'woocommerce_email', 'unhook_default_new_order_emails' );
function unhook_default_new_order_emails( $email_class ) {
    // Elimina las acciones que disparan el correo de "Nuevo Pedido" en los cambios de estado habituales.
    remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
    remove_action( 'woocommerce_order_status_pending_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
    remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
    remove_action( 'woocommerce_order_status_failed_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
    remove_action( 'woocommerce_order_status_failed_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
    remove_action( 'woocommerce_order_status_failed_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
}

/**
 * 2. Disparar manualmente el correo de "Nuevo Pedido" solo cuando el pedido se completa.
 */
add_action( 'woocommerce_order_status_completed', 'trigger_new_order_email_on_completed', 20, 2 );
function trigger_new_order_email_on_completed( $order_id, $order ) {
    // Envía la notificación de "Nuevo Pedido" al administrador.
    WC()->mailer()->get_emails()['WC_Email_New_Order']->trigger( $order_id );
}