/**
 * Control de sonido, pantalla completa y loader para video promocional
 */

document.addEventListener('DOMContentLoaded', function() {
    const video = document.getElementById('video-promocional');
    const soundToggle = document.getElementById('video-sound-toggle');
    const iconMuted = soundToggle.querySelector('.icon-muted');
    const iconUnmuted = soundToggle.querySelector('.icon-unmuted');
    const container = video.closest('.hero-video-container');
    const loader = document.getElementById('video-loader');

    // =============================================
    // 1. DETECTAR SI ES DISPOSITIVO MÓVIL
    // =============================================
    // Usar matchMedia para evitar reflow forzado
    const isMobile = window.matchMedia('(max-width: 768px)').matches;

    // =============================================
    // 2. CONTROL DEL LOADER
    // =============================================
    let loaderHidden = false;
    let loaderTimeout = null;
    let videoLoaded = false;

    function hideLoader() {
        if (!loaderHidden) {
            loader.classList.add('hidden');
            loaderHidden = true;
            if (loaderTimeout) {
                clearTimeout(loaderTimeout);
                loaderTimeout = null;
            }
        }
    }

    function showLoader() {
        // Solo si el video no está cargado y no está oculto
        if (video.readyState < 2 && !videoLoaded && !loaderHidden) {
            loader.classList.remove('hidden');
            loaderHidden = false;
        }
    }

    // ===== EVENTOS DE VIDEO =====
    video.addEventListener('loadstart', function() {
        showLoader();
    });

    video.addEventListener('canplay', function() {
        videoLoaded = true;
        hideLoader();
    });

    video.addEventListener('playing', function() {
        videoLoaded = true;
        hideLoader();
    });

    video.addEventListener('loadeddata', function() {
        videoLoaded = true;
        hideLoader();
    });

    video.addEventListener('canplaythrough', function() {
        videoLoaded = true;
        hideLoader();
    });

    // ===== SI EL VIDEO FALLA (404) =====
    video.addEventListener('error', function(e) {
        console.warn('Error al cargar el video:', e);
        // Mostrar un mensaje en el loader
        const loaderText = loader.querySelector('span');
        if (loaderText) {
            loaderText.textContent = '⚠️ No se pudo cargar el video';
        }
        // Ocultar el spinner pero mantener el mensaje
        const spinner = loader.querySelector('.loader-spinner');
        if (spinner) {
            spinner.style.display = 'none';
        }
        // No ocultar el loader para que el usuario vea el mensaje
        // pero después de 5 segundos lo ocultamos
        setTimeout(function() {
            hideLoader();
        }, 5000);
    });

    // ===== TIMEOUT DE SEGURIDAD =====
    // Si después de 10 segundos el video no ha cargado, ocultar el loader
    loaderTimeout = setTimeout(function() {
        if (!videoLoaded) {
            console.warn('Timeout: el video no cargó, ocultando loader');
            // Mostrar mensaje de error
            const loaderText = loader.querySelector('span');
            if (loaderText) {
                loaderText.textContent = '⚠️ El video está tardando en cargar';
            }
            // Ocultar después de 3 segundos más
            setTimeout(function() {
                hideLoader();
            }, 3000);
        }
    }, 10000);

    // Si el video ya está listo al cargar la página
    if (video.readyState >= 2) {
        videoLoaded = true;
        hideLoader();
    }

    // =============================================
    // 3. PANTALLA COMPLETA EN MÓVIL
    // =============================================
    if (isMobile) {
        video.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            container.classList.toggle('fullscreen-mobile');
            
            if (container.classList.contains('fullscreen-mobile')) {
                video.play().catch(function() {
                    video.muted = true;
                    video.play();
                });
            }
        });
    }

    // =============================================
    // 4. PANTALLA COMPLETA ESCRITORIO
    // =============================================
    if (!isMobile) {
        video.addEventListener('dblclick', function(e) {
            e.preventDefault();
            if (!document.fullscreenElement) {
                if (video.requestFullscreen) {
                    video.requestFullscreen();
                } else if (video.webkitRequestFullscreen) {
                    video.webkitRequestFullscreen();
                } else if (video.msRequestFullscreen) {
                    video.msRequestFullscreen();
                }
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
            }
        });
    }

    // =============================================
    // 5. CONTROL DE SONIDO
    // =============================================
    soundToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        
        if (video.muted) {
            video.muted = false;
            iconMuted.style.display = 'none';
            iconUnmuted.style.display = 'block';
            soundToggle.setAttribute('aria-label', 'Silenciar sonido');
        } else {
            video.muted = true;
            iconMuted.style.display = 'block';
            iconUnmuted.style.display = 'none';
            soundToggle.setAttribute('aria-label', 'Activar sonido');
        }
    });

    // =============================================
    // 6. ICONO INICIAL
    // =============================================
    iconMuted.style.display = 'block';
    iconUnmuted.style.display = 'none';

    // =============================================
    // 7. TOOLTIP (solo escritorio)
    // =============================================
    if (!isMobile) {
        const tooltip = document.createElement('div');
        tooltip.className = 'video-fullscreen-tip';
        tooltip.textContent = 'Doble clic para pantalla completa';
        tooltip.style.cssText = `
            position: absolute;
            bottom: 70px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.7);
            color: #fff;
            padding: 6px 14px;
            border-radius: 4px;
            font-size: 12px;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.5s ease;
            z-index: 5;
            white-space: nowrap;
        `;
        container.appendChild(tooltip);

        let tooltipTimeout;
        video.addEventListener('mouseenter', function() {
            clearTimeout(tooltipTimeout);
            tooltip.style.opacity = '1';
        });

        video.addEventListener('mouseleave', function() {
            tooltipTimeout = setTimeout(function() {
                tooltip.style.opacity = '0';
            }, 2000);
        });

        setTimeout(function() {
            tooltip.style.opacity = '0';
        }, 5000);
    }
});