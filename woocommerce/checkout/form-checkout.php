<?php
/**
 * Checkout Form - VERSIÓN IslaBeya (2 columnas / 3 pasos)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
// * if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
// *	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
// *	return;
// * } 

$needs_shipping_step = function_exists( 'islabeya_carrito_necesita_envio_domicilio' ) && islabeya_carrito_necesita_envio_domicilio();
$steps               = array(
	1 => __( 'Facturación', 'islabeya' ),
	2 => __( 'Envío', 'islabeya' ),
	3 => __( 'Pago', 'islabeya' ),
);

    // Si no hay envío a domicilio, el paso 2 no se muestra.
    if ( ! $needs_shipping_step ) {
        unset( $steps[2] );
    }

    $step_count = count( $steps );
    $step_keys  = array_keys( $steps );
?>

<form name="checkout" method="post" class="checkout woocommerce-checkout islabeya-checkout-3pasos isla-form" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__( 'Checkout', 'woocommerce' ); ?>">

	<?php if ( $checkout->get_checkout_fields() ) : ?>

		<div class="islabeya-checkout-3pasos__layout">

			<!-- ============ COLUMNA IZQUIERDA: FORMULARIO EN PASOS ============ -->
			<div class="islabeya-checkout-3pasos__col-form">

<!-- Indicador de progreso -->
				<div class="islabeya-checkout-progress" id="islabeya-checkout-progress" data-step-count="<?php echo esc_attr( $step_count ); ?>">
					<?php
					$i = 1;
					foreach ( $steps as $step_num => $step_label ) :
						$active = 1 === $step_num ? ' is-active' : '';
						?>
						<button
							type="button"
							class="islabeya-checkout-progress__item<?php echo esc_attr( $active ); ?>"
							data-step="<?php echo esc_attr( $step_num ); ?>"
							aria-current="<?php echo 1 === $step_num ? 'step' : 'false'; ?>"
						>
							<span class="islabeya-checkout-progress__num"><?php echo esc_html( $step_num ); ?></span>
							<span class="islabeya-checkout-progress__label"><?php echo esc_html( $step_label ); ?></span>
						</button>
						<?php if ( $i < $step_count ) : ?>
							<span class="islabeya-checkout-progress__line" data-step="<?php echo esc_attr( $step_num ); ?>" aria-hidden="true"></span>
						<?php endif; ?>
						<?php
						$i++;
					endforeach;
					?>
				</div>

				<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

<!-- PASO 1: FACTURACIÓN -->	
				<section class="islabeya-checkout-step islabeya-checkout-step--billing is-active" id="islabeya-step-1" data-step="1">
					<div class="islabeya-checkout-step__header">
						<span class="islabeya-checkout-step__num">1</span>
						<h3 class="islabeya-checkout-step__title"><?php esc_html_e( 'Datos de facturación', 'islabeya' ); ?></h3>
					</div>
					<div class="islabeya-checkout-step__body">
						<?php do_action( 'woocommerce_checkout_billing' ); ?>
					</div>
					<div class="islabeya-checkout-step__actions">
						<button type="button" class="button islabeya-checkout-btn islabeya-checkout-btn--next" data-step="1">
							<?php esc_html_e( 'Continuar', 'islabeya' ); ?>
							<span class="islabeya-checkout-btn__arrow" aria-hidden="true">→</span>
						</button>
					</div>
				</section>

				<?php if ( $needs_shipping_step ) : ?>
<!-- PASO 2: ENVÍO -->
					<section class="islabeya-checkout-step islabeya-checkout-step--shipping" id="islabeya-step-2" data-step="2">
						<div class="islabeya-checkout-step__header">
							<span class="islabeya-checkout-step__num">2</span>
							<h3 class="islabeya-checkout-step__title"><?php esc_html_e( 'Dirección de envío', 'islabeya' ); ?></h3>
						</div>
						<div class="islabeya-checkout-step__body">
							<?php do_action( 'woocommerce_checkout_shipping' ); ?>
						</div>
						<div class="islabeya-checkout-step__actions">
							<button type="button" class="button islabeya-checkout-btn islabeya-checkout-btn--back" data-step="2">
								<span class="islabeya-checkout-btn__arrow islabeya-checkout-btn__arrow--left" aria-hidden="true">←</span>
								<?php esc_html_e( 'Volver', 'islabeya' ); ?>
							</button>
							<button type="button" class="button islabeya-checkout-btn islabeya-checkout-btn--next" data-step="2">
								<?php esc_html_e( 'Continuar', 'islabeya' ); ?>
								<span class="islabeya-checkout-btn__arrow" aria-hidden="true">→</span>
							</button>
						</div>
					</section>
				<?php endif; ?>

<!-- PASO 3 (o 2 si no hay envío): PAGO -->
				<section class="islabeya-checkout-step islabeya-checkout-step--payment" id="islabeya-step-<?php echo $needs_shipping_step ? 3 : 2; ?>" data-step="<?php echo $needs_shipping_step ? 3 : 2; ?>">
					<div class="islabeya-checkout-step__header">
						<span class="islabeya-checkout-step__num"><?php echo $needs_shipping_step ? 3 : 2; ?></span>
						<h3 class="islabeya-checkout-step__title"><?php esc_html_e( 'Método de pago', 'islabeya' ); ?></h3>
					</div>
<div class="islabeya-checkout-step__body">
						<?php
						// Definir $order_button_text antes de renderizar payment.php (requerido por WooCommerce).
						// Sin esta variable, el botón "Pagar + total" se renderiza vacío en la carga inicial.
						global $order_button_text;
						$order_button_text = apply_filters( 'woocommerce_order_button_text', __( 'Realizar pedido', 'woocommerce' ) );
						wc_get_template( 'checkout/payment.php', array( 'order_button_text' => $order_button_text ) );
						?>
					</div>
					<div class="islabeya-checkout-step__actions">
						<button type="button" class="button islabeya-checkout-btn islabeya-checkout-btn--back" data-step="<?php echo $needs_shipping_step ? 3 : 2; ?>">
							<span class="islabeya-checkout-btn__arrow islabeya-checkout-btn__arrow--left" aria-hidden="true">←</span>
							<?php esc_html_e( 'Volver', 'islabeya' ); ?>
						</button>
					</div>
				</section>

				<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

			</div>

			<!-- ============ COLUMNA DERECHA: RESUMEN DEL PEDIDO ============ -->
			<aside class="islabeya-checkout-3pasos__col-resumen" id="islabeya-checkout-resumen">

				<div class="islabeya-checkout-resumen__inner">
					<h3 class="islabeya-checkout-resumen__titulo"><?php esc_html_e( 'Resumen del pedido', 'islabeya' ); ?></h3>

				<div class="islabeya-checkout-resumen__productos">
						<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
						<div id="order_review" class="woocommerce-checkout-review-order">
							<?php wc_get_template( 'checkout/review-order.php' ); ?>
						</div>
						<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
					</div>

					<?php if ( function_exists( 'islabeya_checkout_cupon_columna_derecha' ) ) : ?>
						<div class="islabeya-checkout-resumen__cupon">
							<?php islabeya_checkout_cupon_columna_derecha(); ?>
						</div>
					<?php endif; ?>

					<?php if ( function_exists( 'islabeya_checkout_resumen_envio' ) ) : ?>
						<div class="islabeya-checkout-resumen__envio">
							<?php islabeya_checkout_resumen_envio(); ?>
						</div>
					<?php endif; ?>

					<a class="islabeya-checkout-resumen__seguir-comprando" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
						<span class="islabeya-checkout-resumen__seguir-comprando-arrow" aria-hidden="true">←</span>
						<?php esc_html_e( 'Seguir comprando', 'islabeya' ); ?>
					</a>

				</div>

			</aside>

		</div>

	<?php endif; ?>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

