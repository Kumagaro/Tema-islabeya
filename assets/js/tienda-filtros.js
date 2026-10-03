// Función para hacer elementos clickeables
function makeClickable(selector, targetSelector, eventType = 'click') {
    const element = document.querySelector(selector);
    if (element) {
        element.style.cursor = 'pointer';
        element.addEventListener('click', () => {
            const target = document.querySelector(targetSelector);
            if (target) target[eventType]();
        });
    }
}

// Estado para controlar si estamos aplicando filtros
let isApplyingFilters = false;

// Interceptar el botón nativo "Aplicar filtros" de YITH
function interceptYithApplyButton() {
    const applyButton = document.querySelector('.yith-wcan-filters .apply-filters');
    
    if (applyButton && !applyButton.hasAttribute('data-intercepted')) {
        applyButton.setAttribute('data-intercepted', 'true');
        
        applyButton.addEventListener('click', function(e) {
            isApplyingFilters = true;
            
            setTimeout(() => {
                if (isApplyingFilters && window.location.search.includes('e-page-')) {
                    cleanYithPagination();
                    isApplyingFilters = false;
                }
            }, 300);
        }, true);
    }
}

// Interceptar enlaces de filtros (para evitar aplicación automática)
function interceptFilterLinks() {
    document.querySelectorAll('.yith-wcan-filter a[href*="yith_wcan"]').forEach(link => {
        if (link.hasAttribute('data-filter-intercepted')) return;
        
        link.setAttribute('data-filter-intercepted', 'true');
        
        link.addEventListener('click', function(e) {
            if (!this.classList.contains('apply-filters') && 
                !this.classList.contains('yith-wcan-reset-filters')) {
                
                e.preventDefault();
                e.stopPropagation();
                
                const input = this.closest('label')?.querySelector('input[type="checkbox"], input[type="radio"]');
                if (input) {
                    if (input.type === 'checkbox') {
                        input.checked = !input.checked;
                    } else if (input.type === 'radio') {
                        input.checked = true;
                    }
                    
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
        });
    });
}

// Limpiar paginación solo cuando se aplican filtros
function cleanYithPagination() {
    if (!isApplyingFilters) return;
    
    const url = new URL(window.location.href);
    const params = new URLSearchParams(url.search);
    let cleaned = false;
    
    for (const key of params.keys()) {
        if (key.startsWith('e-page-')) {
            params.delete(key);
            cleaned = true;
        }
    }
    
    if (cleaned) {
        params.set('paged', '1');
        url.search = params.toString();
        
        if (url.toString() !== window.location.href) {
            window.location.href = url.toString();
        }
    }
}

// Configurar checkboxes para selección manual
function setupManualCheckboxes() {
    document.querySelectorAll('.yith-wcan-filter input[type="checkbox"], .yith-wcan-filter input[type="radio"]').forEach(input => {
        if (input.hasAttribute('data-manual-setup')) return;
        input.setAttribute('data-manual-setup', 'true');
    });
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Ajusta estos selectores según los IDs de tus elementos
    makeClickable('#borrar', '.yith-wcan-reset-filters');
    makeClickable('#aplicar', '.yith-wcan-filters .apply-filters');
    
    setupManualCheckboxes();
    interceptFilterLinks();
    interceptYithApplyButton();
});

// Reconectar si YITH carga contenido dinámico
if (typeof MutationObserver !== 'undefined') {
    const observer = new MutationObserver(() => {
        if (document.querySelector('.yith-wcan-filters')) {
            setTimeout(() => {
                setupManualCheckboxes();
                interceptFilterLinks();
                interceptYithApplyButton();
            }, 300);
        }
    });
    
    observer.observe(document.body, { childList: true, subtree: true });
}