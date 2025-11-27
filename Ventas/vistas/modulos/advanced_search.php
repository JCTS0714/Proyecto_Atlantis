<?php
// Partial: Advanced Search panel (reusable across pages)
?>
<!-- Advanced Search Toggle & Panel -->
<div id="advanced-search-container" style="position:relative; z-index:1050;">
  <button id="btn-toggle-advanced-search" class="btn btn-default" style="position:fixed; right:18px; bottom:18px; z-index:1100; border-radius:50%; width:56px; height:56px; box-shadow:0 6px 18px rgba(0,0,0,0.12);">
    <i class="fa fa-search"></i>
  </button>

  <div id="advanced-search-panel" class="panel panel-default" style="position:fixed; right:18px; bottom:86px; width:340px; display:none; z-index:1100;">
    <div class="panel-heading" style="cursor:move;">
      <strong>Búsqueda avanzada</strong>
      <button type="button" id="btn-close-advanced-search" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
    <div class="panel-body">
      <form id="form-advanced-search" class="form-horizontal" role="form">
        <div class="form-group">
          <label class="control-label">Nombre</label>
          <input type="text" id="adv_nombre" class="form-control" placeholder="Nombre del prospecto">
        </div>
        <div class="form-group">
          <label class="control-label">Teléfono</label>
          <input type="text" id="adv_telefono" class="form-control" placeholder="Teléfono">
        </div>
        <div class="form-group">
          <label class="control-label">Documento</label>
          <input type="text" id="adv_documento" class="form-control" placeholder="Documento">
        </div>

        <div class="form-group">
          <label class="control-label">Periodo</label>
          <select id="adv_periodo" class="form-control">
            <option value="">(Cualquiera)</option>
            <option value="today">Hoy</option>
            <option value="this_week">Esta semana</option>
            <option value="this_month">Este mes</option>
            <option value="custom">Rango personalizado</option>
          </select>
        </div>

        <div id="adv_custom_dates" style="display:none;">
          <div class="form-group">
            <label class="control-label">Desde</label>
            <input type="date" id="adv_fecha_inicio" class="form-control">
          </div>
          <div class="form-group">
            <label class="control-label">Hasta</label>
            <input type="date" id="adv_fecha_fin" class="form-control">
          </div>
        </div>

        <div class="form-group text-right">
          <button type="button" id="adv_clear" class="btn btn-default">Limpiar</button>
          <button type="submit" id="adv_apply" class="btn btn-primary">Buscar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- End Advanced Search -->
