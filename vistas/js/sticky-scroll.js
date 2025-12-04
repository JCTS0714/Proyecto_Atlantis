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
  var targetTable = null;
  var isInitialized = false;

  function init() {
    if (isInitialized) return;
    
    contentWrapper = document.querySelector('.content-wrapper');
    if (!contentWrapper) {
      console.log('sticky-scroll: content-wrapper not found');
      return;
    }

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
    console.log('sticky-scroll: initialized');
    
    // Actualizar inicialmente con varios intentos
    setTimeout(updateAll, 500);
    setTimeout(updateAll, 1000);
    setTimeout(updateAll, 2000);
  }

  function updateAll() {
    findTargetTable();
    updateStickyWidth();
    updateVisibility();
  }

  function findTargetTable() {
    // Buscar la tabla más ancha
    var tables = contentWrapper.querySelectorAll('table.dataTable, .dataTables_wrapper table, table.tabla');
    var maxWidth = 0;
    
    tables.forEach(function(t) {
      var w = t.scrollWidth || t.offsetWidth;
      if (w > maxWidth) {
        maxWidth = w;
        targetTable = t;
      }
    });
    
    if (!targetTable) {
      targetTable = contentWrapper.querySelector('.dataTables_wrapper, .box-body, table');
    }
  }

  function updateStickyWidth() {
    if (!contentWrapper || !stickyContent || !stickyWrapper) return;
    
    // Buscar el elemento con mayor ancho de scroll
    var scrollWidth = contentWrapper.scrollWidth;
    var clientWidth = contentWrapper.clientWidth;
    
    // También verificar tablas específicas
    if (targetTable) {
      var tableWidth = targetTable.scrollWidth || targetTable.offsetWidth;
      if (tableWidth > scrollWidth) {
        scrollWidth = tableWidth;
      }
    }
    
    console.log('sticky-scroll: scrollWidth=' + scrollWidth + ', clientWidth=' + clientWidth);
    
    // Solo mostrar si hay contenido que scrollear (más de 20px de diferencia)
    if (scrollWidth > clientWidth + 20) {
      stickyContent.style.width = scrollWidth + 'px';
      stickyWrapper.style.width = clientWidth + 'px';
      stickyWrapper.style.left = contentWrapper.getBoundingClientRect().left + 'px';
      document.body.classList.add('has-sticky-scroll');
      console.log('sticky-scroll: scroll needed');
    } else {
      stickyWrapper.classList.remove('visible');
      document.body.classList.remove('has-sticky-scroll');
      console.log('sticky-scroll: no scroll needed');
    }
  }

  function updateVisibility() {
    if (!contentWrapper || !stickyWrapper) return;
    
    var scrollWidth = contentWrapper.scrollWidth;
    var clientWidth = contentWrapper.clientWidth;
    
    // También verificar tabla
    if (targetTable) {
      var tableWidth = targetTable.scrollWidth || targetTable.offsetWidth;
      if (tableWidth > scrollWidth) {
        scrollWidth = tableWidth;
      }
    }
    
    // Si no hay nada que scrollear, ocultar
    if (scrollWidth <= clientWidth + 20) {
      stickyWrapper.classList.remove('visible');
      return;
    }

    // Encontrar la tabla o el elemento scrolleable principal
    var table = targetTable || contentWrapper.querySelector('.dataTables_wrapper, .table-responsive, table.dataTable, .box-body');
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

    console.log('sticky-scroll: tableInView=' + tableInView + ', tableBottomVisible=' + tableBottomVisible);

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
  if (typeof $ !== 'undefined') {
    $(document).ready(function() {
      setTimeout(init, 500);
      
      // Reinicializar cuando se redibuje cualquier DataTable
      $(document).on('draw.dt', function() {
        setTimeout(updateAll, 100);
      });
    });
  }

})();
