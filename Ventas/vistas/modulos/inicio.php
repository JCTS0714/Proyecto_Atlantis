<!-- =============================================== -->

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Dashboard de Ventas
      <small>Panel de control y métricas</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Dashboard</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    
    <!-- Indicadores Clave -->
    <div class="row">
      <!-- Clientes Ganados -->
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box" id="indicador-clientes-ganados">
          <span class="info-box-icon bg-green"><i class="fa fa-users"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Clientes Ganados</span>
            <span class="info-box-number indicador-valor">0</span>
            <span class="info-box-text">Este mes</span>
          </div>
        </div>
      </div>
      
      <!-- Prospectos -->
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box" id="indicador-prospectos">
          <span class="info-box-icon bg-aqua"><i class="fa fa-user-plus"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Nuevos Prospectos</span>
            <span class="info-box-number indicador-valor">0</span>
            <span class="info-box-text">
              <span class="indicador-variacion text-muted">0%</span> vs mes anterior
            </span>
          </div>
        </div>
      </div>
      
      <!-- Clientes Perdidos -->
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box" id="indicador-clientes-perdidos">
          <span class="info-box-icon bg-red"><i class="fa fa-user-times"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Clientes Perdidos</span>
            <span class="info-box-number indicador-valor">0</span>
            <span class="info-box-text">Este mes</span>
          </div>
        </div>
      </div>
      
      <!-- Reuniones -->
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box" id="indicador-reuniones">
          <span class="info-box-icon bg-yellow"><i class="fa fa-calendar"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Reuniones</span>
            <span class="info-box-number indicador-valor">0</span>
            <span class="info-box-text">Esta semana</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Gráficos y Contenido Principal -->
    <div class="row">
      <!-- Gráfico de Clientes por Estado -->
      <div class="col-md-6">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Distribución de Clientes</h3>
            <div class="box-tools pull-right">
              <div class="btn-group">
                <button type="button" class="btn btn-default btn-xs btn-periodo active" data-periodo="mensual">Mensual</button>
                <button type="button" class="btn btn-default btn-xs btn-periodo" data-periodo="semanal">Semanal</button>
              </div>
            </div>
          </div>
          <div class="box-body">
            <div class="chart-container" style="position: relative; height: 300px;">
              <canvas id="grafico-clientes"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Reuniones de la Semana -->
      <div class="col-md-6">
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">Reuniones Esta Semana</h3>
          </div>
          <div class="box-body">
            <div class="reuniones-container" style="max-height: 300px; overflow-y: auto;">
              <ul class="list-group" id="lista-reuniones">
                <li class="list-group-item text-center text-muted">Cargando reuniones...</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Evolución Mensual -->
    <div class="row">
      <div class="col-md-12">
        <div class="box box-success">
          <div class="box-header with-border">
            <h3 class="box-title">Evolución Mensual</h3>
          </div>
          <div class="box-body">
            <div class="chart-container" style="position: relative; height: 300px;">
              <canvas id="grafico-evolucion"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Resumen Rápido -->
    <div class="row">
      <div class="col-md-4">
        <div class="small-box bg-green">
          <div class="inner">
            <h3 id="resumen-prospectos">0</h3>
            <p>Total Prospectos</p>
          </div>
          <div class="icon">
            <i class="fa fa-user-plus"></i>
          </div>
          <a href="prospectos" class="small-box-footer">
            Ver todos <i class="fa fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="small-box bg-blue">
          <div class="inner">
            <h3 id="resumen-clientes">0</h3>
            <p>Total Clientes</p>
          </div>
          <div class="icon">
            <i class="fa fa-users"></i>
          </div>
          <a href="clientes" class="small-box-footer">
            Ver todos <i class="fa fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="small-box bg-yellow">
          <div class="inner">
            <h3 id="resumen-reuniones">0</h3>
            <p>Reuniones Totales</p>
          </div>
          <div class="icon">
            <i class="fa fa-calendar"></i>
          </div>
          <a href="calendario" class="small-box-footer">
            Ver calendario <i class="fa fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>
    </div>

  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- Incluir JavaScript del Dashboard -->
<script src="vistas/js/dashboard.js"></script>
