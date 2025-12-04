/**
 * Sticky Horizontal Scroll
 * Muestra una barra de scroll horizontal fija en la parte inferior de la pantalla
 * cuando la tabla es más ancha que su contenedor visible.
 */
(function() {
  'use strict';

  var stickyWrapper = null;
  var stickyContent = null;
  var targetTable = null;
  var scrollContainer = null;
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

  function findTable() {
    // Buscar la tabla principal (DataTable o tabla normal)
    var tables = document.querySelectorAll(
      '#tablaClientes, #tablaProspectos, #tablaContadores, ' +
      '#tablaOportunidades, #tablaIncidencias, #tablaVentas, ' +
      'table.dataTable, .dataTables_wrapper table'
    );
    
    var widestTable = null;
    var maxWidth = 0;
    
    tables.forEach(function(table) {
      var width = table.scrollWidth || table.offsetWidth;
      if (width > maxWidth) {
        maxWidth = width;
        widestTable = table;
      }
    });
    
    return widestTable;
  }

  function findScrollContainer(table) {
    // Buscar el contenedor scrolleable de la tabla
    var parent = table.parentElement;
    while (parent) {
      var style = window.getComputedStyle(parent);
      if (style.overflowX === 'auto' || style.overflowX === 'scroll') {
        return parent;
      }
      if (parent.classList.contains('dataTables_wrapper') ||
          parent.classList.contains('content-wrapper') ||
          parent.classList.contains('box-body')) {
        return parent;
      }
      parent = parent.parentElement;
    }
    return document.querySelector('.content-wrapper');
  }

  function updateAll() {
    targetTable = findTable();
    
    if (!targetTable) {
      console.log('[sticky-scroll] No table found');
      if (stickyWrapper) stickyWrapper.classList.remove('visible');
      return;
    }
    
    scrollContainer = findScrollContainer(targetTable);
    console.log('[sticky-scroll] Table found:', targetTable.id || 'unnamed', 'width:', targetTable.scrollWidth);
    
    setupScrollSync();
    updateStickyWidth();
    updateVisibility();
  }

  function setupScrollSync() {
    if (!scrollContainer || !stickyWrapper) return;
    
    // Sincronizar scroll
    stickyWrapper.onscroll = function() {
      if (!stickyWrapper._syncing && scrollContainer) {
        scrollContainer._syncing = true;
        scrollContainer.scrollLeft = stickyWrapper.scrollLeft;
        scrollContainer._syncing = false;
      }
    };
    
    scrollContainer.onscroll = function() {
      if (!scrollContainer._syncing && stickyWrapper) {
        stickyWrapper._syncing = true;
        stickyWrapper.scrollLeft = scrollContainer.scrollLeft;
        stickyWrapper._syncing = false;
      }
    };
  }

  function updateStickyWidth() {
    if (!targetTable || !stickyContent || !stickyWrapper || !scrollContainer) return;
    
    var tableWidth = targetTable.scrollWidth || targetTable.offsetWidth;
    var containerRect = scrollContainer.getBoundingClientRect();
    var visibleWidth = containerRect.width;
    
    console.log('[sticky-scroll] tableWidth:', tableWidth, 'visibleWidth:', visibleWidth);
    
    // Si la tabla es más ancha que el contenedor visible
    if (tableWidth > visibleWidth + 10) {
      stickyContent.style.width = tableWidth + 'px';
      stickyWrapper.style.width = visibleWidth + 'px';
      stickyWrapper.style.left = containerRect.left + 'px';
      stickyWrapper.scrollLeft = scrollContainer.scrollLeft;
    }
  }

  function updateVisibility() {
    if (!targetTable || !stickyWrapper || !scrollContainer) return;
    
    var tableWidth = targetTable.scrollWidth || targetTable.offsetWidth;
    var containerRect = scrollContainer.getBoundingClientRect();
    var visibleWidth = containerRect.width;
    
    // No hay scroll horizontal necesario, ocultar
    if (tableWidth <= visibleWidth + 10) {
      stickyWrapper.classList.remove('visible');
      console.log('[sticky-scroll] Hidden: table fits in container');
      return;
    }

    // Verificar si la tabla está visible en pantalla
    var tableRect = targetTable.getBoundingClientRect();
    var windowHeight = window.innerHeight;
    var tableInView = tableRect.top < windowHeight && tableRect.bottom > 0;
    
    console.log('[sticky-scroll] tableInView:', tableInView, 'needsScroll:', tableWidth > visibleWidth);

    if (tableInView) {
      stickyWrapper.classList.add('visible');
      stickyWrapper.style.width = visibleWidth + 'px';
      stickyWrapper.style.left = containerRect.left + 'px';
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
      
      jQuery(document).on('draw.dt init.dt', function() {
        console.log('[sticky-scroll] DataTable event detected');
        setTimeout(updateAll, 300);
      });
    });
  }

})();
