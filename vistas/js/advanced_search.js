/**
 * Advanced Search UI handling and event emission
 * Este archivo maneja la lógica de la búsqueda avanzada de forma segura
 * Todos los bloques están envueltos en try-catch para evitar bloquear otras funcionalidades
 */
(function(window, $) {
  'use strict';
  
  // Si jQuery no está disponible, salir silenciosamente
  if (!$ || typeof $ !== 'function') {
    console.warn('advanced_search.js: jQuery no disponible');
    return;
  }

  // Función helper para construir filtros desde un formulario
  function buildFilters($form) {
    if (!$form || !$form.length) return {};
    
    try {
      // Obtener el tipo de fecha seleccionado (radio button)
      var tipoFecha = $form.find('[name=adv_tipo_fecha]:checked').val() || 'fecha_creacion';
      
      return {
        nombre: ($form.find('[name=adv_nombre]').val() || '').trim(),
        telefono: ($form.find('[name=adv_telefono]').val() || '').trim(),
        documento: ($form.find('[name=adv_documento]').val() || '').trim(),
        periodo: ($form.find('[name=adv_periodo]').val() || '').trim(),
        mes_unico: ($form.find('[name=adv_mes_unico]').val() || '').trim(),
        mes_desde: ($form.find('[name=adv_mes_desde]').val() || '').trim(),
        mes_hasta: ($form.find('[name=adv_mes_hasta]').val() || '').trim(),
        fecha_unica: ($form.find('[name=adv_fecha_unica]').val() || '').trim(),
        fecha_inicio: ($form.find('[name=adv_fecha_inicio]').val() || '').trim(),
        fecha_fin: ($form.find('[name=adv_fecha_fin]').val() || '').trim(),
        tipo_fecha: tipoFecha,
        servidor: ($form.find('[name=adv_servidor]').val() || '').trim()
      };
    } catch (e) {
      console.warn('advanced_search: error building filters', e);
      return {};
    }
  }

  // Función para despachar evento de búsqueda
  function dispatchSearchEvent(filters) {
    try {
      var event = new CustomEvent('advancedSearch:apply', { detail: filters });
      window.dispatchEvent(event);
    } catch (e) {
      console.warn('advanced_search: error dispatching event', e);
    }
  }

  // Inicialización cuando el DOM está listo
  $(function() {
    var $panel = $('#advanced-search-panel-inline');
    var $toggleBtn = $('#btn-toggle-advanced-search');
    
    // Mostrar filtro de servidor solo en páginas de clientes y contadores
    try {
      var currentPath = window.location.pathname.toLowerCase();
      if (currentPath.indexOf('clientes') !== -1 || currentPath.indexOf('contadores') !== -1) {
        $('.adv-servidor-filter').show();
      }
    } catch (err) {
      console.warn('advanced_search: error checking servidor filter visibility', err);
    }
    
    // Toggle del panel de búsqueda avanzada
    $(document).on('click', '#btn-toggle-advanced-search', function(e) {
      e.preventDefault();
      try {
        if ($panel.length) {
          $panel.slideToggle(200, function() {
            if ($panel.is(':visible')) {
              $panel.find('input:first').focus();
            }
          });
        }
      } catch (err) {
        console.warn('advanced_search: error toggling panel', err);
      }
    });

    // Cerrar panel
    $(document).on('click', '.btn-close-advanced-search', function(e) {
      e.preventDefault();
      try {
        if ($panel.length) $panel.slideUp(150);
      } catch (err) {
        console.warn('advanced_search: error closing panel', err);
      }
    });

    // Mostrar/ocultar campos según periodo seleccionado
    $(document).on('change', '[name=adv_periodo]', function() {
      try {
        var $container = $(this).closest('#advanced-search-panel-inline, #advanced-search-container, .adv-periodo-container').parent();
        var valor = $(this).val();
        
        // Ocultar todos los campos de periodo
        $container.find('.adv_por_mes, .adv_entre_meses, .adv_por_fecha, .adv_entre_fechas').hide();
        
        // Mostrar el campo correspondiente con display flex
        var $target = null;
        switch(valor) {
          case 'por_mes':
            $target = $container.find('.adv_por_mes');
            break;
          case 'entre_meses':
            $target = $container.find('.adv_entre_meses');
            break;
          case 'por_fecha':
            $target = $container.find('.adv_por_fecha');
            break;
          case 'entre_fechas':
            $target = $container.find('.adv_entre_fechas');
            break;
        }
        
        if ($target && $target.length) {
          $target.css('display', 'flex');
        }
      } catch (err) {
        console.warn('advanced_search: error toggling period fields', err);
      }
    });

    // Submit del formulario de búsqueda
    $(document).on('submit', '#form-advanced-search-inline', function(e) {
      e.preventDefault();
      console.log('advanced_search: form submit triggered');
      try {
        var filters = buildFilters($(this));
        console.log('advanced_search: filters built', filters);
        dispatchSearchEvent(filters);
        console.log('advanced_search: event dispatched');
      } catch (err) {
        console.warn('advanced_search: error on submit', err);
      }
    });

    // Click en botón Buscar (respaldo)
    $(document).on('click', '.adv-apply', function(e) {
      e.preventDefault();
      console.log('advanced_search: apply button clicked');
      try {
        var $form = $(this).closest('form');
        console.log('advanced_search: form found', $form.length > 0);
        var filters = buildFilters($form);
        console.log('advanced_search: filters built', filters);
        dispatchSearchEvent(filters);
        console.log('advanced_search: event dispatched');
      } catch (err) {
        console.warn('advanced_search: error on apply click', err);
      }
    });

    // Limpiar filtros
    $(document).on('click', '.adv-clear', function(e) {
      e.preventDefault();
      try {
        var $container = $(this).closest('#advanced-search-panel-inline, #advanced-search-container');
        $container.find('input[type=text], input[type=date], input[type=month]').val('');
        $container.find('select').val('');
        $container.find('.adv_por_mes, .adv_entre_meses, .adv_por_fecha, .adv_entre_fechas').hide();
        
        // Resetear radio buttons al valor por defecto (fecha_creacion)
        $container.find('[name=adv_tipo_fecha]').prop('checked', false);
        $container.find('[name=adv_tipo_fecha][value=fecha_creacion]').prop('checked', true);
        $container.find('[name=adv_tipo_fecha]').closest('.btn').removeClass('active');
        $container.find('[name=adv_tipo_fecha][value=fecha_creacion]').closest('.btn').addClass('active');
        
        // Despachar evento de limpiar
        var event = new CustomEvent('advancedSearch:clear');
        window.dispatchEvent(event);
      } catch (err) {
        console.warn('advanced_search: error clearing filters', err);
      }
    });

    // Cerrar con ESC
    $(document).on('keydown', function(e) {
      try {
        if (e.key === 'Escape' && $panel.is(':visible')) {
          $panel.slideUp(150);
        }
      } catch (err) {
        // Ignorar errores de teclado
      }
    });
  });

})(window, window.jQuery);
