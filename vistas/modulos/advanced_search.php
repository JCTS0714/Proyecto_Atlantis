<?php
/**
 * Partial: Advanced Search panel (reusable across pages)
 * Este componente es solo HTML/CSS, la lógica está en advanced_search.js
 * Envuelto en try-catch para evitar que errores bloqueen el render de la página
 */
?>
<!-- Advanced Search Panel - Barra compacta visible -->
<style>
  /* Estilos para búsqueda avanzada - Compacto */
  .adv-search-label {
    font-size: 11px;
    font-weight: 600;
    color: #555;
    margin-bottom: 2px;
    display: block;
    text-align: left;
  }
  .adv-search-label i {
    margin-right: 4px;
    color: #3c8dbc;
  }
  .adv-form-group {
    margin-bottom: 0;
    padding: 4px 8px;
  }
  .adv-form-group .form-control {
    height: 28px;
    padding: 3px 8px;
    font-size: 12px;
  }
  .adv-search-row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -8px;
  }
  .adv-search-col {
    flex: 1;
    min-width: 150px;
    padding: 0;
  }
  .adv-tipo-fecha-group {
    display: inline-flex;
    gap: 0;
  }
  .adv-tipo-fecha-group .btn {
    padding: 4px 8px;
    font-size: 11px;
  }
  .adv-buttons-group {
    display: flex;
    gap: 5px;
  }
  .adv-buttons-group .btn {
    flex: 0 0 auto;
    white-space: nowrap;
    padding: 4px 10px;
    font-size: 11px;
  }
  #advanced-search-panel-inline .box-body {
    padding: 8px 10px !important;
  }
  /* Estilos para filas de periodo */
  .adv_por_mes, .adv_entre_meses, .adv_por_fecha, .adv_entre_fechas {
    background: transparent !important;
    border-left: 3px solid #3c8dbc;
    padding: 8px 0 !important;
    margin-left: 8px;
  }
  .adv_por_mes .adv-search-col,
  .adv_entre_meses .adv-search-col,
  .adv_por_fecha .adv-search-col,
  .adv_entre_fechas .adv-search-col {
    flex: 0 0 auto;
    max-width: 200px;
  }
  .adv_por_mes .adv-search-col:last-child,
  .adv_entre_meses .adv-search-col:last-child,
  .adv_por_fecha .adv-search-col:last-child,
  .adv_entre_fechas .adv-search-col:last-child {
    flex: 1;
    max-width: none;
  }
  @media (max-width: 768px) {
    .adv-search-col {
      flex: 0 0 100%;
    }
    .adv_por_mes .adv-search-col,
    .adv_entre_meses .adv-search-col,
    .adv_por_fecha .adv-search-col,
    .adv_entre_fechas .adv-search-col {
      max-width: 100%;
    }
  }
</style>
<div id="advanced-search-container" class="advanced-search-wrapper" style="margin-bottom: 15px;">
  <div id="advanced-search-bar">
    <button type="button" id="btn-toggle-advanced-search" class="btn btn-success btn-sm">
      <i class="fa fa-search"></i> <strong style="margin-left:6px;">Búsqueda Avanzada</strong>
    </button>
  </div>

  <!-- Panel expandible de búsqueda avanzada -->
  <div id="advanced-search-panel-inline" style="display:none; margin-top: 10px;">
    <div class="box box-solid" style="border-left:4px solid #00a65a; margin-bottom: 0;">
      <div class="box-body" style="background:#fff; padding: 15px 10px;">
        <form id="form-advanced-search-inline" role="form">
          
          <!-- Primera fila: Campos de texto -->
          <div class="adv-search-row">
            <div class="adv-search-col">
              <div class="adv-form-group">
                <label class="adv-search-label">
                  <i class="fa fa-user"></i>Nombre
                </label>
                <input type="text" name="adv_nombre" class="form-control input-sm adv-nombre" placeholder="Buscar por nombre...">
              </div>
            </div>
            <div class="adv-search-col">
              <div class="adv-form-group">
                <label class="adv-search-label">
                  <i class="fa fa-phone"></i>Teléfono
                </label>
                <input type="text" name="adv_telefono" class="form-control input-sm adv-telefono" placeholder="Buscar por teléfono...">
              </div>
            </div>
            <div class="adv-search-col">
              <div class="adv-form-group">
                <label class="adv-search-label">
                  <i class="fa fa-id-card"></i>DNI/RUC
                </label>
                <input type="text" name="adv_documento" class="form-control input-sm adv-documento" placeholder="Buscar por documento...">
              </div>
            </div>
            <!-- Filtro de servidor (solo visible en clientes y contadores) -->
            <div class="adv-search-col adv-servidor-filter" style="display:none;">
              <div class="adv-form-group">
                <label class="adv-search-label">
                  <i class="fa fa-server"></i>Servidor
                </label>
                <select name="adv_servidor" class="form-control input-sm adv-servidor">
                  <option value="">Todos los servidores</option>
                  <option value="LORITO">LORITO</option>
                  <option value="ATLANTIS FAST">ATLANTIS FAST</option>
                  <option value="ATLANTIS POS">ATLANTIS POS</option>
                  <option value="ATLANTIS ONLINE">ATLANTIS ONLINE</option>
                  <option value="ATLANTIS VIP">ATLANTIS VIP</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Segunda fila: Filtro de periodo y tipo de fecha -->
          <div class="adv-search-row" style="margin-top: 10px;">
            <div class="adv-search-col">
              <div class="adv-form-group">
                <label class="adv-search-label">
                  <i class="fa fa-calendar"></i>Filtrar por fecha de:
                </label>
                <div class="adv-tipo-fecha-group btn-group btn-group-sm" data-toggle="buttons">
                  <label class="btn btn-default active">
                    <input type="radio" name="adv_tipo_fecha" value="fecha_creacion" checked> 
                    <i class="fa fa-plus-circle"></i> Creación
                  </label>
                  <label class="btn btn-default">
                    <input type="radio" name="adv_tipo_fecha" value="fecha_contacto"> 
                    <i class="fa fa-phone-square"></i> Contacto
                  </label>
                </div>
              </div>
            </div>
            <div class="adv-search-col">
              <div class="adv-form-group">
                <label class="adv-search-label">
                  <i class="fa fa-clock-o"></i>Periodo
                </label>
                <select name="adv_periodo" class="form-control input-sm adv-periodo">
                  <option value="">Todos los periodos</option>
                  <option value="por_mes">Por mes</option>
                  <option value="entre_meses">Entre meses</option>
                  <option value="por_fecha">Por fecha</option>
                  <option value="entre_fechas">Entre fechas</option>
                </select>
              </div>
            </div>
            <div class="adv-search-col">
              <div class="adv-form-group">
                <label class="adv-search-label">&nbsp;</label>
                <div class="adv-buttons-group">
                  <button type="submit" class="btn btn-primary btn-sm adv-apply">
                    <i class="fa fa-search"></i> Buscar
                  </button>
                  <button type="button" class="btn btn-warning btn-sm adv-clear">
                    <i class="fa fa-eraser"></i> Limpiar
                  </button>
                  <button type="button" class="btn btn-default btn-sm btn-close-advanced-search">
                    <i class="fa fa-times"></i> Cerrar
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Tercera fila: Campos dinámicos según periodo seleccionado -->
          <!-- Por mes: Un selector de mes/año -->
          <div class="adv-search-row adv_por_mes" style="display:none; margin-top: 8px;">
            <div class="adv-search-col">
              <div class="adv-form-group">
                <label class="adv-search-label">
                  <i class="fa fa-calendar-o" style="color: #3c8dbc;"></i>Mes
                </label>
                <input type="month" name="adv_mes_unico" class="form-control input-sm adv-mes-unico">
              </div>
            </div>
            <div class="adv-search-col">
              <div class="adv-form-group">
                <label class="adv-search-label" style="color: #777;">
                  <i class="fa fa-info-circle"></i>Información
                </label>
                <p class="text-muted" style="font-size: 11px; margin: 0;">
                  Seleccione el mes para filtrar.
                </p>
              </div>
            </div>
          </div>

          <!-- Entre meses: Dos selectores de mes/año -->
          <div class="adv-search-row adv_entre_meses" style="display:none; margin-top: 8px;">
            <div class="adv-search-col">
              <div class="adv-form-group">
                <label class="adv-search-label">
                  <i class="fa fa-calendar-check-o" style="color: #00a65a;"></i>Mes desde
                </label>
                <input type="month" name="adv_mes_desde" class="form-control input-sm adv-mes-desde">
              </div>
            </div>
            <div class="adv-search-col">
              <div class="adv-form-group">
                <label class="adv-search-label">
                  <i class="fa fa-calendar-times-o" style="color: #dd4b39;"></i>Mes hasta
                </label>
                <input type="month" name="adv_mes_hasta" class="form-control input-sm adv-mes-hasta">
              </div>
            </div>
            <div class="adv-search-col">
              <div class="adv-form-group">
                <label class="adv-search-label" style="color: #777;">
                  <i class="fa fa-info-circle"></i>Información
                </label>
                <p class="text-muted" style="font-size: 11px; margin: 0;">
                  Seleccione el rango de meses para filtrar.
                </p>
              </div>
            </div>
          </div>

          <!-- Por fecha: Un campo de fecha única -->
          <div class="adv-search-row adv_por_fecha" style="display:none; margin-top: 8px;">
            <div class="adv-search-col">
              <div class="adv-form-group">
                <label class="adv-search-label">
                  <i class="fa fa-calendar-o" style="color: #3c8dbc;"></i>Fecha
                </label>
                <input type="date" name="adv_fecha_unica" class="form-control input-sm adv-fecha-unica">
              </div>
            </div>
            <div class="adv-search-col">
              <div class="adv-form-group">
                <label class="adv-search-label" style="color: #777;">
                  <i class="fa fa-info-circle"></i>Información
                </label>
                <p class="text-muted" style="font-size: 11px; margin: 0;">
                  Seleccione la fecha para filtrar.
                </p>
              </div>
            </div>
          </div>

          <!-- Entre fechas: Dos campos de fecha -->
          <div class="adv-search-row adv_entre_fechas" style="display:none; margin-top: 8px;">
            <div class="adv-search-col">
              <div class="adv-form-group">
                <label class="adv-search-label">
                  <i class="fa fa-calendar-check-o" style="color: #00a65a;"></i>Fecha desde
                </label>
                <input type="date" name="adv_fecha_inicio" class="form-control input-sm adv-fecha-inicio">
              </div>
            </div>
            <div class="adv-search-col">
              <div class="adv-form-group">
                <label class="adv-search-label">
                  <i class="fa fa-calendar-times-o" style="color: #dd4b39;"></i>Fecha hasta
                </label>
                <input type="date" name="adv_fecha_fin" class="form-control input-sm adv-fecha-fin">
              </div>
            </div>
            <div class="adv-search-col">
              <div class="adv-form-group">
                <label class="adv-search-label" style="color: #777;">
                  <i class="fa fa-info-circle"></i>Información
                </label>
                <p class="text-muted" style="font-size: 11px; margin: 0;">
                  Seleccione el rango de fechas para filtrar.
                </p>
              </div>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>