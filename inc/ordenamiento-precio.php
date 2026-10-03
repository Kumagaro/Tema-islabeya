<?php

/**
 * Snippet Optimizado: Badge Toggle de Precio para YITH WooCommerce
 * Adaptado específicamente para reemplazar el filtro #filter_361_4 (Ordenar por)
 */

function custom_price_toggle_badge_shortcode() {
    // Determinar estado inicial basado en URL
    $current_orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : '';
    $is_price_asc = ($current_orderby === 'price');
    $is_price_desc = ($current_orderby === 'price-desc');
    $is_active = $is_price_asc || $is_price_desc;
    
    ob_start();
    ?>
    <div class="custom-price-toggle-badge" data-yith-integrated="true">
        <div class="price-toggle-container">
            <button class="price-toggle-badge <?php echo $is_active ? 'active' : ''; ?> has-icon" 
                    data-state="<?php echo $is_price_asc ? 'asc' : ($is_price_desc ? 'desc' : 'neutral'); ?>"
                    role="button"
                    aria-label="Ordenar por precio"
                    aria-pressed="<?php echo $is_active ? 'true' : 'false'; ?>"
                    tabindex="0">
                
                <span class="badge-icon">
                    <img src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/icons/Transfer-Vertical.svg' ) ); ?>" 
                         class="icon-normal" alt="" width="20" height="20" loading="lazy">
                    
                    <img src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/icons/Transfer-Vertical-1.svg' ) ); ?>" 
                         class="icon-hover" alt="" width="20" height="20" loading="lazy">
                    
                    <img src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/icons/Transfer-Vertical-1-1.svg' ) ); ?>" 
                         class="icon-active-asc" alt="" width="20" height="20" loading="lazy"
                         style="<?php echo $is_price_asc ? '' : 'display: none;' ?>">
                    
                    <img src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/icons/Transfer-Vertical-2.svg' ) ); ?>" 
                         class="icon-active-desc" alt="" width="20" height="20" loading="lazy"
                         style="<?php echo $is_price_desc ? '' : 'display: none;' ?>">
                </span>
                
                <span class="badge-label">Precio</span>
            </button>
        </div>
    </div>
    
    <style id="price-toggle-badge-styles">
        .custom-price-toggle-badge {
            font-family: inherit;
            display: block;
        }
        
        .badge-label {
            text-transform: none !important;
        }
        
        .price-toggle-container {
            display: flex;
            align-items: center;
        }
        
        .price-toggle-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background-color: transparent !important;
            border: 1px solid #e0e0e0;
            cursor: pointer;
            transition: all 0.25s ease;
            font-size: 13px !important;
            font-weight: 500 !important;
            color: #333 !important;
            text-decoration: none;
            white-space: nowrap;
            line-height: 1.2 !important;
            min-height: 38px;
            padding: 6px 16px;
            box-sizing: border-box;
            outline: none;
            position: relative;
            user-select: none;
            border-radius: 6px;
            background: white !important;
        }
        
        .price-toggle-badge:hover {
            color: #15ad3c !important;
            border-color: #15ad3c !important;
            background: #f9fff9 !important;
        }
        
        .price-toggle-badge:focus-visible {
            outline: 2px solid #15ad3c;
            outline-offset: 2px;
        }
        
        .price-toggle-badge.active {
            color: #15ad3c !important;
            border-color: #15ad3c !important;
            background: #f0f9f0 !important;
        }
        
        .badge-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            position: relative;
            flex-shrink: 0;
        }
        
        .badge-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: opacity 0.25s ease;
            display: block;
            position: absolute;
            top: 0;
            left: 0;
        }
        
        .icon-normal {
            opacity: 1;
            position: relative !important;
        }
        
        .icon-hover {
            opacity: 0;
        }
        
        .icon-active-asc,
        .icon-active-desc {
            opacity: 0;
        }
        
        .price-toggle-badge:hover .icon-normal {
            opacity: 0;
        }
        
        .price-toggle-badge:hover .icon-hover {
            opacity: 1;
        }
        
        .price-toggle-badge.active:hover .icon-hover {
            opacity: 0;
        }
        
        .price-toggle-badge.active .icon-normal {
            opacity: 0;
        }
        
        .price-toggle-badge.active[data-state="asc"] .icon-active-asc {
            opacity: 1;
        }
        
        .price-toggle-badge.active[data-state="desc"] .icon-active-desc {
            opacity: 1;
        }
        
        .badge-label {
            display: inline-block;
            line-height: 1;
        }
        
        /* Ocultar el filtro original de ordenamiento */
        #filter_361_4,
        .yith-wcan-filter.filter-orderby {
            display: none !important;
        }
        
        @media (max-width: 768px) {
            .price-toggle-badge {
                padding: 5px 12px;
                font-size: 12px;
                min-height: 34px;
            }
        } 
    </style>
    
    <script id="price-toggle-badge-script">
    (function() {
        'use strict';
        
        let isApplying = false;
        let clickSequence = 0;
        
        function initPriceToggle() {
            const toggleContainer = document.querySelector('.custom-price-toggle-badge');
            if (!toggleContainer || toggleContainer.hasAttribute('data-initialized')) return;
            
            toggleContainer.setAttribute('data-initialized', 'true');
            
            initClickSequence();
            setupToggleEvents();
            setupResetObservers();
        }
        
        function initClickSequence() {
            const urlParams = new URLSearchParams(window.location.search);
            const currentOrderby = urlParams.get('orderby');
            
            if (currentOrderby === 'price') {
                clickSequence = 1;
            } else if (currentOrderby === 'price-desc') {
                clickSequence = 2;
            } else {
                clickSequence = 0;
            }
        }
        
        function setupToggleEvents() {
            const toggleBtn = document.querySelector('.price-toggle-badge');
            if (!toggleBtn) return;
            
            const newBtn = toggleBtn.cloneNode(true);
            toggleBtn.parentNode.replaceChild(newBtn, toggleBtn);
            
            newBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (!isApplying) handleToggleClick(this);
            });
            
            newBtn.addEventListener('keydown', function(e) {
                if ((e.key === 'Enter' || e.key === ' ') && !isApplying) {
                    e.preventDefault();
                    handleToggleClick(this);
                }
            });
        }
        
        function handleToggleClick(button) {
            clickSequence++;
            
            let orderbyValue, stateValue, activeIconClass;
            
            if (clickSequence % 2 === 1) {
                orderbyValue = 'price';
                stateValue = 'asc';
                activeIconClass = 'icon-active-asc';
            } else {
                orderbyValue = 'price-desc';
                stateValue = 'desc';
                activeIconClass = 'icon-active-desc';
            }
            
            isApplying = true;
            
            updateToggleUI(button, stateValue, activeIconClass);
            applyPriceFilter(orderbyValue);
            
            setTimeout(() => {
                isApplying = false;
            }, 500);
        }
        
        function updateToggleUI(button, stateValue, activeIconClass) {
            button.classList.add('active');
            button.setAttribute('data-state', stateValue);
            button.setAttribute('aria-pressed', 'true');
            
            const normalIcon = button.querySelector('.icon-normal');
            const hoverIcon = button.querySelector('.icon-hover');
            const activeAscIcon = button.querySelector('.icon-active-asc');
            const activeDescIcon = button.querySelector('.icon-active-desc');
            
            if (normalIcon) normalIcon.style.display = 'none';
            if (hoverIcon) hoverIcon.style.display = 'none';
            if (activeAscIcon) activeAscIcon.style.display = 'block';
            if (activeDescIcon) activeDescIcon.style.display = 'block';
        }
        
        function applyPriceFilter(orderbyValue) {
            // 1. Actualizar el select original de YITH
            updateYithSelect(orderbyValue);
            
            // 2. Actualizar el dropdown visual de YITH
            updateYithDropdown(orderbyValue);
            
            // 3. Disparar el botón aplicar
            triggerYithApplyButton();
            
            // 4. Actualizar URL
            updateBrowserUrl(orderbyValue);
            
            // 5. Guardar estado
            sessionStorage.setItem('priceToggleState', orderbyValue);
            sessionStorage.setItem('priceToggleSequence', clickSequence.toString());
        }
        
        function updateYithSelect(orderbyValue) {
            const select = document.querySelector('#filter_361_4 select');
            
            if (select) {
                select.value = orderbyValue;
                select.dispatchEvent(new Event('change', { bubbles: true }));
                return true;
            }
            
            return false;
        }
        
        function updateYithDropdown(orderbyValue) {
            const dropdown = document.querySelector('#filter_361_4 .yith-wcan-dropdown');
            if (!dropdown) return;
            
            const dropdownLabel = dropdown.querySelector('.dropdown-label');
            if (!dropdownLabel) return;
            
            // Buscar el texto de la opción seleccionada
            const select = document.querySelector('#filter_361_4 select');
            if (select) {
                const option = select.querySelector(`option[value="${orderbyValue}"]`);
                if (option) {
                    dropdownLabel.textContent = option.textContent;
                }
            }
            
            // Cerrar el dropdown si está abierto
            if (dropdown.classList.contains('opened')) {
                dropdown.classList.remove('opened');
            }
        }
        
        function triggerYithApplyButton() {
            // Buscar el botón aplicar en tu estructura
            const applyButton = document.querySelector('.yith-wcan-filters .apply-filters, .btn.btn-primary.apply-filters');
            
            if (applyButton) {
                applyButton.click();
                return true;
            }
            
            // Si no hay botón, intentar con el formulario
            const form = document.querySelector('.yith-wcan-filters form, .filters-container form');
            if (form) {
                form.submit();
                return true;
            }
            
            return false;
        }
        
        function updateBrowserUrl(orderbyValue) {
            try {
                const url = new URL(window.location.href);
                const params = url.searchParams;
                
                params.set('orderby', orderbyValue);
                
                if (!params.has('yith_wcan')) {
                    params.set('yith_wcan', '1');
                }
                
                // Limpiar paginación
                for (const key of params.keys()) {
                    if (key.startsWith('e-page-')) params.delete(key);
                }
                params.set('paged', '1');
                
                history.pushState({ orderby: orderbyValue }, '', url.toString());
            } catch (error) {}
        }
        
        function setupResetObservers() {
            // Botón "Borrar Filtros"
            const resetBtn = document.querySelector('#borrar, .yith-wcan-reset-filters');
            if (resetBtn) {
                resetBtn.addEventListener('click', function() {
                    setTimeout(() => {
                        clickSequence = 0;
                        const toggleBtn = document.querySelector('.price-toggle-badge');
                        if (toggleBtn) {
                            toggleBtn.classList.remove('active');
                            toggleBtn.setAttribute('data-state', 'neutral');
                            toggleBtn.setAttribute('aria-pressed', 'false');
                            
                            const normalIcon = toggleBtn.querySelector('.icon-normal');
                            const activeAscIcon = toggleBtn.querySelector('.icon-active-asc');
                            const activeDescIcon = toggleBtn.querySelector('.icon-active-desc');
                            
                            if (normalIcon) normalIcon.style.display = 'block';
                            if (activeAscIcon) activeAscIcon.style.display = 'none';
                            if (activeDescIcon) activeDescIcon.style.display = 'none';
                            
                            sessionStorage.removeItem('priceToggleState');
                            sessionStorage.removeItem('priceToggleSequence');
                        }
                    }, 100);
                });
            }
            
            // Restaurar estado desde sessionStorage
            const savedState = sessionStorage.getItem('priceToggleState');
            const savedSequence = sessionStorage.getItem('priceToggleSequence');
            
            if (savedState && savedSequence) {
                clickSequence = parseInt(savedSequence);
                
                const toggleBtn = document.querySelector('.price-toggle-badge');
                if (toggleBtn && !toggleBtn.classList.contains('active')) {
                    const stateValue = savedState === 'price' ? 'asc' : 'desc';
                    const activeIconClass = savedState === 'price' ? 'icon-active-asc' : 'icon-active-desc';
                    
                    toggleBtn.classList.add('active');
                    toggleBtn.setAttribute('data-state', stateValue);
                    toggleBtn.setAttribute('aria-pressed', 'true');
                    
                    const normalIcon = toggleBtn.querySelector('.icon-normal');
                    const activeIcon = toggleBtn.querySelector(`.${activeIconClass}`);
                    
                    if (normalIcon) normalIcon.style.display = 'none';
                    if (activeIcon) activeIcon.style.display = 'block';
                }
            }
        }
        
        // Inicialización
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(initPriceToggle, 500);
            });
        } else {
            setTimeout(initPriceToggle, 500);
        }
        
        // Observador para YITH dinámico
        if (typeof MutationObserver !== 'undefined') {
            const yithObserver = new MutationObserver(() => {
                if (document.querySelector('#filter_361_4') && 
                    !document.querySelector('.custom-price-toggle-badge[data-initialized]')) {
                    setTimeout(initPriceToggle, 300);
                }
            });
            
            yithObserver.observe(document.body, { childList: true, subtree: true });
        }
        
    })();
    </script>
    
    <?php
    return ob_get_clean();
}
add_shortcode('price_toggle_badge', 'custom_price_toggle_badge_shortcode');

// Remover shortcode anterior si existe
if (shortcode_exists('orderby_badges')) {
    remove_shortcode('orderby_badges');
}