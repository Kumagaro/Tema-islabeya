/**
 * SISTEMA DE VALIDACIÓN GLOBAL — IslaBeya
 * =========================================
 * Sistema reutilizable de validación client-side con atributos data-*.
 * Se auto-inicializa en todos los formularios con clase .isla-form.
 *
 * Atributos soportados:
 *   data-required       → Campo obligatorio
 *   data-email          → Formato email válido
 *   data-numeric        → Solo dígitos
 *   data-length="11"    → Exactamente 11 caracteres
 *   data-minlength="8"  → Mínimo 8 caracteres
 *   data-match="#id"    → Debe coincidir con el valor de otro campo
 *
 * Compatible con:
 *   - searchable-select (provincia/municipio)
 *   - intl-tel-input (teléfono)
 *   - updated_checkout event de WooCommerce
 *   - Checkout por pasos (checkout-pasos.js)
 */
(function($) {
    'use strict';

    var IslaValidate = {
        /**
         * Inicializar el sistema: escanear formularios y enlazar eventos
         */
        init: function(formSelector) {
            var selector = formSelector || '.isla-form';
            var self = this;

            // Procesar cada formulario con clase isla-form
            $(selector).each(function() {
                var $form = $(this);
                if ($form.data('isla-initialized')) return;
                $form.data('isla-initialized', true);

                // Marcar campos con atributos data-*
                self._marcarCamposNumericos($form);
                self._enlazarValidacionEnTiempoReal($form);
                self._enlazarSubmit($form);
                self._enlazarSearchableSelects($form);
            });
        },

        /**
         * Marcar campos numéricos con inputmode="numeric" y pattern
         * para que en móviles se muestre el teclado numérico
         */
        _marcarCamposNumericos: function($form) {
            var self = this;

            // Campos con data-numeric y campos type="tel"
            $form.find('[data-numeric], input[type="tel"]').each(function() {
                var $input = $(this);

                // Aplicar inputmode y pattern para teclado numérico en móviles
                if (!$input.attr('inputmode')) {
                    $input.attr('inputmode', 'numeric');
                }
                if (!$input.attr('pattern')) {
                    $input.attr('pattern', '[0-9]*');
                }

                // Clase CSS para eliminar spinners numéricos
                if ($input.attr('type') !== 'tel') {
                    $input.addClass('isla-numeric-field');
                }

                // Para inputs type="tel" de intl-tel-input, también aseguramos
                if ($input.attr('id') === 'billing_phone' || $input.attr('id') === 'shipping_phone') {
                    // intl-tel-input maneja su propio input, dejamos pasar
                }
            });

            // Carnet de identidad: forzar type="text" + inputmode y pattern
            $form.find('#shipping_identity_card_field input, [name="shipping_identity_card"]').each(function() {
                var $input = $(this);
                $input.attr('inputmode', 'numeric');
                $input.attr('pattern', '[0-9]*');
                $input.attr('maxlength', '11');
                $input.addClass('isla-numeric-field');
            });
        },

        /**
         * Enlazar validación en tiempo real (blur + input)
         */
        _enlazarValidacionEnTiempoReal: function($form) {
            var self = this;

            // Validar al salir del campo (blur)
            $form.on('blur.islaValidate', '[data-required], [data-email], [data-numeric], [data-length], [data-minlength], [data-match]', function() {
                var $input = $(this);
                self._validarCampo($input, $form);
            });

            // Limpiar error al escribir (input)
            $form.on('input.islaValidate', '[data-required], [data-email], [data-numeric], [data-length], [data-minlength], [data-match]', function() {
                var $input = $(this);
                var $msg = $input.closest('.form-row, .woocommerce-billing-fields__field-wrapper > p, .woocommerce-shipping-fields__field-wrapper > p, p.form-row').find('.isla-error-msg');

                // Solo limpiar si el campo ahora es válido
                if ($msg.length) {
                    var errores = self._obtenerErrores($input);
                    if (errores.length === 0) {
                        self._limpiarError($input);
                    }
                }
            });
        },

        /**
         * Enlazar submit del formulario
         */
        _enlazarSubmit: function($form) {
            var self = this;

            $form.on('submit.islaValidate', function(e) {
                var valido = self._validarFormulario($form);

                if (!valido) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Desplazar al primer error
                    var $primerError = $form.find('.isla-invalid').first();
                    if ($primerError.length) {
                        var top = $primerError.offset().top - 120;
                        $('html, body').animate({ scrollTop: top }, 300);
                        $primerError.trigger('focus');
                    }
                }
            });
        },

        /**
         * Enlazar cambios en searchable-selects (provincia/municipio)
         */
        _enlazarSearchableSelects: function($form) {
            var self = this;

            $form.find('select[data-required].js-searchable-select').each(function() {
                var $select = $(this);
                $select.on('change.islaValidate', function() {
                    var $trigger = $select.closest('.searchable-select-wrapper').find('.searchable-select-trigger');
                    if ($select.val()) {
                        $trigger.removeClass('isla-invalid');
                        var $existingMsg = $trigger.closest('.form-row, .woocommerce-billing-fields__field-wrapper > p, .woocommerce-shipping-fields__field-wrapper > p, p.form-row').find('.isla-error-msg');
                        if ($existingMsg.length) $existingMsg.remove();
                    }
                });
            });
        },

        /**
         * Validar un campo específico
         * @param {jQuery} $input  El campo a validar
         * @param {jQuery} $form   El formulario contenedor
         * @returns {boolean} true si es válido
         */
        _validarCampo: function($input, $form) {
            var self = this;

            // Si el campo está oculto o es un select reemplazado por searchable
            if (!$input.is(':visible') && !$input.is('select')) return true;

            // Si el select tiene searchable-select, validar contra el trigger
            if ($input.is('select.js-searchable-select')) {
                return true; // searchable-select se valida aparte
            }

            var errores = self._obtenerErrores($input);

            if (errores.length > 0) {
                self._mostrarError($input, errores, $form);
                return false;
            } else {
                self._limpiarError($input);
                return true;
            }
        },

        /**
         * Obtener errores de un campo basado en sus data-* attributes
         * @param {jQuery} $input
         * @returns {string[]} Array de mensajes de error
         */
        _obtenerErrores: function($input) {
            var errores = [];
            var valor = $input.val();
            var tipo = $input.attr('type') || 'text';

            // Para intl-tel-input (campos de teléfono)
            var isPhoneField = $input.closest('.iti').length > 0 || $input.attr('id') === 'billing_phone' || $input.attr('id') === 'shipping_phone';

            // data-required
            if ($input.data('required') !== undefined && $input.data('required') !== false) {
                if (!valor || (typeof valor === 'string' && valor.trim() === '')) {
                    errores.push('Este campo es obligatorio');
                    return errores; // Prioridad: si está vacío, no seguir validando
                }
            } else if (!valor || (typeof valor === 'string' && valor.trim() === '')) {
                // Si no es required y está vacío, no validar más
                return errores;
            }

            // data-email
            if ($input.data('email') !== undefined) {
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(valor)) {
                    errores.push('Ingresa un correo electrónico válido');
                }
            }

            // data-numeric
            if ($input.data('numeric') !== undefined) {
                var numericRegex = /^\d+$/;
                if (!numericRegex.test(valor)) {
                    errores.push('Solo se permiten números');
                }
            }

            // data-length (número exacto de caracteres)
            if ($input.data('length') !== undefined) {
                var len = parseInt($input.data('length'));
                if (valor.length !== len) {
                    errores.push('Debe tener exactamente ' + len + ' caracteres');
                }
            }

            // data-minlength
            if ($input.data('minlength') !== undefined) {
                var minLen = parseInt($input.data('minlength'));
                if (valor.length < minLen) {
                    errores.push('Debe tener al menos ' + minLen + ' caracteres');
                }
            }

            // data-match (debe coincidir con otro campo)
            if ($input.data('match')) {
                var matchSelector = $input.data('match');
                var $matchInput = $input.closest('form').find(matchSelector);
                if ($matchInput.length && $matchInput.val() !== valor) {
                    errores.push('Los valores no coinciden');
                }
            }

            return errores;
        },

        /**
         * Mostrar error visual en un campo
         */
        _mostrarError: function($input, errores, $form) {
            var self = this;

            // Buscar el contenedor del campo (varía según el formulario)
            var $row = $input.closest('.form-row, .woocommerce-billing-fields__field-wrapper > p, .woocommerce-shipping-fields__field-wrapper > p, p.form-row');

            // Si no encontró, buscar el wrapper completo
            if (!$row.length) {
                $row = $input.closest('p, div').has('label, input');
            }

            if (!$row.length) return;

            // Agregar borde rojo al campo
            $input.addClass('isla-invalid');

            // Si es searchable-select, marcar también el trigger
            var $searchableWrapper = $input.closest('.searchable-select-wrapper');
            if ($searchableWrapper.length) {
                $searchableWrapper.find('.searchable-select-trigger').addClass('isla-invalid');
            }

            // Crear o actualizar mensaje de error debajo del campo
            var $existingMsg = $row.find('.isla-error-msg');
            var mensaje = errores[0]; // Mostrar solo el primer error

            if ($existingMsg.length) {
                $existingMsg.text(mensaje);
            } else {
                var $msg = $('<div class="isla-error-msg">' + mensaje + '</div>');
                // Insertar después del input (o después del trigger si es searchable)
                if ($searchableWrapper.length) {
                    $msg.insertAfter($searchableWrapper);
                } else {
                    // Insertar después del contenedor del input
                    var $inputWrapper = $input.closest('.iti') || $input.closest('.password-wrapper');
                    if ($inputWrapper.length) {
                        $msg.insertAfter($inputWrapper);
                    } else {
                        $msg.insertAfter($input);
                    }
                }
            }
        },

        /**
         * Limpiar error visual de un campo
         */
        _limpiarError: function($input) {
            $input.removeClass('isla-invalid');

            // Si es searchable-select, limpiar trigger
            var $searchableWrapper = $input.closest('.searchable-select-wrapper');
            if ($searchableWrapper.length) {
                $searchableWrapper.find('.searchable-select-trigger').removeClass('isla-invalid');
            }

            // Buscar contenedor
            var $row = $input.closest('.form-row, .woocommerce-billing-fields__field-wrapper > p, .woocommerce-shipping-fields__field-wrapper > p, p.form-row');
            if (!$row.length) {
                $row = $input.closest('p, div').has('label, input');
            }

            if ($row.length) {
                var $msg = $row.find('.isla-error-msg');
                if ($msg.length) $msg.remove();
            }
        },

        /**
         * Validar todos los campos del formulario y devolver si es válido
         * @param {jQuery} $form
         * @returns {boolean}
         */
        _validarFormulario: function($form) {
            var self = this;
            var valido = true;

            // Limpiar banner de error existente
            $form.find('.isla-error-banner').remove();

            // Validar todos los campos con data-* attributes
            $form.find('[data-required], [data-email], [data-numeric], [data-length], [data-minlength], [data-match]').each(function() {
                var $input = $(this);
                // Ignorar selects que tienen searchable-select (se validan aparte)
                if ($input.is('select.js-searchable-select')) return;
                // Ignorar campos ocultos
                if ($input.attr('type') === 'hidden') return;

                if (!self._validarCampo($input, $form)) {
                    valido = false;
                }
            });

            // Validar searchable-selects (data-required)
            $form.find('select[data-required].js-searchable-select').each(function() {
                var $select = $(this);
                if (!$select.val()) {
                    valido = false;
                    var $trigger = $select.closest('.searchable-select-wrapper').find('.searchable-select-trigger');
                    $trigger.addClass('isla-invalid');
                    var $row = $select.closest('.form-row, .woocommerce-billing-fields__field-wrapper > p, .woocommerce-shipping-fields__field-wrapper > p, p.form-row');
                    if ($row.length && !$row.find('.isla-error-msg').length) {
                        var $msg = $('<div class="isla-error-msg">Este campo es obligatorio</div>');
                        $msg.insertAfter($trigger.closest('.searchable-select-wrapper'));
                    }
                }
            });

            // Si no es válido y se puede mostrar banner (no es checkout por pasos)
            if (!valido && !$form.closest('.islabeya-checkout-3pasos').length) {
                var $errores = $form.find('.isla-invalid');
                if ($errores.length) {
                    var $banner = $('<div class="isla-error-banner">' +
                        '<div class="isla-error-banner__titulo">Corrige los siguientes errores</div>' +
                        '<ul></ul>' +
                        '</div>');
                    var $ul = $banner.find('ul');

                    $errores.each(function() {
                        var $campo = $(this);
                        var label = '';
                        var $row = $campo.closest('.form-row, p.form-row');
                        if ($row.length) {
                            // Buscar una etiqueta label visible o placeholder
                            var $label = $row.find('label');
                            if ($label.length && !$label.hasClass('radio')) {
                                label = $label.text().trim().replace('*', '').trim();
                            } else {
                                label = $campo.attr('placeholder') || $campo.attr('name') || '';
                            }
                        }
                        if (label) {
                            $ul.append('<li>' + label + '</li>');
                        }
                    });

                    $form.prepend($banner);
                }
            }

            // Deshabilitar/habilitar botones de submit/continuar
            self._actualizarBotones($form, valido);

            return valido;
        },

        /**
         * Actualizar estado de botones (submit, continuar, place_order)
         */
        _actualizarBotones: function($form, valido) {
            // Botón "Realizar pedido" (place_order)
            var $placeOrder = $form.find('#place_order');
            if ($placeOrder.length) {
                if (valido) {
                    $placeOrder.prop('disabled', false).removeClass('isla-btn-disabled');
                } else {
                    $placeOrder.prop('disabled', true).addClass('isla-btn-disabled');
                }
            }

            // Botones "Continuar" del checkout por pasos
            $form.find('.islabeya-checkout-btn--next').each(function() {
                var $btn = $(this);
                var stepNum = parseInt($btn.data('step'));
                var $step = $form.find('.islabeya-checkout-step[data-step="' + stepNum + '"]');

                // Validar solo los campos del paso actual
                var stepValido = true;
                if ($step.length && $step.hasClass('is-active')) {
                    $step.find('[data-required], [data-email], [data-numeric], [data-length], [data-minlength], [data-match]').each(function() {
                        var $input = $(this);
                        if ($input.is('select.js-searchable-select')) return;
                        if ($input.attr('type') === 'hidden') return;
                        var errores = IslaValidate._obtenerErrores($input);
                        if (errores.length > 0) {
                            stepValido = false;
                            return false;
                        }
                    });

                    if (!stepValido) {
                        $btn.prop('disabled', true).addClass('isla-btn-disabled');
                    } else {
                        $btn.prop('disabled', false).removeClass('isla-btn-disabled');
                    }
                }
            });
        },

        /**
         * Validación específica para botón "Continuar" en checkout por pasos
         * @param {number} stepNum Número del paso a validar
         * @returns {boolean} true si el paso es válido
         */
        validarPaso: function(stepNum) {
            var $form = $('form.checkout.islabeya-checkout-3pasos');
            if (!$form.length) return true;

            var $step = $form.find('.islabeya-checkout-step[data-step="' + stepNum + '"]');
            if (!$step.length) return true;

            var valido = true;

            // Limpiar errores previos en este paso
            $step.find('.isla-error-msg').remove();
            $step.find('.isla-invalid').removeClass('isla-invalid');

            // Validar todos los campos data-* en el paso
            $step.find('[data-required], [data-email], [data-numeric], [data-length], [data-minlength], [data-match]').each(function() {
                var $input = $(this);
                if ($input.is('select.js-searchable-select')) {
                    // Validar searchable-select
                    if ($input.data('required') !== undefined && !$input.val()) {
                        valido = false;
                        var $trigger = $input.closest('.searchable-select-wrapper').find('.searchable-select-trigger');
                        $trigger.addClass('isla-invalid');
                        var $row = $input.closest('.form-row, p.form-row');
                        if ($row.length && !$row.find('.isla-error-msg').length) {
                            var $msg = $('<div class="isla-error-msg">Este campo es obligatorio</div>');
                            $msg.insertAfter($trigger.closest('.searchable-select-wrapper'));
                        }
                    }
                    return;
                }
                if ($input.attr('type') === 'hidden') return;

                var errores = IslaValidate._obtenerErrores($input);
                if (errores.length > 0) {
                    valido = false;
                    IslaValidate._mostrarError($input, errores, $form);
                }
            });

            // Desplazar al primer error
            if (!valido) {
                var $primerError = $step.find('.isla-invalid').first();
                if ($primerError.length) {
                    var top = $primerError.offset().top - 120;
                    $('html, body').animate({ scrollTop: top }, 300);
                    $primerError.trigger('focus');
                }
            }

            // Actualizar botones
            if (valido) {
                // Habilitar botones de continuar del paso
                $step.find('.islabeya-checkout-btn--next').prop('disabled', false).removeClass('isla-btn-disabled');
            }

            return valido;
        },

        /**
         * Refrescar el sistema (útil tras eventos AJAX como updated_checkout)
         */
        refresh: function() {
            var self = this;
            // Re-inicializar en formularios .isla-form que no se habían inicializado
            $('.isla-form').each(function() {
                var $form = $(this);
                // Si no está inicializado, init() lo hará
                if (!$form.data('isla-initialized')) {
                    self.init();
                } else {
                    // Si ya está inicializado, re-marcar campos numéricos
                    self._marcarCamposNumericos($form);
                    // Re-enlazar searchable-selects que pudieran haber cambiado
                    self._enlazarSearchableSelects($form);
                }
            });
        }
    };

    // ============================================================
    // EXPORTAR AL ÁMBITO GLOBAL
    // ============================================================
    window.IslaValidate = IslaValidate;

    // ============================================================
    // INICIALIZACIÓN
    // ============================================================
    $(document).ready(function() {
        IslaValidate.init();
    });

    // Re-inicializar tras updated_checkout (WooCommerce AJAX)
    $(document.body).on('updated_checkout', function() {
        setTimeout(function() {
            IslaValidate.refresh();
        }, 500);
    });

    // Re-inicializar tras fragment refresh de WooCommerce
    $(document.body).on('wc_fragments_refreshed', function() {
        setTimeout(function() {
            IslaValidate.refresh();
        }, 300);
    });

    // También tras actualizar el carrito
    $(document.body).on('wc_cart_updated', function() {
        setTimeout(function() {
            IslaValidate.refresh();
        }, 300);
    });

})(jQuery);

