<!-- =============================================
     TARJETA DE CONTEO DE REGISTROS POR PERIODO
     Parámetros esperados:
     - $conteoEstado: número de estado (0=prospecto, 1=seguimiento, 2=cliente, 3=no cliente, 4=zona espera)
     - $conteoTitulo: título para mostrar en la tarjeta
     - $conteoColor: color de la tarjeta (aqua, green, yellow, red, blue)
     - $conteoIcono: icono FontAwesome (fa-users, fa-user-plus, etc.)
============================================= -->
<?php
// Valores por defecto
$conteoEstado = isset($conteoEstado) ? $conteoEstado : 0;
$conteoTitulo = isset($conteoTitulo) ? $conteoTitulo : 'Registros';
$conteoColor = isset($conteoColor) ? $conteoColor : 'aqua';
$conteoIcono = isset($conteoIcono) ? $conteoIcono : 'fa-users';
$conteoId = 'conteo-' . $conteoEstado; // ID único para cada tarjeta
?>

<div class="row" style="margin-top: 20px;">
  <div class="col-md-6 col-lg-4">
    <div class="small-box bg-<?php echo $conteoColor; ?>" id="box-<?php echo $conteoId; ?>">
      <div class="inner">
        <h3 id="contador-<?php echo $conteoId; ?>">
          <i class="fa fa-spinner fa-spin"></i>
        </h3>
        <p id="titulo-<?php echo $conteoId; ?>"><?php echo $conteoTitulo; ?></p>
        <small id="rango-<?php echo $conteoId; ?>" class="text-muted" style="color: rgba(255,255,255,0.8);"></small>
      </div>
      <div class="icon">
        <i class="fa <?php echo $conteoIcono; ?>"></i>
      </div>
      <div class="small-box-footer" style="background: rgba(0,0,0,0.1); padding: 10px;">
        <div class="row">
          <div class="col-xs-8 col-sm-7">
            <select class="form-control input-sm select-periodo-conteo" 
                    data-estado="<?php echo $conteoEstado; ?>" 
                    data-conteo-id="<?php echo $conteoId; ?>"
                    style="font-size: 12px;">
              <option value="todo">Todos los registros</option>
              <option value="hoy">Hoy</option>
              <option value="ayer">Ayer</option>
              <option value="esta_semana">Esta semana</option>
              <option value="semana_pasada">Semana pasada</option>
              <option value="este_mes" selected>Este mes</option>
              <option value="mes_pasado">Mes pasado</option>
              <option value="este_ano">Este año</option>
              <option value="ano_pasado">Año pasado</option>
              <option value="personalizado">Personalizado...</option>
            </select>
          </div>
          <div class="col-xs-4 col-sm-5 text-right">
            <button class="btn btn-xs btn-default btn-actualizar-conteo" 
                    data-estado="<?php echo $conteoEstado; ?>" 
                    data-conteo-id="<?php echo $conteoId; ?>"
                    title="Actualizar">
              <i class="fa fa-refresh"></i>
            </button>
          </div>
        </div>
        <!-- Campos de fecha personalizada (ocultos por defecto) -->
        <div class="fecha-personalizada-container" id="fechas-<?php echo $conteoId; ?>" style="display: none; margin-top: 10px;">
          <div class="row">
            <div class="col-xs-6">
              <input type="date" class="form-control input-sm fecha-inicio-conteo" 
                     data-conteo-id="<?php echo $conteoId; ?>"
                     placeholder="Desde">
            </div>
            <div class="col-xs-6">
              <input type="date" class="form-control input-sm fecha-fin-conteo" 
                     data-conteo-id="<?php echo $conteoId; ?>"
                     placeholder="Hasta">
            </div>
          </div>
          <div class="row" style="margin-top: 5px;">
            <div class="col-xs-12">
              <button class="btn btn-xs btn-primary btn-block btn-aplicar-fecha" 
                      data-estado="<?php echo $conteoEstado; ?>"
                      data-conteo-id="<?php echo $conteoId; ?>">
                <i class="fa fa-check"></i> Aplicar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Función para actualizar el conteo de registros (definir solo una vez)
if (typeof actualizarConteoRegistros !== 'function') {
  window.actualizarConteoRegistros = function(estado, periodo, conteoId, fechaInicio, fechaFin) {
    fechaInicio = fechaInicio || null;
    fechaFin = fechaFin || null;
    
    var $contador = $('#contador-' + conteoId);
    var $rango = $('#rango-' + conteoId);
    
    // Mostrar loading
    $contador.html('<i class="fa fa-spinner fa-spin"></i>');
    
    // Construir URL
    var url = 'ajax/conteo_registros.ajax.php?estado=' + estado + '&periodo=' + periodo;
    if (periodo === 'personalizado' && fechaInicio && fechaFin) {
      url += '&fecha_inicio=' + fechaInicio + '&fecha_fin=' + fechaFin;
    }
    
    $.ajax({
      url: url,
      method: 'GET',
      dataType: 'json',
      success: function(data) {
        if (data.status === 'success') {
          // Animación del contador
          $contador.prop('Counter', 0).animate({
            Counter: data.total
          }, {
            duration: 500,
            easing: 'swing',
            step: function(now) {
              $contador.text(Math.ceil(now));
            },
            complete: function() {
              $contador.text(data.total);
            }
          });
          
          // Actualizar rango de fechas
          $rango.text(data.rango || '');
        } else {
          $contador.text('Error');
          console.error('Error en conteo:', data.message);
        }
      },
      error: function(xhr, status, error) {
        $contador.text('Error');
        console.error('Error AJAX conteo:', error);
      }
    });
  };
}

// Inicializar al cargar la página
$(document).ready(function() {
  // Cargar conteo inicial para esta tarjeta (con "este_mes" como default)
  var estado<?php echo $conteoEstado; ?> = <?php echo $conteoEstado; ?>;
  var conteoId<?php echo $conteoEstado; ?> = '<?php echo $conteoId; ?>';
  actualizarConteoRegistros(estado<?php echo $conteoEstado; ?>, 'este_mes', conteoId<?php echo $conteoEstado; ?>);
  
  // Evento al cambiar el select de periodo
  $(document).off('change', '.select-periodo-conteo[data-conteo-id="<?php echo $conteoId; ?>"]');
  $(document).on('change', '.select-periodo-conteo[data-conteo-id="<?php echo $conteoId; ?>"]', function() {
    var $select = $(this);
    var estado = $select.data('estado');
    var periodo = $select.val();
    var conteoId = $select.data('conteo-id');
    var $fechasContainer = $('#fechas-' + conteoId);
    
    if (periodo === 'personalizado') {
      $fechasContainer.slideDown(200);
    } else {
      $fechasContainer.slideUp(200);
      actualizarConteoRegistros(estado, periodo, conteoId);
    }
  });
  
  // Evento al hacer clic en actualizar
  $(document).off('click', '.btn-actualizar-conteo[data-conteo-id="<?php echo $conteoId; ?>"]');
  $(document).on('click', '.btn-actualizar-conteo[data-conteo-id="<?php echo $conteoId; ?>"]', function() {
    var $btn = $(this);
    var estado = $btn.data('estado');
    var conteoId = $btn.data('conteo-id');
    var periodo = $('.select-periodo-conteo[data-conteo-id="' + conteoId + '"]').val();
    
    if (periodo === 'personalizado') {
      var fechaInicio = $('.fecha-inicio-conteo[data-conteo-id="' + conteoId + '"]').val();
      var fechaFin = $('.fecha-fin-conteo[data-conteo-id="' + conteoId + '"]').val();
      actualizarConteoRegistros(estado, periodo, conteoId, fechaInicio, fechaFin);
    } else {
      actualizarConteoRegistros(estado, periodo, conteoId);
    }
  });
  
  // Evento al aplicar fechas personalizadas
  $(document).off('click', '.btn-aplicar-fecha[data-conteo-id="<?php echo $conteoId; ?>"]');
  $(document).on('click', '.btn-aplicar-fecha[data-conteo-id="<?php echo $conteoId; ?>"]', function() {
    var $btn = $(this);
    var estado = $btn.data('estado');
    var conteoId = $btn.data('conteo-id');
    var fechaInicio = $('.fecha-inicio-conteo[data-conteo-id="' + conteoId + '"]').val();
    var fechaFin = $('.fecha-fin-conteo[data-conteo-id="' + conteoId + '"]').val();
    
    if (!fechaInicio || !fechaFin) {
      Swal.fire({
        icon: 'warning',
        title: 'Fechas requeridas',
        text: 'Por favor seleccione la fecha de inicio y fin',
        confirmButtonColor: '#3c8dbc'
      });
      return;
    }
    
    if (fechaInicio > fechaFin) {
      Swal.fire({
        icon: 'warning',
        title: 'Fechas inválidas',
        text: 'La fecha de inicio no puede ser mayor que la fecha fin',
        confirmButtonColor: '#3c8dbc'
      });
      return;
    }
    
    actualizarConteoRegistros(estado, 'personalizado', conteoId, fechaInicio, fechaFin);
  });
});
</script>
