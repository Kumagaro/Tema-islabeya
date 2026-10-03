jQuery(function($) {
    const modal = $("#ubicacion-selector-modal");
    const provinceSelect = $("#ubicacion-province-select");
    const muniSelect = $("#ubicacion-municipality-select");
    const allMunicipalities = modal.data("all-municipalities");
    const config = window.ubicacionConfig;
    const modalClosedKey = "ubicacion_modal_closed_" + config.current_province;

    // Función para obtener el wrapper personalizado del select
    function getWrapper(selectElement) {
        return selectElement.next('.searchable-select-wrapper');
    }

    // Función para obtener el label asociado al select
    function getLabel(selectElement) {
        return selectElement.closest('.ubicacion-form-group').find('.ubicacion-label');
    }

    // Limpiar errores
    function clearErrors() {
        getWrapper(provinceSelect).find('.searchable-select-trigger').removeClass('error');
        getWrapper(muniSelect).find('.searchable-select-trigger').removeClass('error');
        getLabel(provinceSelect).removeClass('error');
        getLabel(muniSelect).removeClass('error');
        $("#ubicacion-message").removeClass('error').hide();
    }

    // Mostrar error en un campo
    function showError(selectElement) {
        const wrapper = getWrapper(selectElement);
        const label = getLabel(selectElement);
        if (wrapper.length) wrapper.find('.searchable-select-trigger').addClass('error');
        if (label.length) label.addClass('error');
    }

    // Actualizar municipios
    function updateMuniOptions(provinceId, selectedMuni = "") {
        let options = '<option value="">' + config.i18n.select_muni + '</option>';
        const filtered = allMunicipalities.filter(m => m.parent == provinceId);
        filtered.forEach(m => {
            const isSelected = m.id == selectedMuni ? "selected" : "";
            options += `<option value="${m.id}" ${isSelected}>${m.name}</option>`;
        });
        muniSelect.html(options).prop("disabled", false);
        if (muniSelect[0] && muniSelect[0].refreshSearchableSelect) {
            muniSelect[0].refreshSearchableSelect();
        }
        // Limpiar error del municipio
        getWrapper(muniSelect).find('.searchable-select-trigger').removeClass('error');
        getLabel(muniSelect).removeClass('error');
    }

    // Inicializar si hay provincia seleccionada
    if (config.current_province) {
        updateMuniOptions(config.current_province, config.current_municipality);
    }

    // Abrir modal
    $("#btn-open-location-modal").on("click", function(e) {
        e.preventDefault();
        modal.fadeIn(200).css("display", "flex");
        $("body").css("overflow", "hidden");
        clearErrors();
    });

    function closeModal() {
        modal.fadeOut(200);
        $("body").css("overflow", "");
        if (config.is_shop_page && !config.current_province) {
            localStorage.setItem(modalClosedKey, "true");
        }
    }

    $("#btn-close-ubicacion-modal, #ubicacion-selector-modal").on("click", function(e) {
        if (e.target === this) closeModal();
    });

    // Cambio de provincia
    provinceSelect.on("change", function() {
        const val = $(this).val();
        if (val) {
            updateMuniOptions(val);
            getWrapper(provinceSelect).find('.searchable-select-trigger').removeClass('error');
            getLabel(provinceSelect).removeClass('error');
        } else {
            muniSelect.html('<option value="">' + config.i18n.select_muni + '</option>').prop("disabled", true);
            if (muniSelect[0] && muniSelect[0].refreshSearchableSelect) {
                muniSelect[0].refreshSearchableSelect();
            }
            getWrapper(muniSelect).find('.searchable-select-trigger').removeClass('error');
            getLabel(muniSelect).removeClass('error');
        }
    });

    // Cambio de municipio: limpiar error
    muniSelect.on("change", function() {
        getWrapper(muniSelect).find('.searchable-select-trigger').removeClass('error');
        getLabel(muniSelect).removeClass('error');
    });

    // Validación y envío
    $("#ubicacion-selector-form").on("submit", function(e) {
        e.preventDefault();
        clearErrors();

        const provinceVal = provinceSelect.val();
        const muniVal = muniSelect.val();
        let hasError = false;

        if (!provinceVal) {
            showError(provinceSelect);
            hasError = true;
        }
        if (!muniVal && provinceVal) {
            showError(muniSelect);
            hasError = true;
        }

        if (hasError) {
            $("#ubicacion-message").text("Por favor selecciona provincia y municipio").addClass("error").show();
            return;
        }

        const btn = $("#btn-confirm-ubicacion");
        btn.prop("disabled", true).find(".btn-text").hide();
        btn.find(".btn-loader").show();

        $.post(config.ajax_url, {
            action: "ubicacion_store_location",
            form_data: $(this).serialize(),
            nonce: config.nonce
        }, function(res) {
            if (res.success) {
                $("#btn-open-location-modal .ubicacion-text").text(res.data.new_text);
                $("#ubicacion-message").text(config.i18n.success).addClass("success").removeClass("error").fadeIn();
                localStorage.removeItem(modalClosedKey);
                setTimeout(() => {
                    closeModal();
                    btn.prop("disabled", false).find(".btn-text").show();
                    btn.find(".btn-loader").hide();
                    $("#ubicacion-message").hide();
                }, 1000);
            } else {
                $("#ubicacion-message").text(config.i18n.error).addClass("error").removeClass("success").fadeIn();
                setTimeout(() => {
                    btn.prop("disabled", false).find(".btn-text").show();
                    btn.find(".btn-loader").hide();
                    $("#ubicacion-message").fadeOut();
                }, 2000);
            }
        });
    });

    // Mostrar modal automáticamente en tienda si no hay ubicación
    if (config.is_shop_page && !config.current_province && !localStorage.getItem(modalClosedKey)) {
        setTimeout(function() {
            if (modal.length) {
                modal.fadeIn(200).css("display", "flex");
                $("body").css("overflow", "hidden");
            }
        }, 500);
    }
});