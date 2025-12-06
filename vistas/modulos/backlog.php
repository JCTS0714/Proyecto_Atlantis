<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!-- =============================================== -->
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Backlog de Incidencias</h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>/inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
        <li class="active">Backlog</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Filtros inline -->
      <div style="margin-bottom: 15px; display:flex; align-items:flex-end; gap:10px; flex-wrap:wrap;">
        <div class="form-group" style="margin:0;">
          <label for="filtro-estado-backlog" style="display:block; font-weight:600; margin-bottom:4px;">Estado</label>
          <select class="form-control" id="filtro-estado-backlog" style="min-width:140px;">
            <option value="">Todos los estados</option>
            <option value="Enproceso">En proceso</option>
            <option value="Validado">Validado</option>
            <option value="Terminado">Terminado</option>
          </select>
        </div>

        <div class="form-group" style="margin:0;">
          <label for="filtro-prioridad-backlog" style="display:block; font-weight:600; margin-bottom:4px;">Prioridad</label>
          <select class="form-control" id="filtro-prioridad-backlog" style="min-width:130px;">
            <option value="">Todas las prioridades</option>
            <option value="baja">Baja</option>
            <option value="media">Media</option>
            <option value="alta">Alta</option>
          </select>
        </div>

        <div class="form-group" style="margin:0;">
          <label for="filtro-periodo-backlog" style="display:block; font-weight:600; margin-bottom:4px;">Periodo</label>
          <select class="form-control" id="filtro-periodo-backlog" style="min-width:140px;">
            <option value="">Todos los periodos</option>
            <option value="diario">Diario</option>
            <option value="semanal">Semanal</option>
            <option value="mensual">Mensual</option>
            <option value="personalizado">Personalizado</option>
          </select>
        </div>

        <div id="filtro-fechas-backlog" style="display: none; margin:0; display:flex; gap:8px;">
          <div class="form-group" style="margin:0;">
            <label for="fecha-inicio-backlog" style="display:block; font-weight:600; margin-bottom:4px;">Fecha Inicio</label>
            <input type="date" class="form-control" id="fecha-inicio-backlog">
          </div>
          <div class="form-group" style="margin:0;">
            <label for="fecha-fin-backlog" style="display:block; font-weight:600; margin-bottom:4px;">Fecha Fin</label>
            <input type="date" class="form-control" id="fecha-fin-backlog">
          </div>
        </div>

        <div style="display:flex; gap:6px;">
          <button type="button" class="btn btn-default" id="btn-limpiar-filtros-backlog">
            <i class="fa fa-eraser"></i> Limpiar
          </button>
          <button type="button" class="btn btn-primary" id="btn-aplicar-filtros-backlog">
            <i class="fa fa-filter"></i> Aplicar
          </button>
        </div>
      </div>

      <div class="row">
        <!-- Lista lateral de incidencias recientes -->
        <div class="col-md-3">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">Incidencias Recientes</h3>
            </div>
            <div class="box-body">
              <div id="lista-incidencias" class="list-group">
                <!-- Las incidencias se cargarán aquí dinámicamente -->
              </div>
            </div>
          </div>
        </div>

        <!-- Tablero Kanban -->
        <div class="col-md-9">
          <div class="row">
            <?php
            $columnas = [
                "En proceso" => "En proceso",
                "Validado" => "Validado",
                "Terminado" => "Terminado"
            ];
            foreach ($columnas as $id => $titulo) {
              $colorClass = '';
              switch ($titulo) {
                case 'En proceso': $colorClass = 'border-warning'; break;
                case 'Validado': $colorClass = 'border-info'; break;
                case 'Terminado': $colorClass = 'border-success'; break;
              }
              echo '<div class="col-md-4">';
              echo '<div class="box box-primary ' . $colorClass . '">';
              echo '<div class="box-header with-border">';
              echo "<h3 class='box-title'>$titulo</h3>";
              echo '</div>';
              echo "<div class='box-body kanban-column' id='" . str_replace(' ', '', $id) . "'>";
              echo "<!-- Tarjetas se cargarán aquí -->";
              echo '</div>';
              echo '</div></div>';
            }
            ?>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- Modal para detalles de incidencia -->
  <div class="modal fade" id="modalDetalleIncidencia" tabindex="-1" role="dialog" aria-labelledby="modalDetalleIncidenciaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form id="formEditarIncidencia">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalDetalleIncidenciaLabel">Detalle Incidencia</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="editar_id_incidencia" name="id">
            <div class="form-group">
              <label for="editar_correlativo">Correlativo</label>
              <input type="text" class="form-control" id="editar_correlativo" readonly>
            </div>
            <div class="form-group">
              <label for="editar_nombre_incidencia">Nombre de la Incidencia <span style="color:red">*</span></label>
              <input type="text" class="form-control" id="editar_nombre_incidencia" name="nombre_incidencia" required>
            </div>
            <div class="form-group">
              <label for="editar_cliente_id">Cliente <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-tags"></i></span>
                <select class="form-control input-lg" id="editar_cliente_id" name="cliente_id_display" disabled style="width: 100%;">
                  <option value="">Seleccionar cliente</option>
                </select>
                <input type="hidden" id="editar_cliente_id_hidden" name="cliente_id">
              </div>
            </div>
            <div class="form-group">
              <label for="editar_fecha">Fecha</label>
              <input type="date" class="form-control" id="editar_fecha" name="fecha" required>
            </div>
            <div class="form-group">
              <label for="editar_prioridad">Prioridad <span style="color:red">*</span></label>
              <select class="form-control" id="editar_prioridad" name="prioridad" required>
                <option value="">Seleccionar Prioridad</option>
                <option value="baja">Baja</option>
                <option value="media">Media</option>
                <option value="alta">Alta</option>
              </select>
            </div>
            <div class="form-group">
              <label for="editar_observaciones">Observaciones</label>
              <textarea class="form-control" id="editar_observaciones" name="observaciones"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <?php if($_SESSION["perfil"] !== "Vendedor"): ?>
            <button type="button" class="btn btn-danger" id="btnEliminarIncidencia">Eliminar</button>
            <?php endif; ?>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <style>
  .kanban-column {
    min-height: 400px;
    border: 1px solid #ddd;
    padding: 10px;
    background-color: #f9f9f9;
    overflow-y: auto;
    transition: all 0.3s ease;
  }

  .kanban-column.drop-target {
    background-color: #e8f4fd;
    border: 2px dashed #3c8dbc;
    transform: scale(1.02);
  }

  .panel-kanban {
    cursor: move;
    margin-bottom: 10px;
  }

  .card.dragging {
    opacity: 0.6;
    transform: rotate(5deg);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
  }

  .card {
    transition: all 0.2s ease;
    cursor: grab;
  }

  .card:active {
    cursor: grabbing;
  }

  .list-group-item.active {
    background-color: #3c8dbc;
    color: white;
  }

  .border-warning {
    border-left: 4px solid #f39c12;
  }

  .border-info {
    border-left: 4px solid #00c0ef;
  }

  .border-success {
    border-left: 4px solid #00a65a;
  }

  .panel-kanban.highlight {
    box-shadow: 0 0 14px rgba(60,141,188,0.7);
    transform: scale(1.01);
    border: 2px solid rgba(60,141,188,0.9);
  }

  .panel-kanban.highlight-alta {
    box-shadow: 0 0 18px rgba(221,75,57,0.85);
    border: 2px solid rgba(221,75,57,0.9);
  }
  .panel-kanban.highlight-media {
    box-shadow: 0 0 18px rgba(0,192,239,0.85);
    border: 2px solid rgba(0,192,239,0.9);
  }
  .panel-kanban.highlight-baja {
    box-shadow: 0 0 18px rgba(243,156,18,0.85);
    border: 2px solid rgba(243,156,18,0.9);
  }
  /* Mantener color original de los badges de prioridad aun cuando el item de la lista esté activo */
  .list-group-item.active .label.label-danger { background-color: #dd4b39 !important; color: #fff !important; }
  .list-group-item.active .label.label-info { background-color: #00c0ef !important; color: #fff !important; }
  .list-group-item.active .label.label-warning { background-color: #f39c12 !important; color: #fff !important; }
  .list-group-item.active .label { opacity: 1 !important; }

  /* Lista lateral: remarcado mínimo en vez de teñir todo el interior */
  #lista-incidencias .list-group-item.active {
    background-color: rgba(60,141,188,0.06) !important; /* muy sutil */
    color: inherit !important;
    border-left: 4px solid rgba(60,141,188,0.9) !important;
    box-shadow: none !important;
  }

  /* Estilos para filtros responsivos */
  @media (max-width: 768px) {
    #filtro-fechas-backlog {
      flex-direction: column !important;
      width: 100%;
    }
    #filtro-fechas-backlog .form-group {
      width: 100%;
    }
  }
  </style>

  <script>
  $(document).ready(function() {
    // Variables globales para filtros
    var filtrosActivos = {
      estado: '',
      prioridad: '',
      periodo: '',
      fechaInicio: '',
      fechaFin: ''
    };

    // Mostrar/ocultar campos de fecha cuando se selecciona "Personalizado"
    $('#filtro-periodo-backlog').change(function() {
      if ($(this).val() === 'personalizado') {
        $('#filtro-fechas-backlog').slideDown();
      } else {
        $('#filtro-fechas-backlog').slideUp();
      }
    });

    // Limpiar filtros
    $('#btn-limpiar-filtros-backlog').click(function() {
      $('#filtro-estado-backlog').val('');
      $('#filtro-prioridad-backlog').val('');
      $('#filtro-periodo-backlog').val('');
      $('#fecha-inicio-backlog').val('');
      $('#fecha-fin-backlog').val('');
      $('#filtro-fechas-backlog').slideUp();
      
      filtrosActivos = {
        estado: '',
        prioridad: '',
        periodo: '',
        fechaInicio: '',
        fechaFin: ''
      };
      
      cargarIncidencias();
      cargarListaIncidencias();
    });

    // Aplicar filtros
    $('#btn-aplicar-filtros-backlog').click(function() {
      filtrosActivos = {
        estado: $('#filtro-estado-backlog').val(),
        prioridad: $('#filtro-prioridad-backlog').val(),
        periodo: $('#filtro-periodo-backlog').val(),
        fechaInicio: $('#fecha-inicio-backlog').val(),
        fechaFin: $('#fecha-fin-backlog').val()
      };
      
      console.log('Aplicando filtros:', filtrosActivos);
      cargarIncidencias();
      cargarListaIncidencias();
    });

    // Cargar incidencias al inicio
    cargarIncidencias();
    cargarListaIncidencias();

    // Escuchar evento storage para actualizaciones desde otras pestañas (p.ej. cuando se crea una incidencia)
    window.addEventListener('storage', function(e) {
      if (!e.key) return;
      if (e.key === 'incidencia_creada') {
        try {
          var payload = JSON.parse(e.newValue);
        } catch (err) {
          var payload = null;
        }
        // Recargar tablero y lista
        cargarIncidencias();
        cargarListaIncidencias();
      }
    });

    // Función para cargar las columnas del Kanban
    function cargarIncidencias() {
      $.ajax({
        url: 'ajax/backlog.ajax.php',
        method: 'GET',
        data: { 
          action: 'mostrarIncidencias',
          filtros: JSON.stringify(filtrosActivos)
        },
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            // Limpiar columnas
            $('.kanban-column').empty();

            var todasIncidencias = response.incidencias;
            
            // Aplicar filtros localmente
            var incidenciasFiltradas = filtrarIncidenciasLocal(todasIncidencias);

            // Organizar incidencias por columna
            var incidenciasPorColumna = {
              'Enproceso': [],
              'Validado': [],
              'Terminado': []
            };

            incidenciasFiltradas.forEach(function(incidencia) {
              var columna = incidencia.columna_backlog || 'En proceso';
              // Convertir espacios a sin espacios para IDs
              var columnaId = columna.replace(/\s+/g, '');
              if (incidenciasPorColumna[columnaId]) {
                incidenciasPorColumna[columnaId].push(incidencia);
              } else {
                // Si no existe la columna, poner en En proceso
                incidenciasPorColumna['Enproceso'].push(incidencia);
              }
            });

            // Renderizar tarjetas en cada columna
            Object.keys(incidenciasPorColumna).forEach(function(columna) {
              var $columna = $('#' + columna);
              incidenciasPorColumna[columna].forEach(function(incidencia) {
                var prioridadClass = getPrioridadClass(incidencia.prioridad);
                var tarjeta = `
                  <div class="panel panel-default panel-kanban" draggable="true" data-id="${incidencia.id}" data-columna="${columna}" data-prioridad="${incidencia.prioridad}">
                    <div class="panel-body">
                      <h5 class="panel-title">${incidencia.correlativo} - ${incidencia.nombre_incidencia}</h5>
                      <p class="text-muted small">${incidencia.nombre_cliente}</p>
                      <span class="label ${prioridadClass}">${incidencia.prioridad}</span>
                    </div>
                  </div>
                `;
                $columna.append(tarjeta);
              });
            });

            console.log('Incidencias cargadas:', response.incidencias.length);
            console.log('Incidencias filtradas:', incidenciasFiltradas.length);
            console.log('Incidencias por columna:', incidenciasPorColumna);

                    // (Drag listeners attached globally outside; no per-card handlers aquí)
          }
        },
        error: function(xhr, status, error) {
          console.error('Error al cargar incidencias:', error);
        }
      });
    }

    // Función para filtrar incidencias localmente
    function filtrarIncidenciasLocal(incidencias) {
      return incidencias.filter(function(incidencia) {
        // Filtro por estado (columna)
        if (filtrosActivos.estado !== '') {
          var columnaId = (incidencia.columna_backlog || 'En proceso').replace(/\s+/g, '');
          if (columnaId !== filtrosActivos.estado) {
            return false;
          }
        }

        // Filtro por prioridad
        if (filtrosActivos.prioridad !== '') {
          if (incidencia.prioridad !== filtrosActivos.prioridad) {
            return false;
          }
        }

        // Filtro por periodo
        if (filtrosActivos.periodo !== '') {
          var fechaIncidencia = new Date(incidencia.fecha);
          var ahora = new Date();
          
          switch(filtrosActivos.periodo) {
            case 'diario':
              var hoy = new Date(ahora.getFullYear(), ahora.getMonth(), ahora.getDate());
              var incidenciaDate = new Date(fechaIncidencia.getFullYear(), fechaIncidencia.getMonth(), fechaIncidencia.getDate());
              if (incidenciaDate.getTime() !== hoy.getTime()) {
                return false;
              }
              break;
              
            case 'semanal':
              var inicioSemana = new Date(ahora);
              inicioSemana.setDate(ahora.getDate() - ahora.getDay());
              inicioSemana.setHours(0, 0, 0, 0);
              var finSemana = new Date(inicioSemana);
              finSemana.setDate(inicioSemana.getDate() + 6);
              finSemana.setHours(23, 59, 59, 999);
              if (fechaIncidencia < inicioSemana || fechaIncidencia > finSemana) {
                return false;
              }
              break;
              
            case 'mensual':
              if (fechaIncidencia.getMonth() !== ahora.getMonth() || 
                  fechaIncidencia.getFullYear() !== ahora.getFullYear()) {
                return false;
              }
              break;
              
            case 'personalizado':
              if (filtrosActivos.fechaInicio !== '' && filtrosActivos.fechaFin !== '') {
                var inicio = new Date(filtrosActivos.fechaInicio);
                var fin = new Date(filtrosActivos.fechaFin);
                fin.setHours(23, 59, 59, 999);
                if (fechaIncidencia < inicio || fechaIncidencia > fin) {
                  return false;
                }
              }
              break;
          }
        }

        return true;
      });
    }

    // Función para cargar la lista lateral de incidencias recientes
    function cargarListaIncidencias() {
      $.ajax({
        url: 'ajax/backlog.ajax.php',
        method: 'GET',
        data: { 
          action: 'mostrarIncidencias',
          filtros: JSON.stringify(filtrosActivos)
        },
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            var $lista = $('#lista-incidencias');
            $lista.empty();

            // Aplicar filtros localmente
            var incidenciasFiltradas = filtrarIncidenciasLocal(response.incidencias);

            // Mostrar solo las primeras 10 incidencias más recientes (después de filtrar)
            var incidenciasRecientes = incidenciasFiltradas.slice(0, 10);

            if (incidenciasRecientes.length === 0) {
              $lista.append('<div class="text-muted text-center" style="padding:10px;">No hay incidencias</div>');
              return;
            }

            incidenciasRecientes.forEach(function(incidencia) {
              var prioridadClass = getPrioridadClass(incidencia.prioridad);
              var item = `
                <a href="#" class="list-group-item list-group-item-action incidencia-item" data-id="${incidencia.id}">
                  <h6 class="mb-1">${incidencia.correlativo} - ${incidencia.nombre_incidencia}</h6>
                  <p class="mb-1 text-muted small">${incidencia.nombre_cliente}</p>
                  <span class="label ${prioridadClass}">${incidencia.prioridad}</span>
                </a>
              `;
              $lista.append(item);
            });

            // Agregar evento click a los items de la lista: solo resaltar la tarjeta en el tablero
            $('.incidencia-item').on('click', function(e) {
              e.preventDefault();
              var idIncidencia = $(this).data('id');

              // Resaltar el item seleccionado en la lista
              $('.incidencia-item').removeClass('active');
              $(this).addClass('active');

              // Buscar la tarjeta correspondiente en el tablero y hacer scroll/efecto
              var $tarjeta = $('.panel-kanban[data-id="' + idIncidencia + '"]');
              if ($tarjeta.length) {
                // Agregar clase temporal de highlight y de prioridad
                // Leer atributo directamente y normalizar
                var prioridadAttr = $tarjeta.attr('data-prioridad') || $tarjeta.data('prioridad') || '';
                var prioridad = String(prioridadAttr).trim().toLowerCase();
                var hlClass = 'highlight';
                var hlPrioClass = '';
                if (prioridad.indexOf('al') === 0 || prioridad === 'alta') hlPrioClass = 'highlight-alta';
                else if (prioridad.indexOf('me') === 0 || prioridad === 'media') hlPrioClass = 'highlight-media';
                else if (prioridad.indexOf('ba') === 0 || prioridad === 'baja') hlPrioClass = 'highlight-baja';

                // Aplicar clase base y estilos inline para asegurar color según prioridad
                $tarjeta.addClass(hlClass);
                var inlineStyles = {};
                if (hlPrioClass === 'highlight-alta') {
                  inlineStyles['box-shadow'] = '0 0 18px rgba(221,75,57,0.85)';
                  inlineStyles['border'] = '2px solid rgba(221,75,57,0.9)';
                } else if (hlPrioClass === 'highlight-media') {
                  inlineStyles['box-shadow'] = '0 0 18px rgba(0,192,239,0.85)';
                  inlineStyles['border'] = '2px solid rgba(0,192,239,0.9)';
                } else if (hlPrioClass === 'highlight-baja') {
                  inlineStyles['box-shadow'] = '0 0 18px rgba(243,156,18,0.85)';
                  inlineStyles['border'] = '2px solid rgba(243,156,18,0.9)';
                } else {
                  inlineStyles['box-shadow'] = '0 0 14px rgba(60,141,188,0.7)';
                  inlineStyles['border'] = '2px solid rgba(60,141,188,0.9)';
                }
                $tarjeta.css(inlineStyles);
                // Scroll a la tarjeta
                var top = $tarjeta.offset().top - 100;
                $('html, body').animate({ scrollTop: top }, 300);
                // Quitar highlight y estilos después
                setTimeout(function() { $tarjeta.removeClass(hlClass); $tarjeta.css({'box-shadow':'','border':''}); }, 1800);
              }
            });
          }
        },
        error: function(xhr, status, error) {
          console.error('Error al cargar lista de incidencias:', error);
        }
      });
    }

    // Función para obtener clase CSS según prioridad
    function getPrioridadClass(prioridad) {
      switch (prioridad) {
        case 'baja': return 'label-warning';
        case 'media': return 'label-info';
        case 'alta': return 'label-danger';
        default: return 'label-default';
      }
    }

    // Delegación: abrir modal al hacer doble click en una tarjeta del Kanban
    $(document).on('dblclick', '.panel-kanban', function(e) {
      e.preventDefault();
      var idIncidencia = $(this).data('id');
      if (!idIncidencia) return;
      cargarDetalleIncidencia(idIncidencia);
      $('#modalDetalleIncidencia').modal('show');
    });

    // Delegated dragstart/dragend handlers (global) to ensure they run for dynamic elements
    document.addEventListener('dragstart', function(ev) {
      var card = ev.target.closest ? ev.target.closest('.panel-kanban') : null;
      if (card) {
        var id = card.getAttribute('data-id');
        console.log('dragstart card id=', id);
        try { ev.dataTransfer.setData('text/plain', id); ev.dataTransfer.setData('text', id); } catch (err) { console.warn('dataTransfer setData failed', err); }
        card.classList.add('dragging');
      }
    }, false);

    document.addEventListener('dragend', function(ev) {
      var card = ev.target.closest ? ev.target.closest('.panel-kanban') : null;
      if (card) {
        console.log('dragend card id=', card.getAttribute('data-id'));
        card.classList.remove('dragging');
      }
    }, false);

    // Función para cargar detalle de incidencia en modal
    function cargarDetalleIncidencia(idIncidencia) {
      $.ajax({
        url: 'ajax/backlog.ajax.php',
        method: 'GET',
        data: { action: 'obtenerDetalleIncidencia', id: idIncidencia },
        dataType: 'json',
        success: function(response) {
          if (response.success && response.incidencia) {
            var incidencia = response.incidencia;
            $('#editar_id_incidencia').val(incidencia.id);
            $('#editar_correlativo').val(incidencia.correlativo);
            $('#editar_nombre_incidencia').val(incidencia.nombre_incidencia);
            $('#editar_fecha').val(incidencia.fecha);
            $('#editar_prioridad').val(incidencia.prioridad);
            $('#editar_observaciones').val(incidencia.observaciones);

            // Mostrar cliente en select (deshabilitado) y sincronizar hidden
            $('#editar_cliente_id').empty().append('<option value="">Cargando...</option>');
            $('#editar_cliente_id_hidden').val(incidencia.cliente_id);
            $.ajax({
              url: 'ajax/incidencias.ajax.php',
              method: 'GET',
              data: { action: 'buscarClientes', term: '' },
              dataType: 'json',
              success: function(clientes) {
                $('#editar_cliente_id').empty().append('<option value="">Seleccionar cliente</option>');
                clientes.forEach(function(cliente) {
                  var selected = (cliente.value == incidencia.cliente_id) ? 'selected' : '';
                  $('#editar_cliente_id').append(`<option value="${cliente.value}" ${selected}>${cliente.label}</option>`);
                });
                // Si el cliente específico no está entre los resultados (p.ej. top 10), agregarlo manualmente
                if ($('#editar_cliente_id option[value="' + incidencia.cliente_id + '"]').length === 0) {
                  var label = incidencia.nombre_cliente || '';
                  if (incidencia.empresa_cliente && incidencia.empresa_cliente.trim() !== '') {
                    label = label + ' (' + incidencia.empresa_cliente + ')';
                  }
                  $('#editar_cliente_id').append(`<option value="${incidencia.cliente_id}" selected>${label}</option>`);
                }
                // Asegurar que el select muestre el cliente correcto aunque esté deshabilitado
                $('#editar_cliente_id').val(incidencia.cliente_id);
              }
            });
          }
        },
        error: function(xhr, status, error) {
          console.error('Error al cargar detalle de incidencia:', error);
        }
      });
    }

    // Drag and drop functions
    // Attach drag/drop handlers to columns (native listeners)
    document.querySelectorAll('.kanban-column').forEach(function(col) {
      col.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('drop-target');
      });
      col.addEventListener('dragleave', function(e) {
        this.classList.remove('drop-target');
      });
      col.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('drop-target');
        var idIncidencia = null;
        try { idIncidencia = e.dataTransfer.getData('text/plain'); } catch (err) { idIncidencia = null; }
        console.log('drop on column', this.id, 'dataTransfer id=', idIncidencia);
        var nuevaColumna = this.id;
        if (!idIncidencia) return;

        // Actualizar columna en base de datos
        $.ajax({
          url: 'ajax/backlog.ajax.php',
          method: 'POST',
          data: {
            action: 'actualizarColumna',
            idIncidencia: idIncidencia,
            columna: nuevaColumna
          },
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              cargarIncidencias(); // Recargar el tablero
            } else {
              console.error('Error al actualizar columna:', response.message);
            }
          },
          error: function(xhr, status, error) {
            console.error('Error AJAX actualizar columna:', error);
          }
        });
      });
    });

    // Evento para actualizar incidencia desde modal
    $('#formEditarIncidencia').on('submit', function(e) {
      e.preventDefault();
      var formData = $(this).serialize() + '&action=actualizarIncidencia';

      $.ajax({
        url: 'ajax/backlog.ajax.php',
        method: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            $('#modalDetalleIncidencia').modal('hide');
            cargarIncidencias();
            cargarListaIncidencias();
            Swal.fire('Éxito', 'Incidencia actualizada correctamente', 'success');
          } else {
            Swal.fire('Error', response.message || 'Error al actualizar incidencia', 'error');
          }
        },
        error: function(xhr, status, error) {
          console.error('Error AJAX actualizar incidencia:', error);
          Swal.fire('Error', 'Error al actualizar incidencia', 'error');
        }
      });
    });

    // Evento para eliminar incidencia
    $('#btnEliminarIncidencia').on('click', function() {
      var idIncidencia = $('#editar_id_incidencia').val();

      Swal.fire({
        title: '¿Está seguro?',
        text: '¿Desea eliminar esta incidencia? Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: 'ajax/backlog.ajax.php',
            method: 'POST',
            data: {
              action: 'eliminarIncidencia',
              idIncidencia: idIncidencia
            },
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                $('#modalDetalleIncidencia').modal('hide');
                cargarIncidencias();
                cargarListaIncidencias();
                Swal.fire('Eliminado', 'Incidencia eliminada correctamente', 'success');
              } else {
                Swal.fire('Error', response.message || 'Error al eliminar incidencia', 'error');
              }
            },
            error: function(xhr, status, error) {
              console.error('Error AJAX eliminar incidencia:', error);
              Swal.fire('Error', 'Error al eliminar incidencia', 'error');
            }
          });
        }
      });
    });
  });
  </script>
      $.ajax({
        url: 'ajax/backlog.ajax.php',
        method: 'POST',
        data: {
          action: 'actualizarColumna',
          idIncidencia: idIncidencia,
          columna: nuevaColumna
        },
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            cargarIncidencias(); // Recargar el tablero
          } else {
            console.error('Error al actualizar columna:', response.message);
          }
        },
        error: function(xhr, status, error) {
          console.error('Error AJAX actualizar columna:', error);
        }
      });
    });
  });

  // Evento para actualizar incidencia desde modal
  $('#formEditarIncidencia').on('submit', function(e) {
    e.preventDefault();
    var formData = $(this).serialize() + '&action=actualizarIncidencia';

    $.ajax({
      url: 'ajax/backlog.ajax.php',
      method: 'POST',
      data: formData,
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          $('#modalDetalleIncidencia').modal('hide');
          cargarIncidencias();
          cargarListaIncidencias();
          Swal.fire('Éxito', 'Incidencia actualizada correctamente', 'success');
        } else {
          Swal.fire('Error', response.message || 'Error al actualizar incidencia', 'error');
        }
      },
      error: function(xhr, status, error) {
        console.error('Error AJAX actualizar incidencia:', error);
        Swal.fire('Error', 'Error al actualizar incidencia', 'error');
      }
    });
  });

  // Evento para eliminar incidencia
  $('#btnEliminarIncidencia').on('click', function() {
    var idIncidencia = $('#editar_id_incidencia').val();

    Swal.fire({
      title: '¿Está seguro?',
      text: '¿Desea eliminar esta incidencia? Esta acción no se puede deshacer.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: 'ajax/backlog.ajax.php',
          method: 'POST',
          data: {
            action: 'eliminarIncidencia',
            idIncidencia: idIncidencia
          },
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              $('#modalDetalleIncidencia').modal('hide');
              cargarIncidencias();
              cargarListaIncidencias();
              Swal.fire('Eliminado', 'Incidencia eliminada correctamente', 'success');
            } else {
              Swal.fire('Error', response.message || 'Error al eliminar incidencia', 'error');
            }
          },
          error: function(xhr, status, error) {
            console.error('Error AJAX eliminar incidencia:', error);
            Swal.fire('Error', 'Error al eliminar incidencia', 'error');
          }
        });
      }
    });
  });
});
</script>
