/**
 * Sticky Horizontal Scroll
 * Muestra una barra de scroll horizontal fija en la parte inferior de la pantalla
 * cuando la tabla es más ancha que la pantalla y el scroll natural no es visible.
 * Se oculta cuando el usuario puede ver el scroll natural de la tabla.
 */
(function() {
  'use strict';

  var stickyWrapper = null;
  var stickyContent = null;
  var contentWrapper = null;
  var isInitialized = false;

  function init() {
    if (isInitialized) return;
    
    contentWrapper = document.querySelector('.content-wrapper');
    if (!contentWrapper) return;

    // Crear el wrapper del scroll sticky
    stickyWrapper = document.createElement('div');
    stickyWrapper.className = 'sticky-scroll-wrapper';
    
    stickyContent = document.createElement('div');
    stickyContent.className = 'sticky-scroll-content';
    
    stickyWrapper.appendChild(stickyContent);
    document.body.appendChild(stickyWrapper);
    
    // Sincronizar scroll entre la barra sticky y el content-wrapper
    stickyWrapper.addEventListener('scroll', function() {
      if (contentWrapper && !stickyWrapper._syncing) {
        contentWrapper._syncing = true;
        contentWrapper.scrollLeft = stickyWrapper.scrollLeft;
        contentWrapper._syncing = false;
      }
    });
    
    contentWrapper.addEventListener('scroll', function() {
      if (stickyWrapper && !contentWrapper._syncing) {
        stickyWrapper._syncing = true;
        stickyWrapper.scrollLeft = contentWrapper.scrollLeft;
        stickyWrapper._syncing = false;
      }
    });

    // Eventos para actualizar la visibilidad
    window.addEventListener('scroll', updateVisibility);
    window.addEventListener('resize', updateAll);
    
    // Observar cambios en el DOM (para cuando se carga la tabla)
    var observer = new MutationObserver(function() {
      setTimeout(updateAll, 100);
    });
    observer.observe(contentWrapper, { childList: true, subtree: true });

    isInitialized = true;
    
    // Actualizar inicialmente
    setTimeout(updateAll, 500);
  }

  function updateAll() {
    updateStickyWidth();
    updateVisibility();
  }

  function updateStickyWidth() {
    if (!contentWrapper || !stickyContent || !stickyWrapper) return;
    
    // El ancho del contenido scrolleable
    var scrollWidth = contentWrapper.scrollWidth;
    var clientWidth = contentWrapper.clientWidth;
    
    // Solo mostrar si hay contenido que scrollear
    if (scrollWidth > clientWidth) {
      stickyContent.style.width = scrollWidth + 'px';
      stickyWrapper.style.width = clientWidth + 'px';
      stickyWrapper.style.left = contentWrapper.getBoundingClientRect().left + 'px';
      document.body.classList.add('has-sticky-scroll');
    } else {
      stickyWrapper.classList.remove('visible');
      document.body.classList.remove('has-sticky-scroll');
    }
  }

  function updateVisibility() {
    if (!contentWrapper || !stickyWrapper) return;
    
    var scrollWidth = contentWrapper.scrollWidth;
    var clientWidth = contentWrapper.clientWidth;
    
    // Si no hay nada que scrollear, ocultar
    if (scrollWidth <= clientWidth) {
      stickyWrapper.classList.remove('visible');
      return;
    }

    // Encontrar la tabla o el elemento scrolleable principal
    var table = contentWrapper.querySelector('.dataTables_wrapper, .table-responsive, table.dataTable, .box-body');
    if (!table) {
      stickyWrapper.classList.remove('visible');
      return;
    }

    var tableRect = table.getBoundingClientRect();
    var windowHeight = window.innerHeight;
    
    // El scroll natural de la tabla está en la parte inferior del contenedor
    // Si el final de la tabla está visible en pantalla, ocultar el sticky scroll
    var tableBottomVisible = tableRect.bottom <= windowHeight + 20; // 20px de margen
    
    // También verificar si la tabla es visible en pantalla
    var tableTopVisible = tableRect.top < windowHeight;
    var tableInView = tableTopVisible && tableRect.bottom > 0;

    if (tableInView && !tableBottomVisible) {
      // La tabla está en vista pero su parte inferior no es visible
      stickyWrapper.classList.add('visible');
      
      // Actualizar posición y ancho
      stickyWrapper.style.width = clientWidth + 'px';
      stickyWrapper.style.left = contentWrapper.getBoundingClientRect().left + 'px';
    } else {
      // El final de la tabla es visible o la tabla no está en vista
      stickyWrapper.classList.remove('visible');
    }
  }

  // Inicializar cuando el DOM esté listo
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
      setTimeout(init, 300);
    });
  } else {
    setTimeout(init, 300);
  }

  // También reinicializar después de que DataTables cargue
  $(document).ready(function() {
    setTimeout(init, 500);
    
    // Reinicializar cuando se redibuje cualquier DataTable
    $(document).on('draw.dt', function() {
      setTimeout(updateAll, 100);
    });
  });

})();
