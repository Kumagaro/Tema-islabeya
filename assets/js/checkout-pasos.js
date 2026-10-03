/**
 * CHECKOUT IslaBeya — 3 pasos
 *
 * Dependencias: jQuery, WooCommerce checkout
 *
 * Funcionalidades:
 * - Navegación entre pasos con visibility:hidden (no rompe validación)
 * - Validación de campos obligatorios antes de avanzar
 * - Indicador de progreso clickeable (pasos completados)
 * - Cupón en columna derecha vía AJAX
 * - Auto-navegar al paso con error tras validación
 * - Re-inicialización tras fragment refresh de WooCommerce
 */

(function($) {
    'use strict';

    var CHECKOUT = {
        // Configuración
        currentStep: 1,
        stepSelector: '.islabeya-checkout-step',
        progressSelector: '.islabeya-checkout-progress__item',
        progressLineSelector: '.islabeya-checkout-progress__line',
        formSelector: 'form.checkout',

        /**
         * Obtener número de pasos dinámicamente
         */
        getSteps: function() {
            return parseInt($('#islabeya-checkout-progress').data('step-count')) || 3;
        },

/**
         * Inicializar módulo
         */
        init: function() {
            this.determinarPasoInicial();
            this.actualizarUI();
            this.bindNavegacion();
            this.bindProgreso();
            this.bindCoupon();
            this.bindCheckoutEvents();
            this.initIntlTelInput();
        },

        /**
         * Si hay error en algún paso, ir a ese paso. Si no, empezar en paso 1.
         */
        determinarPasoInicial: function() {
            var self = this;
            // Buscar el primer paso con errores
            $(this.stepSelector).each(function() {
                var $step = $(this);
                if ($step.find('.woocommerce-error, .woocommerce-invalid').length > 0) {
                    var stepNum = parseInt($step.data('step'));
                    if (stepNum) {
                        self.currentStep = stepNum;
                        return false;
                    }
                }
            });
        },

        /**
         * Actualizar la UI: visibilidad de pasos, estado del progreso, botones
         */
        actualizarUI: function() {
            var self = this;
            var steps = this.getSteps();

            // 1. Visibilidad de pasos
            $(this.stepSelector).each(function() {
                var $step = $(this);
                var stepNum = parseInt($step.data('step'));

                if (stepNum === self.currentStep) {
                    $step.addClass('is-active').removeClass('is-completed');
                } else if (stepNum < self.currentStep) {
                    $step.removeClass('is-active').addClass('is-completed');
                } else {
                    $step.removeClass('is-active is-completed');
                }
            });

            // 2. Progreso
            $(this.progressSelector).each(function() {
                var $item = $(this);
                var stepNum = parseInt($item.data('step'));

                $item.removeClass('is-active is-completed');

                if (stepNum === self.currentStep) {
                    $item.addClass('is-active');
                } else if (stepNum < self.currentStep) {
                    $item.addClass('is-completed');
                }
            });

            // 3. Líneas de progreso
            $(this.progressLineSelector).each(function() {
                var $line = $(this);
                var lineNum = parseInt($line.data('step'));

                if (lineNum < self.currentStep) {
                    $line.addClass('is-completed');
                } else {
                    $line.removeClass('is-completed');
                }
            });

            // 4. Botón Volver: mostrar solo cuando NO estamos en el paso actual
            $('.islabeya-checkout-btn--back').each(function() {
                var $btn = $(this);
                var stepNum = parseInt($btn.data('step'));
                // Mostrar el botón de volver solo si pertenece al paso actual Y no es paso 1
                if (stepNum === self.currentStep && stepNum > 1) {
                    $btn.show();
                } else {
                    $btn.hide();
                }
            });

            // 5. Botón Continuar: mostrar solo en el paso actual, ocultar en el último paso
            $('.islabeya-checkout-btn--next').each(function() {
                var $btn = $(this);
                var stepNum = parseInt($btn.data('step'));
                if (stepNum === self.currentStep && stepNum < steps) {
                    $btn.show();
                } else {
                    $btn.hide();
                }
            });
        },

        /**
         * Ir a un paso específico
         */
        irAlPaso: function(step) {
            var steps = this.getSteps();
            if (step < 1 || step > steps) return;
            this.currentStep = step;
            this.actualizarUI();
            // Scroll al inicio del formulario
            $('html, body').animate({
                scrollTop: $(this.formSelector).offset().top - 60
            }, 300);
        },

        /**
         * Validar que los campos de Provincia y Municipio/Población del paso
         * actual no estén vacíos. Se aplica a facturación (paso 1) y envío (paso 2).
         * @param {jQuery} $step El paso activo
         * @returns {boolean} true si ambas selecciones son válidas
         */
        validarProvinciaMunicipio: function($step) {
            var valido = true;
            var stepNum = parseInt($step.data('step'));

            // Determinar prefijo según el paso (facturación o envío)
            var prefijo = (stepNum === 1) ? 'billing' : 'shipping';

            var campos = [
                { id: prefijo + '_state',  tipo: 'provincia'   },
                { id: prefijo + '_city',   tipo: 'municipio'   }
            ];

            var self = this;

            $.each(campos, function(i, campo) {
                var $select = $('#' + campo.id);
                // Buscar también el select reemplazado por searchable (si existe)
                var $searchable = $('#' + campo.id + '_select');

                // Elegir qué elemento validar
                var $target = null;
                if ($searchable.length && $searchable.is('select')) {
                    $target = $searchable;
                } else if ($select.length) {
                    $target = $select;
                }

                if (!$target) return; // El campo no existe

                var valor = $target.val();
                var vacio = !valor || (typeof valor === 'string' && valor.trim() === '');

                // Limpiar/marcar el trigger del searchable
                var $trigger = $target.closest('.searchable-select-wrapper').find('.searchable-select-trigger');
                var $row = $target.closest('.form-row');

                if (vacio) {
                    valido = false;
                    if ($trigger.length) {
                        $trigger.addClass('isla-invalid');
                        $trigger.css('border-color', '#e74c3c');
                    } else {
                        $target.addClass('isla-invalid');
                        $row.addClass('woocommerce-invalid woocommerce-invalid-required-field');
                    }
                    // Mensaje simple
                    if ($row.length && !$row.find('.isla-error-msg').length) {
                        var msg = (campo.tipo === 'provincia')
                            ? 'Selecciona la provincia'
                            : 'Selecciona el municipio';
                        var $msg = $('<div class="isla-error-msg">' + msg + '</div>');
                        if ($trigger.closest('.searchable-select-wrapper').length) {
                            $msg.insertAfter($trigger.closest('.searchable-select-wrapper'));
                        } else {
                            $msg.insertAfter($target);
                        }
                    }
                } else {
                    // Limpiar error
                    if ($trigger.length) {
                        $trigger.removeClass('isla-invalid').css('border-color', '');
                    } else {
                        $target.removeClass('isla-invalid');
                        $row.removeClass('woocommerce-invalid woocommerce-invalid-required-field');
                    }
                    if ($row.length) $row.find('.isla-error-msg').remove();
                }
            });

            return valido;
        },

        validarPasoActual: function() {
            var self = this;
            var $step = $(this.stepSelector + '.is-active');
            var stepNum = parseInt($step.data('step'));

            // 1. Primero validar Provincia y Municipio (siempre, en pasos 1 y 2)
            if (stepNum === 1 || stepNum === 2) {
                if (!self.validarProvinciaMunicipio($step)) {
                    // Cancelar la validación global si provincia/municipio están vacíos
                    var $primerError = $step.find('.isla-invalid').first();
                    if ($primerError.length) {
                        $('html', 'body').animate({
                            scrollTop: $primerError.offset().top - 120
                        }, 300);
                    }
                    return false;
                }
            }

            // 2. Delegar el resto de la validación al sistema global IslaValidate
            if (typeof window.IslaValidate !== 'undefined' && typeof window.IslaValidate.validarPaso === 'function') {
                return window.IslaValidate.validarPaso(stepNum);
            }

            // ===== Fallback si IslaValidate no está disponible =====
            var valido = true;

            // Encontrar todos los campos requeridos en el paso actual
            $step.find('.form-row.validate-required input, .form-row.validate-required select, .form-row.validate-required textarea').each(function() {
                var $input = $(this);
                var $row = $input.closest('.form-row');
                var valor = $input.val();

                // Si el campo está oculto (ej: select reemplazado por searchable), omitir
                if (!$input.is(':visible') && !$input.is('select')) return;

                // Quitar clases de error previas
                $row.removeClass('woocommerce-invalid woocommerce-invalid-required-field');

                if (!valor || (typeof valor === 'string' && valor.trim() === '')) {
                    $row.addClass('woocommerce-invalid woocommerce-invalid-required-field');
                    valido = false;
                }
            });

            // Si hay searchable-selects, verificar que tengan valor
            $step.find('.searchable-select-trigger').each(function() {
                var $trigger = $(this);
                var $select = $trigger.closest('.searchable-select-wrapper').find('select');
                if ($select.length && $select.prop('required') && !$select.val()) {
                    valido = false;
                    $trigger.css('border-color', '#e74c3c');
                } else {
                    $trigger.css('border-color', '');
                }
            });

            if (!valido) {
                // Mostrar un mensaje amigable
                var $firstError = $step.find('.woocommerce-invalid-required-field').first();
                if ($firstError.length) {
                    $('html, body').animate({
                        scrollTop: $firstError.offset().top - 100
                    }, 300);
                    $firstError.find('input, select').first().focus();
                }
            }

            return valido;
        },

        /**
         * Vincular botones Continuar / Volver
         */
        bindNavegacion: function() {
            var self = this;

            // Botón "Continuar" (siguiente paso)
            $(document).on('click', '.islabeya-checkout-btn--next', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var stepActual = parseInt($btn.data('step'));

                if (stepActual !== self.currentStep) return;

                // Validar paso actual antes de avanzar
                if (!self.validarPasoActual()) {
                    return;
                }

                // Avanzar al siguiente paso
                self.irAlPaso(stepActual + 1);
            });

            // Botón "Volver" (paso anterior)
            $(document).on('click', '.islabeya-checkout-btn--back', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var stepActual = parseInt($btn.data('step'));

                if (stepActual !== self.currentStep) return;

                self.irAlPaso(stepActual - 1);
            });
        },

        /**
         * Vincular clicks en el progreso (pasos completados)
         */
        bindProgreso: function() {
            var self = this;

            $(document).on('click', this.progressSelector + '.is-completed', function() {
                var stepNum = parseInt($(this).data('step'));
                if (stepNum && stepNum < self.currentStep) {
                    self.irAlPaso(stepNum);
                }
            });
        },

        /**
         * Vincular cupón en columna derecha (AJAX)
         */
        bindCoupon: function() {
            var self = this;

            // Aplicar cupón
            $(document).on('click', '.islabeya-coupon-btn', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var $input = $btn.closest('.islabeya-checkout-coupon').find('.islabeya-coupon-input');
                var $msg = $btn.closest('.islabeya-checkout-coupon').find('.islabeya-checkout-coupon__mensaje');
                var codigo = $input.val().trim();

                if (!codigo) {
                    $msg.text('Introduce un código de cupón.').removeClass('is-success').addClass('is-error');
                    return;
                }

                $btn.prop('disabled', true).text('Aplicando...');

                $.ajax({
                    type: 'POST',
                    url: wc_checkout_params.wc_ajax_url.toString().replace('%%endpoint%%', 'apply_coupon'),
                    data: {
                        coupon_code: codigo
                    },
                    success: function(response) {
                        if (response && response.indexOf('woocommerce-error') === -1) {
                            $msg.text('¡Cupón aplicado!').removeClass('is-error').addClass('is-success');
                            $input.val('');
                        } else {
                            // Extraer mensaje de error
                            var errorMsg = 'El cupón no es válido.';
                            var match = response.match(/<li[^>]*>([\s\S]*?)<\/li>/);
                            if (match) {
                                errorMsg = $(match[0]).text();
                            }
                            $msg.text(errorMsg).removeClass('is-success').addClass('is-error');
                        }
                        // Refrescar todo el checkout
                        $(document.body).trigger('update_checkout');
                    },
                    error: function() {
                        $msg.text('Error de conexión. Intenta de nuevo.').removeClass('is-success').addClass('is-error');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('Aplicar');
                    }
                });
            });

            // Remover cupón
            $(document).on('click', '.islabeya-checkout-coupon__remove', function(e) {
                e.preventDefault();
                var $link = $(this);
                var codigo = $link.data('coupon');
                var $msg = $link.closest('.islabeya-checkout-coupon').find('.islabeya-checkout-coupon__mensaje');

                if (!codigo) return;

                $link.closest('.islabeya-checkout-coupon__item').css('opacity', '0.5');

                $.ajax({
                    type: 'POST',
                    url: wc_checkout_params.wc_ajax_url.toString().replace('%%endpoint%%', 'remove_coupon'),
                    data: {
                        coupon: codigo
                    },
                    success: function() {
                        $msg.text('Cupón eliminado.').removeClass('is-error').addClass('is-success');
                        $(document.body).trigger('update_checkout');
                    },
                    error: function() {
                        $msg.text('Error al eliminar el cupón.').removeClass('is-success').addClass('is-error');
                        $link.closest('.islabeya-checkout-coupon__item').css('opacity', '1');
                    }
                });
            });

            // Enter en input de cupón
            $(document).on('keydown', '.islabeya-coupon-input', function(e) {
                if (e.key === 'Enter' || e.keyCode === 13) {
                    e.preventDefault();
                    $(this).closest('.islabeya-checkout-coupon').find('.islabeya-coupon-btn').trigger('click');
                }
            });
        },

        /**
         * Escuchar eventos de WooCommerce Checkout para re-inicializar
         */
        bindCheckoutEvents: function() {
            var self = this;

            // Después de actualizar fragmentos de checkout
            $(document.body).on('updated_checkout', function() {
                // Re-inicializar la UI de pasos
                self.determinarPasoInicial();
                self.actualizarUI();
                // Re-aplicar estilos a searchable-selects si existen
                if (typeof makeSearchableSelect !== 'undefined') {
                    $('.js-searchable-select').each(function() {
                        if (!$(this).data('searchable-initialized')) {
                            makeSearchableSelect(this);
                        }
                    });
                }
            });

            // Cuando hay errores de checkout, detectar en qué paso están
            $(document.body).on('checkout_error', function() {
                // Buscar errores en los pasos
                var stepConError = 1;
                $(self.stepSelector).each(function() {
                    var $step = $(this);
                    if ($step.find('.woocommerce-error, .woocommerce-invalid').length > 0) {
                        var stepNum = parseInt($step.data('step'));
                        if (stepNum) {
                            stepConError = stepNum;
                            return false;
                        }
                    }
                });

                // Si el paso con error no es el actual, navegar automáticamente
                if (stepConError !== self.currentStep) {
                    self.irAlPaso(stepConError);
                }
            });
        },

        /**
         * Guardar el número de teléfono completo (con prefijo de país) en campos
         * ocultos que se envían junto al formulario de checkout.
         *
         * Se crea/actualiza un input hidden por cada campo de teléfono con la
         * clase `.isla-phone-field-full`. El valor es el número internacional
         * completo que devuelve intl-tel-input (ej: +5351234567).
         */
        guardarTelefonosCompletos: function() {
            var self = this;
            var mapeo = {
                'billing_phone':  'billing_phone_full',
                'shipping_phone': 'shipping_phone_full'
            };

            Object.keys(mapeo).forEach(function(campoId) {
                var $input = $('#' + campoId);
                if (!$input.length) return;

                // Obtener instancia de intl-tel-input
                var iti = $input.data('islabeya-iti') ||
                    (window.intlTelInputGlobals ? window.intlTelInputGlobals.getInstance($input[0]) : null);

                var fullNumber = '';
                if (iti) {
                    fullNumber = iti.getNumber(); // formato E.164 completo (ej: +5351234567)
                } else {
                    // Fallback: tomar el valor visible del input tal cual
                    fullNumber = $input.val() || '';
                }

                if (!fullNumber) return;

                var nombreOculto = mapeo[campoId];
                var $hidden = $('input[name="' + nombreOculto + '"]');
                if (!$hidden.length) {
                    $hidden = $('<input type="hidden" name="' + nombreOculto + '" />');
                    $('form.checkout').append($hidden);
                }
                $hidden.val(fullNumber);
            });
        },

        /**
         * Inicializar intl-tel-input en campos de teléfono del checkout.
         *
         * Comportamientos:
         * - FACTURACIÓN (#billing_phone): el usuario puede seleccionar CUALQUIER país.
         * - ENVÍO (#shipping_phone): solo se permite Cuba (CU) y el selector queda
         *   bloqueado (no se puede abrir ni cambiar).
         */
        initIntlTelInput: function() {
            var self = this;

            var initPhone = function(selector, countrySelector, options) {
                options = options || {};
                var $input = $(selector);
                if ($input.length && !$input.data('iti-initialized') && typeof window.intlTelInput !== 'undefined') {

                    var config = {
                        initialCountry: "auto",
                        separateDialCode: true,
                        preferredCountries: ["cu", "us", "es", "mx", "ar", "co"],
                        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                        geoIpLookup: function(callback) {
                            fetch('https://ipinfo.io/json?token=3d2bc059192e3d')
                                .then(function(resp) { return resp.json(); })
                                .catch(function() { return { country: 'cu' }; })
                                .then(function(resp) { callback(resp.country); });
                        }
                    };

                    // Aplicar restricciones si vienen por opciones (envío: solo Cuba)
                    if (options.onlyCountries) {
                        config.onlyCountries = options.onlyCountries;
                        delete config.preferredCountries; // solo se listan los permitidos
                    }
                    if (options.initialCountry) {
                        config.initialCountry = options.initialCountry;
                    }

window.intlTelInput($input[0], config);
                    $input.data('iti-initialized', true);

                    var iti = $input.data('iti');
                    if (!iti) iti = window.intlTelInputGlobals ? window.intlTelInputGlobals.getInstance($input[0]) : null;
                    if (iti) {
                        $input.data('islabeya-iti', iti);

                        // Si es un teléfono con país bloqueado (envío), impedir abrir el selector
                        if (options.lockCountry) {
                            $input.closest('.iti').find('.iti__country-list').css('pointer-events', 'none');
                            $input.closest('.iti').find('.iti__arrow').css('display', 'none');
                            $input.closest('.iti').find('.iti__country-container, .iti__selected-dial-code').css('pointer-events', 'none');
                            $input.closest('.iti').addClass('iti--locked');
                        }

                        // ============================================
                        // GUARDAR NÚMERO COMPLETO CON PREFIJO DE PAÍS
                        // Con separateDialCode:true, el input solo contiene
                        // el número nacional. Creamos un campo oculto con
                        // el número internacional completo (ej: +5351234567)
                        // para que se guarde en el pedido.
                        // ============================================
                        var fullFieldName = $input.attr('name') + '_full';
                        if (!$('input[name="' + fullFieldName + '"]').length) {
                            var $hidden = $('<input type="hidden" name="' + fullFieldName + '" value="" />');
                            $input.closest('form').append($hidden);
                        }
                        var $hiddenFull = $('input[name="' + fullFieldName + '"]');

                        var syncFullNumber = function() {
                            var itiInstance = $input.data('islabeya-iti') ||
                                (window.intlTelInputGlobals ? window.intlTelInputGlobals.getInstance($input[0]) : null);
                            if (itiInstance) {
                                var fullNumber = itiInstance.getNumber();
                                if (fullNumber) {
                                    $hiddenFull.val(fullNumber);
                                }
                            }
                        };

                        // Sincronizar al escribir, al cambiar país y en blur
                        $input.on('input blur countrychange', syncFullNumber);
                        if (iti.promise) {
                            iti.promise.then(syncFullNumber);
                        }
                        setTimeout(syncFullNumber, 500);
                    }

                    // Sincronizar con el campo de Pais seleccionado
                    var syncFromCountry = function() {
                        var $country = $(countrySelector);
                        if (!$country.length) return;
                        var countryCode = ($country.val() || 'CU').toUpperCase();
                        var itiInstance = $input.data('islabeya-iti') ||
                            (window.intlTelInputGlobals ? window.intlTelInputGlobals.getInstance($input[0]) : null);
                        if (itiInstance && itiInstance.iso2code !== countryCode.toLowerCase()) {
                            itiInstance.setCountry(countryCode.toLowerCase());
                        }
                    };

                    // Escuchar cambios en el Pais del formulario
                    $(document).on('change', countrySelector, function() {
                        syncFromCountry();
                    });

                    // Sincronizar una vez al inicio
                    setTimeout(syncFromCountry, 500);
                }
            };

            // Facturación: país libre (cualquier país seleccionable)
            initPhone('#billing_phone', '#billing_country', {});

            // Envío: SOLO Cuba, selector bloqueado (no se puede abrir)
            initPhone('#shipping_phone', '#shipping_country', {
                onlyCountries: ['cu'],
                initialCountry: 'cu',
                lockCountry: true
            });

            // Re-inicializar tras update_checkout
            $(document.body).on('updated_checkout', function() {
                setTimeout(function() {
                    initPhone('#billing_phone', '#billing_country', {});
                    initPhone('#shipping_phone', '#shipping_country', {
                        onlyCountries: ['cu'],
                        initialCountry: 'cu',
                        lockCountry: true
                    });
                }, 300);
            });
        },
    };

    /**
     * WooCommerce solo usa #shipping_* en update_checkout si
     * #ship-to-different-address input está checked. Sin eso, s_country
     * toma el valor de billing_country y falla la zona de envío.
     */
    function ensureShipToDifferentAddress() {
        // Solo cuando el formulario de envío existe (paso 2 visible / domicilio).
        if (!$('#shipping_country').length && !$('.woocommerce-shipping-fields__field-wrapper').length) {
            return;
        }

        var $form = $('form.checkout');
        if (!$form.length) {
            return;
        }

        var $wrap = $('#ship-to-different-address');
        if (!$wrap.length) {
            $wrap = $('<div id="ship-to-different-address" class="islabeya-ship-to-different" aria-hidden="true"></div>');
            $form.prepend($wrap);
        }

        var $checkbox = $wrap.find('input[name="ship_to_different_address"]');
        if (!$checkbox.length) {
            $checkbox = $('<input type="checkbox" name="ship_to_different_address" value="1" id="ship-to-different-address-checkbox" />');
            $wrap.append($checkbox);
        }

        $checkbox.prop('checked', true).attr('checked', 'checked');
    }

    function forceShippingCountryToCU() {
        ensureShipToDifferentAddress();

        var $shippingCountry = $('#shipping_country');
        if (!$shippingCountry.length) {
            return;
        }

        if ($shippingCountry.val() !== 'CU') {
            $shippingCountry.val('CU');
        }
    }

    // Inicializar cuando el DOM esté listo
    $(document).ready(function() {
        // Esperar un momento para que WooCommerce cargue sus scripts
        setTimeout(function() {
            CHECKOUT.init();
            forceShippingCountryToCU();
        }, 300);
    });

    $(document.body).on('updated_checkout', function() {
        setTimeout(forceShippingCountryToCU, 200);
    });

    // Antes de cada recálculo AJAX / envío del pedido.
    $(document.body).on('update_checkout checkout_place_order', function() {
        forceShippingCountryToCU();
    });

})(jQuery);
