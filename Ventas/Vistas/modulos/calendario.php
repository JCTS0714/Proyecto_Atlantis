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
        <div class="box box-solid">
          <div class="box-header with-border">
            <h4 class="box-title">Eventos Arrastrables</h4>
          </div>
        <div class="box-body">
            <!-- the events -->
            <div id="external-events">
              <?php
                // Mostramos eventos desde la tabla eventos usando el modelo directamente
                $eventos = ControladorEvento::ctrMostrarEventos();
                $eventos_unicos = [];
                foreach ($eventos as $evento) {
                  if (!in_array($evento["titulo"], $eventos_unicos)) {
                    $eventos_unicos[] = $evento["titulo"];
                    $colorClass = $evento["color"] ? $evento["color"] : "bg-green";
                    echo '<div class="external-event ' . htmlspecialchars($colorClass) . '" data-id="' . $evento["id"] . '">' . htmlspecialchars($evento["titulo"]) . '</div>';
                  }
                }
              ?>
              <div class="checkbox">
                <label for="drop-remove">
                  <input type="checkbox" id="drop-remove">
                  Eliminar después de soltar
                </label>
              </div>
            </div>

          </div>
        </div>
        <!-- /. box -->
        <div class="box box-solid">
          <div class="box-header with-border">
            <h3 class="box-title">Crear Evento</h3>
          </div>
          <div class="box-body">
            <div class="btn-group" style="width: 100%; margin-bottom: 10px;">
              <ul class="fc-color-picker" id="color-chooser">
                <li><a class="text-aqua" href="#"><i class="fa fa-square"></i></a></li>
                <li><a class="text-blue" href="#"><i class="fa fa-square"></i></a></li>
                <li><a class="text-light-blue" href="#"><i class="fa fa-square"></i></a></li>
                <li><a class="text-teal" href="#"><i class="fa fa-square"></i></a></li>
                <li><a class="text-yellow" href="#"><i class="fa fa-square"></i></a></li>
                <li><a class="text-orange" href="#"><i class="fa fa-square"></i></a></li>
                <li><a class="text-green" href="#"><i class="fa fa-square"></i></a></li>
                <li><a class="text-lime" href="#"><i class="fa fa-square"></i></a></li>
                <li><a class="text-red" href="#"><i class="fa fa-square"></i></a></li>
                <li><a class="text-purple" href="#"><i class="fa fa-square"></i></a></li>
                <li><a class="text-fuchsia" href="#"><i class="fa fa-square"></i></a></li>
                <li><a class="text-muted" href="#"><i class="fa fa-square"></i></a></li>
                <li><a class="text-navy" href="#"><i class="fa fa-square"></i></a></li>
              </ul>
            </div>
            <!-- /btn-group -->
            <div class="input-group">
              <input id="new-event" type="text" class="form-control" placeholder="Título del evento">

              <div class="input-group-btn">
                <button id="add-new-event" type="button" class="btn btn-primary btn-flat">Agregar</button>
              </div>
              <!-- /btn-group -->
            </div>
          </div>
          <!-- /.box-body -->
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
                <label for="titulo">Título <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="titulo" name="titulo" required>
              </div>
              <div class="form-group">
                <label for="cliente_id">Cliente <span class="text-danger">*</span></label>
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
            <label for="hora_inicio">Hora Inicio <span class="text-danger">*</span></label>
            <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
          </div>
          <div class="form-group">
            <label for="hora_fin">Hora Fin <span class="text-danger">*</span></label>
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
            <label for="editar_titulo">Título <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="editar_titulo" name="titulo" required>
          </div>
          <div class="form-group">
            <label for="editar_cliente_id">Cliente <span class="text-danger">*</span></label>
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
            <label for="editar_hora_inicio">Hora Inicio <span class="text-danger">*</span></label>
            <input type="time" class="form-control" id="editar_hora_inicio" name="hora_inicio" required>
          </div>
          <div class="form-group">
            <label for="editar_hora_fin">Hora Fin <span class="text-danger">*</span></label>
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
          <button type="button" class="btn btn-danger" id="btnEliminarReunion">Eliminar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>
