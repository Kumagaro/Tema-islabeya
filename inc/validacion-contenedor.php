<?php

// VALIDACIÓN PARA PRODUCTOS DE CATEGORÍA "CONTENEDOR"

// Requisitos:
// - Ser PYME/TCP
// - Estar inscrito en Impocaribe

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Render de checkboxes en página del producto
 */
function islabeya_contenedor_casillas_verificacion() {
    global $product;

    if ( ! $product || ! is_product() ) {
        return;
    }

    if ( ! has_term( 'contenedor', 'product_cat', $product->get_id() ) ) {
        return;
    }
    ?>

    <div class="islabeya-contenedor-validacion">
        <h4 style="margin: 0 0 15px 0; color: #333; font-size: 16px;">Requisitos para comprar contenedores</h4>
        <p style="margin: 0 0 15px 0; color: #666; font-size: 13px;">Para poder adquirir este contenedor, debes cumplir con los siguientes requisitos:</p>

        <div style="margin: 15px 0;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 12px; padding: 8px; background: #fff; border-radius: 8px;">
                <input type="checkbox" class="islabeya-requisito-checkbox" data-requisito="tipo_cliente">
                <span style="flex: 1;">
                    <strong>Ser PYME o TCP</strong>
                    <span style="display: block; font-size: 12px; color: #666;">Debes estar registrado como Pequeña y Mediana Empresa (PYME) o Trabajador por Cuenta Propia (TCP).</span>
                </span>
            </label>

            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 8px; background: #fff; border-radius: 8px;">
                <input type="checkbox" class="islabeya-requisito-checkbox" data-requisito="impocaribe">
                <span style="flex: 1;">
                    <strong>Estar inscrito en Impocaribe</strong>
                    <span style="display: block; font-size: 12px; color: #666;">Debes estar registrado en el registro de importadores de Impocaribe.</span>
                </span>
            </label>
        </div>

        <div id="islabeya-contenedor-error" style="display: none; background: #FFEBEE; border-left: 4px solid #F44336; padding: 10px 15px; margin-top: 15px; border-radius: 4px;">
            <p style="margin: 0; color: #C62828; font-size: 13px;">
                Debes aceptar TODOS los requisitos para poder comprar este contenedor.
            </p>
        </div>
    </div>

    <style>
        .islabeya-contenedor-validacion label:hover {
            background: #F0F0F0 !important;
            transition: background 0.2s ease;
        }
        .islabeya-requisito-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
    </style>

    <script type="text/javascript">
    jQuery(document).ready(function($) {
        function verificarRequisitos() {
            var todosMarcados = true;
            $('.islabeya-requisito-checkbox').each(function() {
                if (!$(this).is(':checked')) {
                    todosMarcados = false;
                }
            });
            return todosMarcados;
        }

        function actualizarBotones() {
            var requisitosOk = verificarRequisitos();

            // Bloquear/permitir botón normal de WooCommerce
            var $addToCartBtn = $('.single_add_to_cart_button');
            if (!requisitosOk) {
                $addToCartBtn.prop('disabled', true).addClass('disabled');
            } else {
                $addToCartBtn.prop('disabled', false).removeClass('disabled');
            }


            // Bloquear/permitir nuestro botón FunnelKit (solo por requisitos contenedor)
            // IMPORTANT: NO sobrescribir el disabled calculado por variaciones/cantidad,
            // para evitar conflictos con el JS de `inc/boton-comprar-ahora.php`.
            if (!requisitosOk) {
                $('.comprar-ahora-btn-fk').prop('disabled', true).addClass('disabled');
            } else {
                $('.comprar-ahora-btn-fk').prop('disabled', false).removeClass('disabled');
            }


            if (requisitosOk) {
                $('#islabeya-contenedor-error').hide();
            }
        }

        // Inicializar
        actualizarBotones();

        $(document).on('change', '.islabeya-requisito-checkbox', function() {
            actualizarBotones();
        });

        // Validar submit del carrito normal
        $('form.cart').on('submit', function(e) {
            if (!verificarRequisitos()) {
                e.preventDefault();
                $('#islabeya-contenedor-error').show();
                $('html, body').animate({
                    scrollTop: $('#islabeya-contenedor-error').offset().top - 100
                }, 500);
                return false;
            }

            // Agregar hidden fields (si no existen)
            var $form = $(this);
            if ($form.find('input[name="islabeya_tipo_cliente"]').length === 0) {
                $form.append('<input type="hidden" name="islabeya_tipo_cliente" value="pyme_tcp">');
            }
            if ($form.find('input[name="islabeya_impocaribe"]').length === 0) {
                $form.append('<input type="hidden" name="islabeya_impocaribe" value="inscrito">');
            }
        });

        // Validación UX: antes de ejecutar COMPRAR AHORA (FunnelKit)
        $(document).on('click', '.comprar-ahora-btn-fk', function(e) {
            if ( $(this).prop('disabled') ) {
                // ya bloqueado
                e.preventDefault();
                $('#islabeya-contenedor-error').show();
                $('html, body').animate({
                    scrollTop: $('#islabeya-contenedor-error').offset().top - 100
                }, 500);
                return false;
            }

            // Por seguridad adicional (por si el botón no quedó disabled)
            if (!verificarRequisitos()) {
                e.preventDefault();
                $('#islabeya-contenedor-error').show();
                $('html, body').animate({
                    scrollTop: $('#islabeya-contenedor-error').offset().top - 100
                }, 500);
                return false;
            }
        });
    });
    </script>

    <?php
}
add_action( 'woocommerce_before_add_to_cart_button', 'islabeya_contenedor_casillas_verificacion', 20 );


/**
 * Validación para el carrito normal (servidor)
 */
function islabeya_validar_contenedor_antes_carrito( $passed, $product_id, $quantity ) {
    // Bloquea solo si el producto es de la categoría contenedor

    if ( ! has_term( 'contenedor', 'product_cat', $product_id ) ) {
        return $passed;
    }

    $tipo_cliente = isset( $_POST['islabeya_tipo_cliente'] ) ? sanitize_text_field( $_POST['islabeya_tipo_cliente'] ) : '';
    $impocaribe   = isset( $_POST['islabeya_impocaribe'] ) ? sanitize_text_field( $_POST['islabeya_impocaribe'] ) : '';

    if ( empty( $tipo_cliente ) || empty( $impocaribe ) ) {
        wc_add_notice(
            __( '⚠️ Para comprar este contenedor, debes aceptar TODOS los requisitos obligatorios: Ser PYME/TCP y estar inscrito en Impocaribe.', 'islabeya' ),
            'error'
        );
        return false;
    }

    return $passed;
}
add_filter( 'woocommerce_add_to_cart_validation', 'islabeya_validar_contenedor_antes_carrito', 10, 3 );

