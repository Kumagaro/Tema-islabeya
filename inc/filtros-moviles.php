<?php
/**
 * Shortcode HIPER-OPTIMIZADO para filtros móviles (Adaptado para draft-preset)
 */
function filtro_movil_hiper_optimizado($atts) {
    $atts = shortcode_atts(array('clase' => ''), $atts, 'filtro_movil');
    $popup_id = 'filtro-' . rand(1000, 9999);
    
    return '
    <button class="fmo-btn ' . esc_attr($atts['clase']) . '" data-fmo="' . esc_attr($popup_id) . '">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M4.95301 2.25C4.96862 2.25 4.98429 2.25 5.00001 2.25L19.047 2.25C19.7139 2.24997 20.2841 2.24994 20.7398 2.30742C21.2231 2.36839 21.6902 2.50529 22.0738 2.86524C22.4643 3.23154 22.6194 3.68856 22.6875 4.16405C22.7501 4.60084 22.7501 5.14397 22.75 5.76358L22.75 6.54012C22.75 7.02863 22.75 7.45095 22.7136 7.80311C22.6743 8.18206 22.5885 8.5376 22.3825 8.87893C22.1781 9.2177 21.9028 9.4636 21.5854 9.68404C21.2865 9.8917 20.9045 10.1067 20.4553 10.3596L17.5129 12.0159C16.8431 12.393 16.6099 12.5288 16.4542 12.6639C16.0966 12.9744 15.8918 13.3188 15.7956 13.7504C15.7545 13.9349 15.75 14.1672 15.75 14.8729L15.75 17.605C15.7501 18.5062 15.7501 19.2714 15.6574 19.8596C15.5587 20.4851 15.3298 21.0849 14.7298 21.4602C14.1434 21.827 13.4975 21.7933 12.8698 21.6442C12.2653 21.5007 11.5203 21.2094 10.6264 20.8599L10.5395 20.826C10.1208 20.6623 9.75411 20.519 9.46385 20.3691C9.1519 20.208 8.8622 20.0076 8.64055 19.6957C8.41641 19.3803 8.32655 19.042 8.28648 18.6963C8.24994 18.381 8.24997 18.0026 8.25 17.5806L8.25 14.8729C8.25 14.1672 8.24555 13.9349 8.20442 13.7504C8.1082 13.3188 7.90342 12.9744 7.54584 12.6639C7.39014 12.5288 7.15692 12.393 6.48714 12.0159L3.54471 10.3596C3.09549 10.1067 2.71353 9.8917 2.41458 9.68404C2.09724 9.4636 1.82191 9.2177 1.61747 8.87893C1.41148 8.5376 1.32571 8.18206 1.28645 7.80311C1.24996 7.45094 1.24998 7.02863 1.25 6.54012L1.25001 5.81466C1.25001 5.79757 1.25 5.78054 1.25 5.76357C1.24996 5.14396 1.24991 4.60084 1.31251 4.16405C1.38064 3.68856 1.53576 3.23154 1.92618 2.86524C2.30983 2.50529 2.77695 2.36839 3.26024 2.30742C3.71592 2.24994 4.28607 2.24997 4.95301 2.25ZM3.44796 3.79563C3.1143 3.83772 3.0082 3.90691 2.95251 3.95916C2.90359 4.00505 2.83904 4.08585 2.79734 4.37683C2.75181 4.69454 2.75001 5.12868 2.75001 5.81466V6.50448C2.75001 7.03869 2.75093 7.38278 2.77846 7.64854C2.8041 7.89605 2.84813 8.01507 2.90174 8.10391C2.9569 8.19532 3.0485 8.298 3.27034 8.45209C3.50406 8.61444 3.82336 8.79508 4.30993 9.06899L7.22296 10.7088C7.25024 10.7242 7.2771 10.7393 7.30357 10.7542C7.86227 11.0685 8.24278 11.2826 8.5292 11.5312C9.12056 12.0446 9.49997 12.6682 9.66847 13.424C9.75036 13.7913 9.75022 14.2031 9.75002 14.7845C9.75001 14.8135 9.75 14.843 9.75 14.8729V17.5424C9.75 18.0146 9.75117 18.305 9.77651 18.5236C9.79942 18.7213 9.83552 18.7878 9.8633 18.8269C9.89359 18.8695 9.95357 18.9338 10.152 19.0363C10.3644 19.146 10.6571 19.2614 11.1192 19.442C12.0802 19.8177 12.7266 20.0685 13.2164 20.1848C13.695 20.2985 13.8527 20.2396 13.9343 20.1885C14.0023 20.146 14.1073 20.0597 14.1757 19.626C14.2478 19.1686 14.25 18.5234 14.25 17.5424V14.8729C14.25 14.843 14.25 14.8135 14.25 14.7845C14.2498 14.2031 14.2496 13.7913 14.3315 13.424C14.5 12.6682 14.8794 12.0446 15.4708 11.5312C15.7572 11.2826 16.1377 11.0685 16.6964 10.7542C16.7229 10.7393 16.7498 10.7242 16.7771 10.7088L19.6901 9.06899C20.1767 8.79508 20.496 8.61444 20.7297 8.45209C20.9515 8.298 21.0431 8.19532 21.0983 8.10391C21.1519 8.01507 21.1959 7.89605 21.2215 7.64854C21.2491 7.38278 21.25 7.03869 21.25 6.50448V5.81466C21.25 5.12868 21.2482 4.69454 21.2027 4.37683C21.161 4.08585 21.0964 4.00505 21.0475 3.95916C20.9918 3.90691 20.8857 3.83772 20.5521 3.79563C20.2015 3.75141 19.727 3.75 19 3.75H5.00001C4.27297 3.75 3.79854 3.75141 3.44796 3.79563Z" fill="#15AD3C"/>
        </svg>
        <span class="fmo-count"></span>
    </button>
    
    <div id="' . esc_attr($popup_id) . '" class="fmo-wrapper">
        <div class="fmo-overlay" data-close="' . esc_attr($popup_id) . '"></div>
        <div class="fmo-popup">
            <div class="fmo-drag" data-drag="' . esc_attr($popup_id) . '"></div>
            <!-- CONTENEDOR SUPERIOR - Título -->
            <div class="fmo-top" data-drag="' . esc_attr($popup_id) . '">
                <div class="fmo-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M4.95301 2.25C4.96862 2.25 4.98429 2.25 5.00001 2.25L19.047 2.25C19.7139 2.24997 20.2841 2.24994 20.7398 2.30742C21.2231 2.36839 21.6902 2.50529 22.0738 2.86524C22.4643 3.23154 22.6194 3.68856 22.6875 4.16405C22.7501 4.60084 22.7501 5.14397 22.75 5.76358L22.75 6.54012C22.75 7.02863 22.75 7.45095 22.7136 7.80311C22.6743 8.18206 22.5885 8.5376 22.3825 8.87893C22.1781 9.2177 21.9028 9.4636 21.5854 9.68404C21.2865 9.8917 20.9045 10.1067 20.4553 10.3596L17.5129 12.0159C16.8431 12.393 16.6099 12.5288 16.4542 12.6639C16.0966 12.9744 15.8918 13.3188 15.7956 13.7504C15.7545 13.9349 15.75 14.1672 15.75 14.8729L15.75 17.605C15.7501 18.5062 15.7501 19.2714 15.6574 19.8596C15.5587 20.4851 15.3298 21.0849 14.7298 21.4602C14.1434 21.827 13.4975 21.7933 12.8698 21.6442C12.2653 21.5007 11.5203 21.2094 10.6264 20.8599L10.5395 20.826C10.1208 20.6623 9.75411 20.519 9.46385 20.3691C9.1519 20.208 8.8622 20.0076 8.64055 19.6957C8.41641 19.3803 8.32655 19.042 8.28648 18.6963C8.24994 18.381 8.24997 18.0026 8.25 17.5806L8.25 14.8729C8.25 14.1672 8.24555 13.9349 8.20442 13.7504C8.1082 13.3188 7.90342 12.9744 7.54584 12.6639C7.39014 12.5288 7.15692 12.393 6.48714 12.0159L3.54471 10.3596C3.09549 10.1067 2.71353 9.8917 2.41458 9.68404C2.09724 9.4636 1.82191 9.2177 1.61747 8.87893C1.41148 8.5376 1.32571 8.18206 1.28645 7.80311C1.24996 7.45094 1.24998 7.02863 1.25 6.54012L1.25001 5.81466C1.25001 5.79757 1.25 5.78054 1.25 5.76357C1.24996 5.14396 1.24991 4.60084 1.31251 4.16405C1.38064 3.68856 1.53576 3.23154 1.92618 2.86524C2.30983 2.50529 2.77695 2.36839 3.26024 2.30742C3.71592 2.24994 4.28607 2.24997 4.95301 2.25ZM3.44796 3.79563C3.1143 3.83772 3.0082 3.90691 2.95251 3.95916C2.90359 4.00505 2.83904 4.08585 2.79734 4.37683C2.75181 4.69454 2.75001 5.12868 2.75001 5.81466V6.50448C2.75001 7.03869 2.75093 7.38278 2.77846 7.64854C2.8041 7.89605 2.84813 8.01507 2.90174 8.10391C2.9569 8.19532 3.0485 8.298 3.27034 8.45209C3.50406 8.61444 3.82336 8.79508 4.30993 9.06899L7.22296 10.7088C7.25024 10.7242 7.2771 10.7393 7.30357 10.7542C7.86227 11.0685 8.24278 11.2826 8.5292 11.5312C9.12056 12.0446 9.49997 12.6682 9.66847 13.424C9.75036 13.7913 9.75022 14.2031 9.75002 14.7845C9.75001 14.8135 9.75 14.843 9.75 14.8729V17.5424C9.75 18.0146 9.75117 18.305 9.77651 18.5236C9.79942 18.7213 9.83552 18.7878 9.8633 18.8269C9.89359 18.8695 9.95357 18.9338 10.152 19.0363C10.3644 19.146 10.6571 19.2614 11.1192 19.442C12.0802 19.8177 12.7266 20.0685 13.2164 20.1848C13.695 20.2985 13.8527 20.2396 13.9343 20.1885C14.0023 20.146 14.1073 20.0597 14.1757 19.626C14.2478 19.1686 14.25 18.5234 14.25 17.5424V14.8729C14.25 14.843 14.25 14.8135 14.25 14.7845C14.2498 14.2031 14.2496 13.7913 14.3315 13.424C14.5 12.6682 14.8794 12.0446 15.4708 11.5312C15.7572 11.2826 16.1377 11.0685 16.6964 10.7542C16.7229 10.7393 16.7498 10.7242 16.7771 10.7088L19.6901 9.06899C20.1767 8.79508 20.496 8.61444 20.7297 8.45209C20.9515 8.298 21.0431 8.19532 21.0983 8.10391C21.1519 8.01507 21.1959 7.89605 21.2215 7.64854C21.2491 7.38278 21.25 7.03869 21.25 6.50448V5.81466C21.25 5.12868 21.2482 4.69454 21.2027 4.37683C21.161 4.08585 21.0964 4.00505 21.0475 3.95916C20.9918 3.90691 20.8857 3.83772 20.5521 3.79563C20.2015 3.75141 19.727 3.75 19 3.75H5.00001C4.27297 3.75 3.79854 3.75141 3.44796 3.79563Z" fill="#15AD3C"/>
                    </svg>
                    <h4>Filtros</h4>
                </div>
            </div>
            
            <!-- CONTENEDOR DE BOTONES -->
            <div class="fmo-buttons">
                <a href="' . esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ) . '" class="fmo-clear" id="clear-' . esc_attr($popup_id) . '">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M13.0828 19.0632C12.6389 19.5072 12.2399 19.9062 11.8725 20.25H21C21.4142 20.25 21.75 20.5858 21.75 21C21.75 21.4142 21.4142 21.75 21 21.75H9C8.98166 21.75 8.96347 21.7493 8.94546 21.748C8.24156 21.7211 7.64439 21.4169 7.05863 20.97C6.47124 20.5218 5.81539 19.866 5.01269 19.0632L4.93674 18.9873C4.13402 18.1846 3.47815 17.5288 3.03 16.9414C2.56159 16.3274 2.25 15.701 2.25 14.9522C2.25 14.2035 2.56159 13.577 3.03 12.9631C3.47816 12.3757 4.13402 11.7199 4.93674 10.9172L10.9172 4.93674C11.7199 4.13403 12.3757 3.47815 12.9631 3.03C13.577 2.56159 14.2035 2.25 14.9522 2.25C15.701 2.25 16.3274 2.56159 16.9414 3.03C17.5288 3.47816 18.1846 4.13402 18.9873 4.93674L19.0632 5.01269C19.866 5.81539 20.5218 6.47124 20.97 7.05863C21.4384 7.67256 21.75 8.29902 21.75 9.04776C21.75 9.79649 21.4384 10.423 20.97 11.0369C20.5219 11.6243 19.866 12.2801 19.0633 13.0827L13.0828 19.0632ZM11.9399 6.03539C12.7899 5.18538 13.3752 4.60235 13.873 4.22253C14.3535 3.85592 14.6633 3.75 14.9522 3.75C15.2411 3.75 15.551 3.85592 16.0315 4.22253C16.5293 4.60235 17.1146 5.18538 17.9646 6.03539C18.8146 6.88541 19.3977 7.47069 19.7775 7.9685C20.1441 8.449 20.25 8.75886 20.25 9.04776C20.25 9.33665 20.1441 9.64651 19.7775 10.127C19.3977 10.6248 18.8146 11.2101 17.9646 12.0601L13.7713 16.2534L7.74662 10.2287L11.9399 6.03539ZM9.04776 20.25C9.33665 20.25 9.64651 20.1441 10.127 19.7775C10.6248 19.3977 11.2101 18.8146 12.0601 17.9646L12.7107 17.314L6.68596 11.2893L6.03539 11.9399C5.18538 12.7899 4.60235 13.3752 4.22253 13.873C3.85592 14.3535 3.75 14.6633 3.75 14.9522C3.75 15.2411 3.85592 15.551 4.22253 16.0315C4.60235 16.5293 5.18538 17.1146 6.03539 17.9646C6.88541 18.8146 7.47069 19.3977 7.9685 19.7775C8.449 20.1441 8.75886 20.25 9.04776 20.25Z" fill="black"/>
                    </svg>
                    Borrar filtros
                </a>
            </div>
            <div class="fmo-content">' . do_shortcode('[yith_wcan_filters slug="filtro-de-tienda"]') . '</div>
        </div>
    </div>';
}
add_shortcode('filtro_movil', 'filtro_movil_hiper_optimizado');

/**
 * CSS HIPER-OPTIMIZADO
 */
function fmo_css_hyper() {
    static $done = false;
    if ($done) return;
    $done = true;
    
    echo '<style>
    .fmo-btn,.fmo-count,.fmo-overlay,.fmo-popup,.fmo-drag,.fmo-top,.fmo-title,
    .fmo-clear,.fmo-content {
        box-sizing:border-box
    }
    .fmo-btn {
        display:inline-flex !important;
        position:relative !important;
        padding:8px !important;
        background:#fff !important;
        border:none !important;
        border-radius:4px !important;
        cursor:pointer !important;
        line-height: 1 !important;
        vertical-align:middle !important;
    }
    
    .fmo-count{
        position:absolute !important;
        top:-5px !important;
        right:-5px !important;
        background: #fff !important;
        color: #15ad3c !important;
        font:bold 11px/1 sans-serif !important;
        min-width:18px !important;
        height:18px !important;
        border-radius:9px !important;
        display:flex;
        align-items:center !important;
        justify-content:center !important;
        padding:0 4px !important;
    }
    
    .fmo-top {
        display:flex !important;
        justify-content:center !important;
        align-items:center !important;
        padding:16px 16px 8px 16px !important;
        border:none !important;
        background:#fff !important;
        flex-shrink:0 !important;
        border-bottom:1px solid #e5e7eb !important;
    }
    
    .fmo-title {
        display:flex !important;
        align-items:center !important;
    }
    
    .fmo-title h4 {
        margin:0 0 0 12px !important;
        color: #15ad3c !important;
    }
    
    .fmo-buttons {
        display:flex !important;
        justify-content:space-between !important;
        align-items:center !important;
        padding:12px 16px !important;
        background:#fff !important;
        flex-shrink:0 !important;
        gap: 16px;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .fmo-clear, .fmo-apply {
        display:flex !important;
        align-items:center !important;
        justify-content: center;
        border:none !important;
        cursor:pointer !important;
        padding:8px 16px !important;
        margin:0 !important;
        transition:background-color var(--tiempo-transicion) !important;
        height: 40px !important;
        border-radius: 6px;
        font-size: 14px;
        gap: 8px;
        text-decoration: none;
    }
    
    .fmo-clear {
        background-color: #f5f5f5 !important;
        color: #333 !important;
        flex: 1;
    }
    
    .fmo-apply {
        flex: 1;
    }
    
    .fmo-overlay {
        position:fixed;
        inset:0;
        background:#0008;
        z-index:30000;
        opacity:0;
        visibility:hidden;
        transition:opacity var(--tiempo-transicion);
    }
    .fmo-overlay.active{
        opacity:1;
        visibility:visible;
    }
    .fmo-popup {
        position:fixed;
        bottom:0;
        left:0;
        width:100%;
        height:70vh;
        background:#fff;
        z-index:40000!important;
        border-radius: 20px 20px 0 0;
        transform:translateY(100%);
        transition:transform var(--tiempo-transicion);
        display:flex;
        flex-direction:column;
        overflow:hidden;
    }
    .fmo-popup.active{
        transform:translateY(0);
    }
    .fmo-drag{
        width:40px;
        height:4px;
        background: #e5e7eb;
        border-radius:2px;
        margin:10px auto;
        cursor: grab;
    }
    .fmo-content{
        flex:1;
        overflow-y:auto;
        padding:16px;
    }
    .fmo-content::-webkit-scrollbar{
        width:3px;
    }
    .fmo-content::-webkit-scrollbar-track {
        background:#f1f1f1;
    }
    .fmo-content::-webkit-scrollbar-thumb{
        background: #15ad3c;
        border-radius:3px;
    }
    @media(max-width:480px) {
        .fmo-top{
            padding:10px 12px;
        }
        .fmo-content{
            padding:12px;
        }
        .fmo-clear, .fmo-apply {
            padding: 6px 12px;
            height: 36px;
            font-size: 13px;
        }
    }
    </style>';
}
add_action('wp_head', 'fmo_css_hyper', 0);

/**
 * JavaScript HIPER-OPTIMIZADO (Adaptado para draft-preset)
 */
function fmo_js_hyper() {
    static $done = false;
    if ($done) return;
    $done = true;
    
    echo '<script>
    (function(){
    "use strict";
    
    var fmoDrag={y:0,popup:null,active:false},fmoTimeout;
    
    function fmoUpdateCount(){
        var count = 0;
        var activeFilters = document.querySelectorAll(\'.yith-wcan-filter .filter-item.active, .yith-wcan-filter input:checked, .yith-wcan-filter .active\');
        if(activeFilters.length) {
            count = activeFilters.length;
        }
        var activeFilterElements = document.querySelectorAll(\'.yith-wcan-active-filters .active-filter\');
        if(activeFilterElements.length) {
            count = activeFilterElements.length;
        }
        document.querySelectorAll(".fmo-count").forEach(function(el){
            el.textContent = count > 0 ? count : "";
        });
    }
    
    var fmoObs=new MutationObserver(function(){
        clearTimeout(fmoTimeout);
        fmoTimeout=setTimeout(fmoUpdateCount,50);
    });
    
    if(document.readyState!=="loading"){
        fmoInit();
    }else{
        document.addEventListener("DOMContentLoaded",fmoInit);
    }
    
    function fmoInit(){
        var filterContainers=document.querySelectorAll(".yith-wcan-filters");
        filterContainers.forEach(function(cont){
            fmoObs.observe(cont,{childList:true,subtree:true,attributes:true});
        });
        fmoUpdateCount();
        
        document.addEventListener("click",function(e){
            // Abrir popup
            var btn=e.target.closest(".fmo-btn");
            if(btn){
                var popup=document.getElementById(btn.getAttribute("data-fmo"));
                if(popup){
                    popup.querySelector(".fmo-popup").classList.add("active");
                    popup.querySelector(".fmo-overlay").classList.add("active");
                    document.body.style.overflow="hidden";
                }
                return;
            }
            // Cerrar overlay
            if(e.target.closest(".fmo-overlay")){
                fmoClosePopup(e.target.closest(".fmo-overlay").getAttribute("data-close"));
                return;
            }
            // Botón borrar filtros - Ahora redirige directamente a la tienda
            if(e.target.closest(".fmo-clear")){
                var closeId = e.target.closest(".fmo-clear").closest(".fmo-wrapper").id;
                fmoClosePopup(closeId);
                return;
            } 
        });
        
        // GESTOS TÁCTILES OPTIMIZADOS
        document.addEventListener("touchstart",function(e){
            var popup=e.target.closest(".fmo-popup");
            if(!popup)return;
            var dragEl=e.target.closest("[data-drag]");
            var content=popup.querySelector(".fmo-content");
            if(dragEl||(content&&content.contains(e.target)&&content.scrollTop===0)){
                fmoDrag.y=e.touches[0].clientY;
                fmoDrag.popup=popup;
                fmoDrag.active=true;
                popup.style.transition="none";
            }
        },{passive:true});
        
        document.addEventListener("touchmove",function(e){
            if(!fmoDrag.active||!fmoDrag.popup)return;
            var delta=e.touches[0].clientY-fmoDrag.y;
            if(delta>0)fmoDrag.popup.style.transform="translateY("+delta+"px)";
        },{passive:true});
        
        document.addEventListener("touchend",function(){
            if(!fmoDrag.active||!fmoDrag.popup)return;
            fmoDrag.active=false;
            var delta=parseInt(fmoDrag.popup.style.transform.replace(/[^\d]/g,""))||0;
            fmoDrag.popup.style.transition="transform .3s";
            if(delta>100){
                fmoClosePopup(fmoDrag.popup.closest(".fmo-wrapper").id);
            }else{
                fmoDrag.popup.style.transform="translateY(0)";
            }
            fmoDrag.y=0;fmoDrag.popup=null;
        });
        
        // TECLA ESC
        document.addEventListener("keydown",function(e){
            if(e.key==="Escape"){
                var activePopup=document.querySelector(".fmo-popup.active");
                if(activePopup){
                    fmoClosePopup(activePopup.closest(".fmo-wrapper").id);
                }
            }
        });
    }
    
    function fmoClosePopup(id){
        var wrapper=document.getElementById(id);
        if(!wrapper)return;
        var popup=wrapper.querySelector(".fmo-popup");
        popup.classList.remove("active");
        var overlay = wrapper.querySelector(".fmo-overlay");
        if(overlay) overlay.classList.remove("active");
        popup.style.transform="";
        document.body.style.overflow="";
    }
    
    })();</script>';
}
add_action('wp_footer', 'fmo_js_hyper', 20);