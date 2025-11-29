<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Calendario
      <small>Control de eventos y actividades</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Calendario</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-3">
        <!-- Nueva sección: Lista de Clientes Recientes -->
        <div class="box box-solid">
          <div class="box-header with-border">
            <h3 class="box-title">Clientes Recientes</h3>
          </div>
          <div class="box-body">
            <div style="margin-bottom:8px;">
              <button id="btnReunionesArchivadas" class="btn btn-sm btn-primary">Historial de reuniones</button>
            </div>
            <?php
              // Controlador ya incluido globalmente en index.php
              $clientes = ControladorOportunidad::ctrMostrarClientesOrdenados();

              // Obtener reuniones activas (no archivadas) para todos los clientes
              $allClientIds = array_column($clientes, 'id');
              // Evitar llamadas vacías
              if (!empty($allClientIds)) {
                $reuniones = ModeloCalendario::mdlMostrarReunionesPorClientes($allClientIds);
              } else {
                $reuniones = [];
              }

              // Agrupar reuniones por cliente_id
              $reunionesPorCliente = [];
              foreach ($reuniones as $reunion) {
                $reunionesPorCliente[$reunion['cliente_id']][] = $reunion['titulo'];
              }

              // Filtrar lista de clientes para incluir solo aquellos que tengan reuniones activas
              $clientesConReuniones = array_filter($clientes, function($c) use ($reunionesPorCliente) {
                return isset($reunionesPorCliente[$c['id']]);
              });

              // Mostrar solo los primeros 5 clientes que realmente tienen reuniones activas
              $clientesLimit = array_slice(array_values($clientesConReuniones), 0, 5);
            ?>
            <ul class="list-group">
              <?php if (!empty($clientesLimit)): ?>
              <?php foreach ($clientesLimit as $cliente): ?>
                  <li class="list-group-item">
                    <strong><?php echo htmlspecialchars($cliente['nombre']); ?></strong><br>
                    <small>
                      <?php
                        // Obtener actividades para el cliente
                        $actividades = isset($reunionesPorCliente[$cliente['id']]) ? $reunionesPorCliente[$cliente['id']] : [];
                        if (!empty($actividades)) {
                          // Mostrar botones para cada actividad
                          foreach ($actividades as $index => $actividad) {
                            echo '<button type="button" class="btn btn-default btn-xs actividad-btn" data-cliente-id="' . htmlspecialchars($cliente['id']) . '" data-actividad="' . htmlspecialchars($actividad) . '" style="margin-right: 5px; margin-bottom: 3px;">' . htmlspecialchars($actividad) . '</button>';
                          }
                          // Mostrar contador de actividades
                          echo '<br><small class="text-muted">Total actividades: ' . count($actividades) . '</small>';
                        } else {
                          echo 'Sin actividades';
                        }
                      ?>
                    </small>
                  </li>
                <?php endforeach; ?>
              <?php else: ?>
                <li class="list-group-item">No hay clientes registrados.</li>
              <?php endif; ?>
            </ul>
          </div>
        </div>

        <!-- /. box -->
      </div>
      <!-- /.col -->
      <div class="col-md-9">
        <div class="box box-primary">
          <div class="box-body no-padding">
            <!-- CALENDARIO DE REUNIONES -->
            <div id="calendarReuniones"></div>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /. box -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->

  <!-- Modal Reuniones Pasadas (abre automáticamente si hay reuniones pasadas) -->
  <div class="modal fade" id="modalReunionesPasadas" tabindex="-1" role="dialog" aria-labelledby="modalReunionesPasadasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalReunionesPasadasLabel">Reuniones pasadas</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <table class="table table-condensed table-striped" id="tablaReunionesPasadas">
            <thead>
              <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Título</th>
                <th>Cliente</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Eliminada la inclusión del modal de eventos para evitar abrir modal innecesario -->
  <!-- <?php // include "evento_modals.php"; ?> -->

</div>
<!-- /.content-wrapper -->

  <!-- Modal Crear Reunión -->
  <div class="modal fade" id="modalCrearReunion" tabindex="-1" role="dialog" aria-labelledby="modalCrearReunionLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
          <form id="formCrearReunion">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalCrearReunionLabel">Crear Reunión</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            <div class="modal-body">
              <input type="hidden" id="usuario_id" name="usuario_id" value="<?php echo $_SESSION['id']; ?>">
                <div class="form-group">
                  <label for="titulo">Título <span style="color:red">*</span></label>
                  <input type="text" class="form-control" id="titulo" name="titulo" required>
                </div>
                <div class="form-group">
                  <label for="cliente_id">Cliente <span style="color:red">*</span></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-tags"></i></span>
                    <select class="form-control input-lg" id="cliente_id" name="cliente_id" required style="width: 100%;">
                      <option value="">Seleccionar cliente</option>
                    </select>
                  </div>
                </div>
            <div class="form-group">
              <label for="fecha">Fecha</label>
              <input type="text" class="form-control" id="fecha" name="fecha" readonly>
            </div>
            <div class="form-group">
              <label for="hora_inicio">Hora Inicio <span style="color:red">*</span></label>
              <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
            </div>
            <div class="form-group">
              <label for="hora_fin">Hora Fin <span style="color:red">*</span></label>
              <input type="time" class="form-control" id="hora_fin" name="hora_fin" required>
            </div>
            <div class="form-group">
              <label for="descripcion">Descripción</label>
              <textarea class="form-control" id="descripcion" name="descripcion"></textarea>
            </div>
            <div class="form-group">
              <label for="ubicacion">Ubicación</label>
              <input type="text" class="form-control" id="ubicacion" name="ubicacion">
            </div>
            <div class="form-group">
              <label for="observaciones">Observaciones</label>
              <textarea class="form-control" id="observaciones" name="observaciones"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

<!-- Modal Detalle / Edición Reunión -->
<div class="modal fade" id="modalDetalleReunion" tabindex="-1" role="dialog" aria-labelledby="modalDetalleReunionLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formEditarReunion">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalDetalleReunionLabel">Detalle Reunión</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="editar_id" name="id">
          <div class="form-group">
            <label for="editar_titulo">Título <span style="color:red">*</span></label>
            <input type="text" class="form-control" id="editar_titulo" name="titulo" required>
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
            <input type="text" class="form-control" id="editar_fecha" name="fecha" readonly>
          </div>
          <div class="form-group">
            <label for="editar_hora_inicio">Hora Inicio <span style="color:red">*</span></label>
            <input type="time" class="form-control" id="editar_hora_inicio" name="hora_inicio" required>
          </div>
          <div class="form-group">
            <label for="editar_hora_fin">Hora Fin <span style="color:red">*</span></label>
            <input type="time" class="form-control" id="editar_hora_fin" name="hora_fin" required>
          </div>
          <div class="form-group">
            <label for="editar_descripcion">Descripción</label>
            <textarea class="form-control" id="editar_descripcion" name="descripcion"></textarea>
          </div>
          <div class="form-group">
            <label for="editar_ubicacion">Ubicación</label>
            <input type="text" class="form-control" id="editar_ubicacion" name="ubicacion">
          </div>
          <div class="form-group">
            <label for="editar_observaciones">Observaciones</label>
            <textarea class="form-control" id="editar_observaciones" name="observaciones"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Actualizar</button>
          <?php if($_SESSION["perfil"] !== "Vendedor"): ?>
          <button type="button" class="btn btn-danger" id="btnEliminarReunion">Eliminar</button>
          <?php endif; ?>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
// Agrupa los eventos en páginas de 5
function paginateEvents() {
    var events = Array.from(document.querySelectorAll('#external-events .external-event'));
    var eventsPerPage = 5;
    var totalPages = Math.ceil(events.length / eventsPerPage);
    var pagination = document.getElementById('event-pagination');
    if (pagination) {
        // Limpiar paginación
        pagination.innerHTML = '<li><a href="#" class="page-link" data-page="prev">&laquo;</a></li>';
        for (let i = 1; i <= totalPages; i++) {
            pagination.innerHTML += `<li><a href="#" class="page-link" data-page="${i}">${i}</a></li>`;
        }
        pagination.innerHTML += '<li><a href="#" class="page-link" data-page="next">&raquo;</a></li>';

        // Mostrar solo la primera página al inicio
        showEventPage(1, events, eventsPerPage);

        // Evento click en paginación
        pagination.querySelectorAll('.page-link').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                let page = this.getAttribute('data-page');
                let current = pagination.querySelector('.active');
                let currentPage = current ? parseInt(current.textContent) : 1;
                if (page === 'prev') {
                    if (currentPage > 1) showEventPage(currentPage - 1, events, eventsPerPage);
                } else if (page === 'next') {
                    if (currentPage < totalPages) showEventPage(currentPage + 1, events, eventsPerPage);
                } else {
                    showEventPage(parseInt(page), events, eventsPerPage);
                }
            });
        });
    }
}

function showEventPage(page, events, eventsPerPage) {
    // Oculta todos los eventos
    events.forEach(function(ev) { ev.style.display = 'none'; });
    // Muestra solo los eventos de la página seleccionada
    let start = (page - 1) * eventsPerPage;
    let end = start + eventsPerPage;
    events.slice(start, end).forEach(function(ev) { ev.style.display = ''; });
    // Actualiza la paginación activa
    let pagination = document.getElementById('event-pagination');
    if (pagination) {
        pagination.querySelectorAll('li').forEach(function(li) {
            li.classList.remove('active');
        });
        let pageLinks = pagination.querySelectorAll('.page-link');
        pageLinks.forEach(function(link) {
            if (link.getAttribute('data-page') == page) {
                link.parentElement.classList.add('active');
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    paginateEvents();
    // Re-paginar eventos cada vez que se agregue uno nuevo
    var addNewEventBtn = document.getElementById('add-new-event');
    if (addNewEventBtn) {
        addNewEventBtn.addEventListener('click', function() {
            setTimeout(function() {
                paginateEvents();
            }, 100);
        });
    }
    // MutationObserver para detectar cambios en #external-events
    var externalEvents = document.getElementById('external-events');
    if (externalEvents) {
        var observer = new MutationObserver(function(mutationsList, observer) {
            paginateEvents();
        });
        observer.observe(externalEvents, { childList: true });
    }
});
</script>

<style>
.actividad-destacada {
  border: 3px solid #ff0000 !important;
  box-shadow: 0 0 10px rgba(255, 0, 0, 0.5);
}
</style>
