(function(){
  function hidePreloader(){
    var el = document.getElementById('islabeya-preloader');
    if(!el){ return; }
    el.classList.add('is-hidden');
  }

  function showPreloader(){
    var el = document.getElementById('islabeya-preloader');
    if(!el){ return; }
    el.classList.remove('is-hidden');
  }

  // Si el script corre cuando el usuario llega a una página (incluye back/forward)
  // mantenemos el preloader visible hasta que el contenido esté listo.
  if(window.islabeyaPreloader && window.islabeyaPreloader.enabled){
    showPreloader();

    var fallbackMs = Number(window.islabeyaPreloader.fallbackMs || 2500);
    var started = false;

    function onReady(){
      if(started){ return; }
      started = true;
      hidePreloader();
    }

    // Tu requisito: el diseño debe estar listo (evitar saltos).
    window.addEventListener('load', onReady, { once: true });

    // Fallback por si load no llega en algún caso.
    window.setTimeout(onReady, fallbackMs);

    // Cuando el usuario navega con history (bfcache)
    window.addEventListener('pageshow', function(event){
      if(event && event.persisted){
        showPreloader();
        window.setTimeout(hidePreloader, fallbackMs);
      }
    });
  }
})();

