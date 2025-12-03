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
      <div class="box-body" style="background:#fff; padding: 20px;">
        <form id="form-advanced-search-inline" class="form-horizontal" role="form">
          
          <!-- Primera fila: Campos de texto -->
          <div class="row">
            <div class="col-md-4 col-sm-6">
              <div class="form-group" style="margin-bottom: 15px;">
                <label class="control-label" style="font-size: 12px; font-weight: 600; color: #555; margin-bottom: 5px; display: block;">
                  <i class="fa fa-user" style="margin-right: 5px; color: #3c8dbc;"></i>Nombre
                </label>
                <input type="text" name="adv_nombre" class="form-control input-sm adv-nombre" placeholder="Buscar por nombre...">
              </div>
            </div>
            <div class="col-md-4 col-sm-6">
              <div class="form-group" style="margin-bottom: 15px;">
                <label class="control-label" style="font-size: 12px; font-weight: 600; color: #555; margin-bottom: 5px; display: block;">
                  <i class="fa fa-phone" style="margin-right: 5px; color: #3c8dbc;"></i>Teléfono
                </label>
                <input type="text" name="adv_telefono" class="form-control input-sm adv-telefono" placeholder="Buscar por teléfono...">
              </div>
            </div>
            <div class="col-md-4 col-sm-6">
              <div class="form-group" style="margin-bottom: 15px;">
                <label class="control-label" style="font-size: 12px; font-weight: 600; color: #555; margin-bottom: 5px; display: block;">
                  <i class="fa fa-id-card" style="margin-right: 5px; color: #3c8dbc;"></i>DNI/RUC
                </label>
                <input type="text" name="adv_documento" class="form-control input-sm adv-documento" placeholder="Buscar por documento...">
              </div>
            </div>
          </div>

          <!-- Segunda fila: Filtro de periodo y tipo de fecha -->
          <div class="row">
            <div class="col-md-4 col-sm-6">
              <div class="form-group" style="margin-bottom: 15px;">
                <label class="control-label" style="font-size: 12px; font-weight: 600; color: #555; margin-bottom: 5px; display: block;">
                  <i class="fa fa-calendar" style="margin-right: 5px; color: #3c8dbc;"></i>Filtrar por fecha de:
                </label>
                <div class="btn-group btn-group-sm" data-toggle="buttons" style="width: 100%;">
                  <label class="btn btn-default active" style="width: 50%;">
                    <input type="radio" name="adv_tipo_fecha" value="fecha_creacion" checked> 
                    <i class="fa fa-plus-circle"></i> Creación
                  </label>
                  <label class="btn btn-default" style="width: 50%;">
                    <input type="radio" name="adv_tipo_fecha" value="fecha_contacto"> 
                    <i class="fa fa-phone-square"></i> Contacto
                  </label>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6">
              <div class="form-group" style="margin-bottom: 15px;">
                <label class="control-label" style="font-size: 12px; font-weight: 600; color: #555; margin-bottom: 5px; display: block;">
                  <i class="fa fa-clock-o" style="margin-right: 5px; color: #3c8dbc;"></i>Periodo
                </label>
                <select name="adv_periodo" class="form-control input-sm adv-periodo">
                  <option value="">Todos los periodos</option>
                  <option value="today">Hoy</option>
                  <option value="yesterday">Ayer</option>
                  <option value="this_week">Esta semana</option>
                  <option value="last_week">Semana pasada</option>
                  <option value="this_month">Este mes</option>
                  <option value="last_month">Mes pasado</option>
                  <option value="this_year">Este año</option>
                  <option value="custom">Personalizado...</option>
                </select>
              </div>
            </div>
            <div class="col-md-4 col-sm-12">
              <div class="form-group" style="margin-bottom: 15px;">
                <label class="control-label" style="font-size: 12px; font-weight: 600; color: #555; margin-bottom: 5px; display: block;">
                  &nbsp;
                </label>
                <div class="btn-group" style="width: 100%;">
                  <button type="submit" class="btn btn-primary btn-sm adv-apply" style="width: 40%;">
                    <i class="fa fa-search"></i> Buscar
                  </button>
                  <button type="button" class="btn btn-warning btn-sm adv-clear" style="width: 35%;">
                    <i class="fa fa-eraser"></i> Limpiar
                  </button>
                  <button type="button" class="btn btn-default btn-sm btn-close-advanced-search" style="width: 25%;">
                    <i class="fa fa-times"></i> Cerrar
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Tercera fila: Fechas personalizadas (ocultas por defecto) -->
          <div class="row adv_custom_dates" style="display:none; background: #f9f9f9; padding: 15px; border-radius: 4px; margin-top: 10px;">
            <div class="col-md-4 col-sm-6">
              <div class="form-group" style="margin-bottom: 0;">
                <label class="control-label" style="font-size: 12px; font-weight: 600; color: #555; margin-bottom: 5px; display: block;">
                  <i class="fa fa-calendar-check-o" style="margin-right: 5px; color: #00a65a;"></i>Fecha Inicio
                </label>
                <input type="date" name="adv_fecha_inicio" class="form-control input-sm adv-fecha-inicio">
              </div>
            </div>
            <div class="col-md-4 col-sm-6">
              <div class="form-group" style="margin-bottom: 0;">
                <label class="control-label" style="font-size: 12px; font-weight: 600; color: #555; margin-bottom: 5px; display: block;">
                  <i class="fa fa-calendar-times-o" style="margin-right: 5px; color: #dd4b39;"></i>Fecha Fin
                </label>
                <input type="date" name="adv_fecha_fin" class="form-control input-sm adv-fecha-fin">
              </div>
            </div>
            <div class="col-md-4 col-sm-12">
              <div class="form-group" style="margin-bottom: 0;">
                <label class="control-label" style="font-size: 12px; color: #777; margin-bottom: 5px; display: block;">
                  <i class="fa fa-info-circle" style="margin-right: 5px;"></i>Información
                </label>
                <p class="text-muted" style="font-size: 11px; margin: 0; padding-top: 5px;">
                  Seleccione el rango de fechas para filtrar los registros según la fecha elegida arriba.
                </p>
              </div>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>