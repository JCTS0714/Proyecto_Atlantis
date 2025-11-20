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
            echo "<div class='box-body kanban-column' id='" . str_replace(' ', '', $id) . "' ondrop='drop(event)' ondragover='allowDrop(event)'>";
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
              <select class="form-control input-lg" id="editar_cliente_id" name="cliente_id" required style="width: 100%;">
                <option value="">Seleccionar cliente</option>
              </select>
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
</style>

<script>
$(document).ready(function() {
  // Cargar incidencias al inicio
  cargarIncidencias();
  cargarListaIncidencias();

  // Función para cargar las columnas del Kanban
  function cargarIncidencias() {
    $.ajax({
      url: 'ajax/backlog.ajax.php',
      method: 'GET',
      data: { action: 'mostrarIncidencias' },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          // Limpiar columnas
          $('.kanban-column').empty();

          // Organizar incidencias por columna
          var incidenciasPorColumna = {
            'Enproceso': [],
            'Validado': [],
            'Terminado': []
          };

          response.incidencias.forEach(function(incidencia) {
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
                <div class="panel panel-default panel-kanban" draggable="true" data-id="${incidencia.id}" data-columna="${columna}">
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

          console.log('Incidencias cargadas:', response.incidencias);
          console.log('Incidencias por columna:', incidenciasPorColumna);

          // Agregar eventos de drag and drop
          $('.panel-kanban').on('dragstart', function(e) {
            e.originalEvent.dataTransfer.setData('text/plain', $(this).data('id'));
            $(this).addClass('dragging');
          });

          $('.panel-kanban').on('dragend', function(e) {
            $(this).removeClass('dragging');
          });
        }
      },
      error: function(xhr, status, error) {
        console.error('Error al cargar incidencias:', error);
      }
    });
  }

  // Función para cargar la lista lateral de incidencias recientes
  function cargarListaIncidencias() {
    $.ajax({
      url: 'ajax/backlog.ajax.php',
      method: 'GET',
      data: { action: 'mostrarIncidencias' },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          var $lista = $('#lista-incidencias');
          $lista.empty();

          // Mostrar solo las primeras 10 incidencias más recientes
          var incidenciasRecientes = response.incidencias.slice(0, 10);

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

          // Agregar evento click a los items de la lista
          $('.incidencia-item').on('click', function(e) {
            e.preventDefault();
            var idIncidencia = $(this).data('id');
            cargarDetalleIncidencia(idIncidencia);
            $('#modalDetalleIncidencia').modal('show');

            // Resaltar el item seleccionado
            $('.incidencia-item').removeClass('active');
            $(this).addClass('active');
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

          // Cargar cliente en select
          $('#editar_cliente_id').empty().append('<option value="">Cargando...</option>');
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
  window.allowDrop = function(ev) {
    ev.preventDefault();
    var target = ev.target.closest('.kanban-column');
    if (target) {
      target.classList.add('drop-target');
    }
  };

  window.drop = function(ev) {
    ev.preventDefault();
    var target = ev.target.closest('.kanban-column');
    if (target) {
      target.classList.remove('drop-target');
      var idIncidencia = ev.dataTransfer.getData('text/plain');
      var nuevaColumna = target.id;

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
    }
  };

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
