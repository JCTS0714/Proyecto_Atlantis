<?php
/**
 * Partial: Advanced Search panel (reusable across pages)
 * Este componente es solo HTML/CSS, la lógica está en advanced_search.js
 * Envuelto en try-catch para evitar que errores bloqueen el render de la página
 */
?>
<!-- Advanced Search Panel - Barra compacta visible -->
<div id="advanced-search-container" class="advanced-search-wrapper" style="margin-bottom: 15px;">
  <div id="advanced-search-bar">
    <button type="button" id="btn-toggle-advanced-search" class="btn btn-success btn-sm">
      <i class="fa fa-search"></i> <strong style="margin-left:6px;">Búsqueda Avanzada</strong>
    </button>
  </div>

  <!-- Panel expandible de búsqueda avanzada -->
  <div id="advanced-search-panel-inline" style="display:none; margin-top: 10px;">
    <div class="box box-solid" style="border-left:4px solid #00a65a; margin-bottom: 0;">
      <div class="box-body" style="background:#fff; padding: 15px;">
        <form id="form-advanced-search-inline" class="form-horizontal" role="form">
          <div class="row">
            <div class="col-md-3 col-sm-6">
              <div class="form-group" style="margin-bottom: 10px;">
                <input type="text" name="adv_nombre" class="form-control input-sm adv-nombre" placeholder="Nombre">
              </div>
            </div>
            <div class="col-md-2 col-sm-6">
              <div class="form-group" style="margin-bottom: 10px;">
                <input type="text" name="adv_telefono" class="form-control input-sm adv-telefono" placeholder="Teléfono">
              </div>
            </div>
            <div class="col-md-2 col-sm-6">
              <div class="form-group" style="margin-bottom: 10px;">
                <input type="text" name="adv_documento" class="form-control input-sm adv-documento" placeholder="DNI/RUC">
              </div>
            </div>
            <div class="col-md-2 col-sm-6">
              <div class="form-group" style="margin-bottom: 10px;">
                <select name="adv_periodo" class="form-control input-sm adv-periodo">
                  <option value="">Periodo</option>
                  <option value="today">Hoy</option>
                  <option value="this_week">Esta semana</option>
                  <option value="this_month">Este mes</option>
                  <option value="custom">Personalizado</option>
                </select>
              </div>
            </div>
            <div class="col-md-3 col-sm-12">
              <div class="form-group" style="margin-bottom: 10px;">
                <button type="submit" class="btn btn-primary btn-sm adv-apply"><i class="fa fa-search"></i> Buscar</button>
                <button type="button" class="btn btn-default btn-sm adv-clear"><i class="fa fa-eraser"></i> Limpiar</button>
                <button type="button" class="btn btn-default btn-sm btn-close-advanced-search"><i class="fa fa-times"></i></button>
              </div>
            </div>
          </div>
          <!-- Fechas personalizadas (ocultas por defecto) -->
          <div class="row adv_custom_dates" style="display:none;">
            <div class="col-md-3 col-sm-6">
              <div class="form-group" style="margin-bottom: 10px;">
                <label class="control-label" style="font-size: 12px;">Desde:</label>
                <input type="date" name="adv_fecha_inicio" class="form-control input-sm adv-fecha-inicio">
              </div>
            </div>
            <div class="col-md-3 col-sm-6">
              <div class="form-group" style="margin-bottom: 10px;">
                <label class="control-label" style="font-size: 12px;">Hasta:</label>
                <input type="date" name="adv_fecha_fin" class="form-control input-sm adv-fecha-fin">
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>