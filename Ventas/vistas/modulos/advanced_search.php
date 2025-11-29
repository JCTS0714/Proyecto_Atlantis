<?php
// Partial: Advanced Search panel (reusable across pages)
?>
<!-- Advanced Search Top Bar (collapsed by default) -->
<div id="advanced-search-top" class="box box-default" style="display:none; margin: 8px 15px;">
  <div class="box-body">
    <form id="form-advanced-search" class="form-inline" role="form" style="width:100%">
      <div class="form-group" style="margin-right:8px; width:28%;">
        <label class="sr-only" for="adv_nombre">Nombre</label>
        <input type="text" name="adv_nombre" class="form-control input-md adv-nombre" placeholder="Nombre" style="width:100%">
      </div>
      <div class="form-group" style="margin-right:8px; width:18%;">
        <label class="sr-only" for="adv_telefono">Teléfono</label>
        <input type="text" name="adv_telefono" class="form-control input-md adv-telefono" placeholder="Teléfono" style="width:100%">
      </div>
      <div class="form-group" style="margin-right:8px; width:18%;">
        <label class="sr-only" for="adv_documento">Documento</label>
        <input type="text" name="adv_documento" class="form-control input-md adv-documento" placeholder="DNI/RUC" style="width:100%">
      </div>
      <div class="form-group" style="margin-right:8px; width:18%;">
        <label class="sr-only" for="adv_periodo">Periodo</label>
        <select name="adv_periodo" class="form-control input-md adv-periodo" style="width:100%">
          <option value="">Periodo</option>
          <option value="today">Hoy</option>
          <option value="this_week">Esta semana</option>
          <option value="this_month">Este mes</option>
          <option value="custom">Rango personalizado</option>
        </select>
      </div>
      <div class="adv_custom_dates" style="display:none; margin-right:8px; width:40%;">
        <div class="form-group" style="margin-right:8px; width:49%; display:inline-block;">
          <label class="sr-only" for="adv_fecha_inicio">Desde</label>
          <input type="date" name="adv_fecha_inicio" class="form-control input-md adv-fecha-inicio" style="width:100%">
        </div>
        <div class="form-group" style="width:49%; display:inline-block;">
          <label class="sr-only" for="adv_fecha_fin">Hasta</label>
          <input type="date" name="adv_fecha_fin" class="form-control input-md adv-fecha-fin" style="width:100%">
        </div>
      </div>

      <div class="pull-right" style="margin-left:8px;">
        <button type="button" class="btn btn-primary adv-clear">Limpiar</button>
        <button type="submit" class="btn btn-primary adv-apply">Buscar</button>
        <button type="button" class="btn btn-default btn-close-advanced-search">Cerrar</button>
      </div>
    </form>
  </div>
</div>

<!-- Compact toggle bar (visible) placed to mimic header area) -->
<div id="advanced-search-bar" style="margin: 10px 15px;">
  <a href="#" id="btn-toggle-advanced-search" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> <strong style="margin-left:6px;">Búsqueda Avanzada</strong></a>
</div>

<!-- Inline panel that expands in-place when the toggle is clicked -->
<div id="advanced-search-panel-inline" style="display:none; margin: 10px 15px;">
  <div class="box box-solid" style="border-left:4px solid #00a65a;">
        <div class="box-body" style="background:#fff;">
      <form id="form-advanced-search-inline" class="form-inline" role="form" style="width:100%">
        <div class="form-group" style="margin-right:8px; width:28%;">
          <label class="sr-only" for="adv_nombre">Nombre</label>
          <input type="text" name="adv_nombre" class="form-control input-md adv-nombre" placeholder="Nombre" style="width:100%">
        </div>
        <div class="form-group" style="margin-right:8px; width:18%;">
          <label class="sr-only" for="adv_telefono">Teléfono</label>
          <input type="text" name="adv_telefono" class="form-control input-md adv-telefono" placeholder="Teléfono" style="width:100%">
        </div>
        <div class="form-group" style="margin-right:8px; width:18%;">
          <label class="sr-only" for="adv_documento">Documento</label>
          <input type="text" name="adv_documento" class="form-control input-md adv-documento" placeholder="DNI/RUC" style="width:100%">
        </div>
        <div class="form-group" style="margin-right:8px; width:18%;">
          <label class="sr-only" for="adv_periodo">Periodo</label>
          <select name="adv_periodo" class="form-control input-md adv-periodo" style="width:100%">
            <option value="">Periodo</option>
            <option value="today">Hoy</option>
            <option value="this_week">Esta semana</option>
            <option value="this_month">Este mes</option>
            <option value="custom">Rango personalizado</option>
          </select>
        </div>
        <div class="adv_custom_dates" style="display:none; margin-right:8px; width:40%;">
          <div class="form-group" style="margin-right:8px; width:49%; display:inline-block;">
            <label class="sr-only" for="adv_fecha_inicio">Desde</label>
            <input type="date" name="adv_fecha_inicio" class="form-control input-md adv-fecha-inicio" style="width:100%">
          </div>
          <div class="form-group" style="width:49%; display:inline-block;">
            <label class="sr-only" for="adv_fecha_fin">Hasta</label>
            <input type="date" name="adv_fecha_fin" class="form-control input-md adv-fecha-fin" style="width:100%">
          </div>
        </div>

        <div class="pull-right" style="margin-left:8px;">
          <button type="button" class="btn btn-default adv-clear">Limpiar</button>
          <button type="submit" class="btn btn-primary adv-apply">Buscar</button>
          <button type="button" class="btn btn-default btn-close-advanced-search">Cerrar</button>
        </div>
      </form>
    </div>
  </div>
</div>

    <!-- Inline debug helper: logs form submit even if external JS fails to load -->
    <script>
      (function(){
        try {
          document.addEventListener('DOMContentLoaded', function(){
            var fTop = document.getElementById('form-advanced-search');
            var fInline = document.getElementById('form-advanced-search-inline');
            function buildFilters(form){
              try {
                function clean(v){
                  try { return (v||'').toString().replace(/[\t\r\n]+/g,' ').trim(); } catch(e) { return ''; }
                }
                return {
                  nombre: clean(form.querySelector('[name=adv_nombre]') && form.querySelector('[name=adv_nombre]').value),
                  telefono: clean(form.querySelector('[name=adv_telefono]') && form.querySelector('[name=adv_telefono]').value),
                  documento: clean(form.querySelector('[name=adv_documento]') && form.querySelector('[name=adv_documento]').value),
                  periodo: clean(form.querySelector('[name=adv_periodo]') && form.querySelector('[name=adv_periodo]').value),
                  fecha_inicio: clean(form.querySelector('[name=adv_fecha_inicio]') && form.querySelector('[name=adv_fecha_inicio]').value),
                  fecha_fin: clean(form.querySelector('[name=adv_fecha_fin]') && form.querySelector('[name=adv_fecha_fin]').value)
                };
              } catch(e){ return {}; }
            }

            function logSubmit(e){
              try {
                var _dev = (window.PLANTILLA_DEV === true);
                if (_dev) { try { console.log('advanced_search-inline-debug: submit on', this && this.id, 'target:', e && e.target); } catch(err){} }
              } catch(ignore){}
              try {
                var filters = buildFilters(this || e.target || document.getElementById('form-advanced-search-inline'));
                if (window.PLANTILLA_DEV === true) { try { console.log('advanced_search-inline-debug: built filters', filters); } catch(e){} }
                try { window.dispatchEvent(new CustomEvent('advancedSearch:apply', { detail: filters })); if (window.PLANTILLA_DEV === true) try { console.log('advanced_search-inline-debug: dispatched advancedSearch:apply'); } catch(e){} } catch(ex) { if (window.PLANTILLA_DEV === true) console.warn('dispatch failed', ex); }
              } catch(err){ if (window.PLANTILLA_DEV === true) console.warn('advanced_search-inline-debug error building/dispatching', err); }
            }
            if (fTop && !fTop._dbgAttached) { fTop.addEventListener('submit', logSubmit); fTop._dbgAttached = true; }
            if (fInline && !fInline._dbgAttached) { fInline.addEventListener('submit', logSubmit); fInline._dbgAttached = true; }

            // jQuery fallback: attach direct click handler to the Buscar button(s) to ensure event fires
            try {
              if (window.jQuery) {
                (function($){
                  $(document).on('click', '.adv-apply', function(ev){
                    ev.preventDefault();
                    var $form = $(this).closest('form');
                    var filters = buildFilters($form[0]);
                    try { if (window.PLANTILLA_DEV === true) console.log('advanced_search-jquery-fallback: dispatching', filters); } catch(e){}
                    try { window.dispatchEvent(new CustomEvent('advancedSearch:apply', { detail: filters })); } catch(ex) { if (window.PLANTILLA_DEV === true) console.warn('dispatch failed (jquery fallback)', ex); }
                  });
                })(window.jQuery);
              }
            } catch(e){ console.warn('advanced_search jquery fallback failed', e); }
          });
        } catch(e){ console.warn('advanced_search debug helper failed', e); }
      })();
    </script>

