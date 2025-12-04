/**
 * Sticky Horizontal Scroll
 * Muestra una barra de scroll horizontal fija en la parte inferior de la pantalla
 * cuando hay scroll horizontal disponible y el usuario no puede ver la barra de scroll natural.
 */
(function() {
  'use strict';

  var stickyWrapper = null;
  var stickyContent = null;
  var scrollableElement = null;
  var isInitialized = false;

  function init() {
    if (isInitialized) return;
    
    // Crear el wrapper del scroll sticky
    stickyWrapper = document.createElement('div');
    stickyWrapper.className = 'sticky-scroll-wrapper';
    stickyWrapper.id = 'sticky-scroll-wrapper';
    
    stickyContent = document.createElement('div');
    stickyContent.className = 'sticky-scroll-content';
    
    stickyWrapper.appendChild(stickyContent);
    document.body.appendChild(stickyWrapper);

    // Eventos para actualizar
    window.addEventListener('scroll', updateVisibility, { passive: true });
    window.addEventListener('resize', updateAll);
    
    // Observar cambios en el DOM
    var observer = new MutationObserver(function() {
      setTimeout(updateAll, 200);
    });
    observer.observe(document.body, { childList: true, subtree: true });

    isInitialized = true;
    console.log('[sticky-scroll] Initialized');
    
    // Actualizar con varios intentos
    setTimeout(updateAll, 500);
    setTimeout(updateAll, 1500);
    setTimeout(updateAll, 3000);
  }

  function findScrollableElement() {
    // Buscar el elemento que tiene scroll horizontal
    // Prioridad: dataTables_scrollBody > content-wrapper > table-responsive
    var candidates = [
      '.dataTables_scrollBody',
      '.content-wrapper',
      '.table-responsive',
      '.box-body'
    ];
    
    for (var i = 0; i < candidates.length; i++) {
      var elements = document.querySelectorAll(candidates[i]);
      for (var j = 0; j < elements.length; j++) {
        var el = elements[j];
        if (el.scrollWidth > el.clientWidth + 10) {
          console.log('[sticky-scroll] Found scrollable:', candidates[i], 'scrollWidth:', el.scrollWidth, 'clientWidth:', el.clientWidth);
          return el;
        }
      }
    }
    
    // Fallback: buscar cualquier tabla ancha
    var tables = document.querySelectorAll('table');
    for (var k = 0; k < tables.length; k++) {
      var table = tables[k];
      var parent = table.parentElement;
      if (parent && table.offsetWidth > parent.clientWidth + 10) {
        console.log('[sticky-scroll] Found wide table, using parent');
        return parent;
      }
    }
    
    return null;
  }

  function updateAll() {
    scrollableElement = findScrollableElement();
    
    if (!scrollableElement) {
      console.log('[sticky-scroll] No scrollable element found');
      if (stickyWrapper) stickyWrapper.classList.remove('visible');
      return;
    }
    
    setupScrollSync();
    updateStickyWidth();
    updateVisibility();
  }

  function setupScrollSync() {
    if (!scrollableElement || !stickyWrapper) return;
    
    // Remover listeners anteriores
    stickyWrapper.onscroll = null;
    scrollableElement.onscroll = null;
    
    // Sincronizar scroll
    stickyWrapper.onscroll = function() {
      if (!stickyWrapper._syncing) {
        scrollableElement._syncing = true;
        scrollableElement.scrollLeft = stickyWrapper.scrollLeft;
        scrollableElement._syncing = false;
      }
    };
    
    scrollableElement.onscroll = function() {
      if (!scrollableElement._syncing) {
        stickyWrapper._syncing = true;
        stickyWrapper.scrollLeft = scrollableElement.scrollLeft;
        stickyWrapper._syncing = false;
      }
    };
  }

  function updateStickyWidth() {
    if (!scrollableElement || !stickyContent || !stickyWrapper) return;
    
    var scrollWidth = scrollableElement.scrollWidth;
    var clientWidth = scrollableElement.clientWidth;
    var rect = scrollableElement.getBoundingClientRect();
    
    console.log('[sticky-scroll] scrollWidth:', scrollWidth, 'clientWidth:', clientWidth);
    
    if (scrollWidth > clientWidth + 10) {
      stickyContent.style.width = scrollWidth + 'px';
      stickyWrapper.style.width = clientWidth + 'px';
      stickyWrapper.style.left = rect.left + 'px';
      stickyWrapper.scrollLeft = scrollableElement.scrollLeft;
    }
  }

  function updateVisibility() {
    if (!scrollableElement || !stickyWrapper) return;
    
    var scrollWidth = scrollableElement.scrollWidth;
    var clientWidth = scrollableElement.clientWidth;
    
    // No hay scroll horizontal, ocultar
    if (scrollWidth <= clientWidth + 10) {
      stickyWrapper.classList.remove('visible');
      return;
    }

    var rect = scrollableElement.getBoundingClientRect();
    var windowHeight = window.innerHeight;
    
    // Verificar si el elemento está en vista (al menos parcialmente)
    var elementInView = rect.top < windowHeight && rect.bottom > 0;
    
    console.log('[sticky-scroll] inView:', elementInView, 'hasScroll:', true);

    if (elementInView) {
      // El elemento está en vista y tiene scroll horizontal - mostrar siempre
      stickyWrapper.classList.add('visible');
      
      // Actualizar posición
      stickyWrapper.style.width = clientWidth + 'px';
      stickyWrapper.style.left = rect.left + 'px';
    } else {
      stickyWrapper.classList.remove('visible');
    }
  }

  // Inicializar
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
      setTimeout(init, 500);
    });
  } else {
    setTimeout(init, 500);
  }

  // También inicializar con jQuery si está disponible
  if (typeof jQuery !== 'undefined') {
    jQuery(document).ready(function() {
      setTimeout(init, 800);
      
      // Actualizar cuando DataTables se redibuje
      jQuery(document).on('draw.dt init.dt', function() {
        console.log('[sticky-scroll] DataTable event detected');
        setTimeout(updateAll, 300);
      });
    });
  }

})();
