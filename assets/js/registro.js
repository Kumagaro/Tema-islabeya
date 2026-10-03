/**
 * Registro de Clientes IslaBeya
 * Validación y funcionalidad del formulario de registro
 */

(function() {
    'use strict';
    
    const form = document.getElementById('registro-form');
    const phoneInput = document.getElementById('shop-phone');
    const roleRadios = document.querySelectorAll('input[name="role"]');
    const sellerFields = document.querySelector('.show_if_seller');
    
    let iti = null;
    
    // Inicializar intl-tel-input si existe el campo de teléfono
    if (phoneInput && typeof window.intlTelInput !== 'undefined') {
        iti = window.intlTelInput(phoneInput, {
            initialCountry: "auto",
            separateDialCode: true,
            preferredCountries: ["cu", "us", "es", "mx", "ar", "co"],
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
            geoIpLookup: function(callback) {
                fetch('https://ipinfo.io/json?token=3d2bc059192e3d')
                    .then(function(resp) { return resp.json(); })
                    .catch(function() { return { country: 'us' }; })
                    .then(function(resp) { callback(resp.country); });
            }
        });
    }
    
    // Mostrar/ocultar campos de vendedor según rol seleccionado
    function toggleSellerFields() {
        if (!roleRadios.length) return;
        
        const selectedRadio = document.querySelector('input[name="role"]:checked');
        if (!selectedRadio) return;
        
        const isSeller = selectedRadio.value === 'seller';
        
        if (sellerFields) {
            sellerFields.style.display = isSeller ? 'flex' : 'none';
        }
        
        // Actualizar required de los campos de vendedor
        if (sellerFields) {
            const sellerInputs = sellerFields.querySelectorAll('input');
            sellerInputs.forEach(function(input) {
                input.required = isSeller;
                if (!isSeller) {
                    input.value = '';
                }
            });
        }
    }
    
    // Validar teléfono
    function validatePhone() {
        if (!phoneInput || !iti) return true;
        if (phoneInput.value.trim() === '') return true;
        
        const errorDiv = document.getElementById('phone-error');
        
        if (!iti.isValidNumber()) {
            if (errorDiv) {
                errorDiv.style.display = 'block';
                errorDiv.textContent = 'Por favor ingresa un número de teléfono válido para el país seleccionado.';
            }
            phoneInput.style.borderColor = '#e74c3c';
            return false;
        } else {
            if (errorDiv) errorDiv.style.display = 'none';
            phoneInput.style.borderColor = '';
            
            // Guardar el número completo en un campo oculto
            const fullNumber = iti.getNumber();
            let hiddenField = document.querySelector('input[name="phone_full"]');
            if (!hiddenField) {
                hiddenField = document.createElement('input');
                hiddenField.type = 'hidden';
                hiddenField.name = 'phone_full';
                form.appendChild(hiddenField);
            }
            hiddenField.value = fullNumber;
            return true;
        }
    }
    
    // Validar formulario antes de enviar
    function validateForm(e) {
        let isValid = true;
        
        const selectedRadio = document.querySelector('input[name="role"]:checked');
        const isSeller = selectedRadio ? selectedRadio.value === 'seller' : false;
        
        // Validar teléfono solo si es vendedor
        if (isSeller && phoneInput) {
            if (!phoneInput.value.trim() || !validatePhone()) {
                isValid = false;
            }
        }
        
        if (!isValid) {
            e.preventDefault();
            
            let errorDiv = document.querySelector('.woocommerce-error');
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'woocommerce-error';
                errorDiv.innerHTML = '<ul><li>Por favor corrige los errores antes de enviar el formulario.</li></ul>';
                const container = document.querySelector('.islabeya-registro-container');
                const formElement = document.querySelector('.register');
                if (container && formElement) {
                    container.insertBefore(errorDiv, formElement);
                }
            }
        }
    }
    
    // Limpiar errores al escribir en el teléfono
    function setupPhoneEvents() {
        if (!phoneInput) return;
        
        phoneInput.addEventListener('blur', validatePhone);
        phoneInput.addEventListener('input', function() {
            const errorDiv = document.getElementById('phone-error');
            if (errorDiv) errorDiv.style.display = 'none';
            phoneInput.style.borderColor = '';
            
            const generalError = document.querySelector('.woocommerce-error');
            if (generalError) generalError.remove();
        });
    }
    
    // Inicializar eventos del formulario
    function init() {
        toggleSellerFields();
        setupPhoneEvents();
        
        if (roleRadios.length) {
            roleRadios.forEach(function(radio) {
                radio.addEventListener('change', toggleSellerFields);
            });
        }
        
        if (form) {
            form.addEventListener('submit', validateForm);
        }
    }
    
    // Ejecutar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();