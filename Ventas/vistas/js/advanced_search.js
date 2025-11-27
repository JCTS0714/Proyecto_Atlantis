// Advanced Search UI handling and event emission
(function(window, $) {
  if (!$) return;

  $(function(){
    var panel = $('#advanced-search-panel-inline');

    // Toggle button (delegated so it works even if injected later)
    $(document).on('click', '#btn-toggle-advanced-search', function(e){
      e.preventDefault();
      if (!panel.length) return;
      panel.slideToggle(200, function(){
        if (panel.is(':visible')){
          // focus the first input inside the panel
          var $first = panel.find('input[type=text], input[type=date], select').first();
          if ($first.length) $first.focus();
          $('html, body').animate({ scrollTop: panel.offset().top - 70 }, 200);
        }
      });
    });

    // Close button
    $(document).on('click', '#btn-close-advanced-search', function(e){
      e.preventDefault();
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
      var event = new CustomEvent('advancedSearch:apply', { detail: filters });
      window.dispatchEvent(event);
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
  });

})(window, window.jQuery);
