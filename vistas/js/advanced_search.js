// Advanced Search UI handling and event emission
(function(window, $) {
  if (!$) return;

  $(function(){
    var panel = $('#advanced-search-panel-inline');
    // debug: confirm initialization
    try { console.debug('advanced_search.js initialized. panel found:', panel.length > 0); } catch(e){}

    // Toggle button: attach delegated handler and a direct handler to be robust
    $(document).on('click', '#btn-toggle-advanced-search', function(e){
      e.preventDefault();
      try { console.debug('advanced search toggle clicked (delegated)'); } catch(e){}
      if (!panel.length) return;
      panel.slideToggle(200, function(){
        if (panel.is(':visible')){
          var $first = panel.find('input[type=text], input[type=date], select').first();
          if ($first.length) $first.focus();
          $('html, body').animate({ scrollTop: panel.offset().top - 70 }, 200);
        }
      });
    });

    // NOTE: do not bind a direct handler here — use the delegated handler above

    // Close button (support both id and class selectors)
    $(document).on('click', '#btn-close-advanced-search, .btn-close-advanced-search', function(e){
      e.preventDefault();
      try { console.debug('advanced search close clicked'); } catch(e){}
      if (panel.length) panel.slideUp(150);
    });

    // Show/hide custom dates depending on periodo (scoped to the panel containing the control)
    $(document).on('change', '#advanced-search-panel-inline [name=adv_periodo], #advanced-search-top [name=adv_periodo]', function(){
      var $panel = $(this).closest('#advanced-search-panel-inline, #advanced-search-top');
      if ($(this).val() === 'custom') $panel.find('.adv_custom_dates').show(); else $panel.find('.adv_custom_dates').hide();
    });

    // Submit handler for the inline form (build filters from the form that was submitted)
    $(document).on('submit', '#form-advanced-search-inline, #form-advanced-search', function(e){
      e.preventDefault();
      var $form = $(this);
      var filters = {
        nombre: $form.find('[name=adv_nombre]').val() || '',
        telefono: $form.find('[name=adv_telefono]').val() || '',
        documento: $form.find('[name=adv_documento]').val() || '',
        periodo: $form.find('[name=adv_periodo]').val() || '',
        fecha_inicio: $form.find('[name=adv_fecha_inicio]').val() || '',
        fecha_fin: $form.find('[name=adv_fecha_fin]').val() || ''
      };
      try { console.debug('advanced_search: submit detected on', $form.attr('id'), 'filters:', filters); } catch(e){}
      var event = new CustomEvent('advancedSearch:apply', { detail: filters });
      window.dispatchEvent(event);
      try { console.debug('advanced_search: event dispatched advancedSearch:apply'); } catch(e){}
    });

    // Clear button (scoped)
    $(document).on('click', '#advanced-search-panel-inline .adv-clear, #advanced-search-top .adv-clear', function(e){
      e.preventDefault();
      var $panel = $(this).closest('#advanced-search-panel-inline, #advanced-search-top');
      $panel.find('[name=adv_nombre],[name=adv_telefono],[name=adv_documento],[name=adv_periodo],[name=adv_fecha_inicio],[name=adv_fecha_fin]').val('');
      $panel.find('.adv_custom_dates').hide();
      var event = new CustomEvent('advancedSearch:clear');
      window.dispatchEvent(event);
    });

    // Close panel on ESC
    $(document).on('keydown', function(e){
      if (e.key === 'Escape') {
        if (panel.length) panel.slideUp(150);
      }
    });

    // Debug helper: when an advancedSearch:apply event fires, try to query the server-side ajax
    // of the visible '.tabla' and show a temporary debug banner with counts/rows to help
    // diagnose why searches return no changes.
    window.addEventListener('advancedSearch:apply', function(e){
      try {
        var filters = (e && e.detail) ? e.detail : {};
        // find the first visible table with class 'tabla' inside the same module/area
        var $visibleTable = $('.tabla:visible').first();
        if (!$visibleTable || $visibleTable.length === 0) return;
        var ajaxUrl = $visibleTable.data('ajax');
        if (!ajaxUrl) return;

        // build post payload mirroring DataTable debug usage
        var postData = {
          adv_debug: 1,
          draw: 1,
          start: 0,
          length: 10,
          nombre: filters.nombre || '',
          telefono: filters.telefono || '',
          documento: filters.documento || '',
          adv_nombre: filters.nombre || '',
          adv_telefono: filters.telefono || '',
          adv_documento: filters.documento || '',
          adv_periodo: filters.periodo || '',
          adv_fecha_inicio: filters.fecha_inicio || '',
          adv_fecha_fin: filters.fecha_fin || ''
        };

        // show a loading banner
        var $banner = $('<div class="advanced-search-debug-banner alert alert-info" style="margin:10px 15px;">Buscando... (debug)</div>');
        // insert banner before the table's container
        $visibleTable.before($banner);

        // perform AJAX POST
        $.ajax({
          url: ajaxUrl,
          method: 'POST',
          data: postData,
          dataType: 'json'
        }).done(function(resp){
          try {
            var recordsTotal = resp && resp.recordsTotal !== undefined ? resp.recordsTotal : 'N/A';
            var recordsFiltered = resp && resp.recordsFiltered !== undefined ? resp.recordsFiltered : 'N/A';
            var rowsFetched = (resp && resp.data && Array.isArray(resp.data)) ? resp.data.length : 'N/A';
            var html = '<strong>Debug búsqueda:</strong> total=' + recordsTotal + ', filtrados=' + recordsFiltered + ', filas devueltas=' + rowsFetched;
            if (resp && resp.debug && resp.debug.sqlWhere) html += '<br><small>SQL WHERE: ' + (resp.debug.sqlWhere || '') + '</small>';
            $banner.removeClass('alert-info').addClass('alert-success').html(html);
          } catch(err){
            $banner.removeClass('alert-info').addClass('alert-danger').text('Debug: respuesta inválida o error al mostrar datos');
          }
        }).fail(function(xhr){
          try { $banner.removeClass('alert-info').addClass('alert-danger').text('Debug: error al consultar el servidor (status ' + xhr.status + ')'); } catch(e){}
        }).always(function(){
          // remove banner after 6 seconds
          setTimeout(function(){ $banner.fadeOut(300, function(){ $banner.remove(); }); }, 6000);
        });
      } catch(e){
        // silent
      }
    });
  });

})(window, window.jQuery);
