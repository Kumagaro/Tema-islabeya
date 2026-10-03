jQuery(document).ready(function($) {
    
    // Función para crear botones
    function crearBotonesSelector() {
        // Buscar todos los divs con clase .quantity
        $('.quantity').each(function() {
            var $quantity = $(this);
            
// Verificar si ya tiene botones
            if ($quantity.find('.qty-button').length > 0) {
                return;
            }
            
            // Buscar el input dentro del quantity
            var $input = $quantity.find('.qty, input[type="number"]').first();
            
            if ($input.length === 0) {
                return;
            }
            
            // Guardar atributos del input
            var inputId = $input.attr('id');
            var inputName = $input.attr('name');
            var inputValue = $input.val();
            var inputMin = $input.attr('min') || 1;
            var inputMax = $input.attr('max') || 9999;
            var inputStep = $input.attr('step') || 1;
            
            // Crear botón menos
            var $minusBtn = $('<button type="button" class="qty-button minus-button">-</button>');
            
            // Crear botón más
            var $plusBtn = $('<button type="button" class="qty-button plus-button">+</button>');
            
            // Estilos para los botones
            $minusBtn.css({
                'width': '35px',
                'height': '40px',
                'background': '#f5f5f5',
                'border': 'none',
                'border-radius': '4px 0 0 4px',
                'cursor': 'pointer',
                'font-size': '18px',
                'font-weight': 'bold',
                'color': '#333',
                'display': 'inline-block',
                'text-align': 'center',
                'line-height': '40px'
            });
            
            $plusBtn.css({
                'width': '35px',
                'height': '40px',
                'background': '#f5f5f5',
                'border': 'none',
                'border-radius': '0 4px 4px 0',
                'cursor': 'pointer',
                'font-size': '18px',
                'font-weight': 'bold',
                'color': '#333',
                'display': 'inline-block',
                'text-align': 'center',
                'line-height': '40px'
            });
            
            // Estilos para el input
            $input.css({
                'width': '30px',
                'height': '40px',
                'text-align': 'center',
                'border': 'none',
                'margin': '0',
                'padding': '0',
                'font-size': '16px',
                'float': 'none',
                'display': 'inline-block',
                'border-radius': '0',
                '-moz-appearance': 'textfield',
                'background': '#f5f5f5',
            });
            
            // Ocultar flechas del input
            $input.attr('type', 'text');
            $input.css('-webkit-appearance', 'none');
            
            // Limpiar el contenedor y agregar los elementos
            $quantity.empty();
            $quantity.append($minusBtn);
            $quantity.append($input);
            $quantity.append($plusBtn);
            
            // Estilos para el contenedor
            $quantity.css({
                'display': 'inline-flex',
                'align-items': 'center',
                'justify-content': 'center',
                'white-space': 'nowrap'
            });
            
            // Función para actualizar estado de botones
            function actualizarBotones() {
                var currentVal = parseInt($input.val()) || inputMin;
                if (currentVal <= inputMin) {
                    $minusBtn.addClass('disabled').prop('disabled', true);
                } else {
                    $minusBtn.removeClass('disabled').prop('disabled', false);
                }
                if (currentVal >= inputMax) {
                    $plusBtn.addClass('disabled').prop('disabled', true);
                } else {
                    $plusBtn.removeClass('disabled').prop('disabled', false);
                }
            }
            
            // Evento botón menos
            $minusBtn.on('click', function(e) {
                e.preventDefault();
                var currentVal = parseInt($input.val()) || inputMin;
                if (currentVal > inputMin) {
                    var newVal = currentVal - parseInt(inputStep);
                    if (newVal >= inputMin) {
                        $input.val(newVal).trigger('change');
                    }
                }
                actualizarBotones();
            });
            
            // Evento botón más
            $plusBtn.on('click', function(e) {
                e.preventDefault();
                var currentVal = parseInt($input.val()) || inputMin;
                if (currentVal < inputMax) {
                    var newVal = currentVal + parseInt(inputStep);
                    if (newVal <= inputMax) {
                        $input.val(newVal).trigger('change');
                    }
                }
                actualizarBotones();
            });
            
            // Evento cambio manual
            $input.on('change keyup', function() {
                var val = parseInt($(this).val()) || inputMin;
                if (val < inputMin) val = inputMin;
                if (val > inputMax) val = inputMax;
                $(this).val(val);
                actualizarBotones();
            });
            
            // Estado inicial
            actualizarBotones();
        });
    }
    
    // Ejecutar al cargar
    crearBotonesSelector();
    
    // También ejecutar después de un pequeño retraso (para asegurar)
    setTimeout(crearBotonesSelector, 500);
    
    // Para productos variables (cuando cambia la variación)
    $(document).on('found_variation', function() {
        setTimeout(crearBotonesSelector, 200);
    });
    
});