// Advanced Search UI handling and event emission
(function(window, $) {
  if (!$) return;

  function getFilters() {
    var periodo = $('#adv_periodo').val();
    var filters = {
      nombre: $('#adv_nombre').val() || '',
      telefono: $('#adv_telefono').val() || '',
      documento: $('#adv_documento').val() || '',
      periodo: periodo || '',
      fecha_inicio: '',
      fecha_fin: ''
    };

    if (periodo === 'custom') {
      filters.fecha_inicio = $('#adv_fecha_inicio').val() || '';
      filters.fecha_fin = $('#adv_fecha_fin').val() || '';
    }

    return filters;
  }

  function applyFilters() {
    var filters = getFilters();
    window.advancedSearchFilters = filters; // global for quick access
    // Emit a DOM event so any page module can listen
    window.dispatchEvent(new CustomEvent('advancedSearch:apply', { detail: filters }));
  }

  function clearFilters() {
    $('#form-advanced-search')[0].reset();
    $('#adv_custom_dates').hide();
    window.advancedSearchFilters = {};
    window.dispatchEvent(new CustomEvent('advancedSearch:clear'));
  }

  $(function() {
    // Toggle panel
    $('#btn-toggle-advanced-search').on('click', function() {
      $('#advanced-search-panel').toggle();
      $('#adv_nombre').focus();
    });

    $('#btn-close-advanced-search').on('click', function() {
      $('#advanced-search-panel').hide();
    });

    // Period selection
    $('#adv_periodo').on('change', function() {
      if ($(this).val() === 'custom') {
        $('#adv_custom_dates').show();
      } else {
        $('#adv_custom_dates').hide();
      }
    });

    // Apply
    $('#form-advanced-search').on('submit', function(e) {
      e.preventDefault();
      applyFilters();
    });

    $('#adv_clear').on('click', function() {
      clearFilters();
    });

    // Close on ESC
    $(document).on('keydown', function(e) {
      if (e.key === 'Escape') {
        $('#advanced-search-panel').hide();
      }
    });
  });

})(window, window.jQuery);
