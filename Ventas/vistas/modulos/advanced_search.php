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

