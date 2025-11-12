<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>Administrar Incidencias</h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Administrar Incidencias</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="box">
      <div class="box-header with-border">
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalRegistrarIncidencia">
          Registrar Incidencia
        </button>
      </div>

      <div class="box-body">
        <table class="table table-bordered table-striped dt-responsive tabla" id="tablaIncidencias">
          <thead>
            <tr>
              <th>#</th>
              <th>Correlativo</th>
              <th>Nombre de la Incidencia</th>
              <th>Nombre del Cliente</th>
              <th>Fecha</th>
              <th>Prioridad</th>
              <th>Observaciones</th>
              <th>Fecha Creación</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <!-- Registros de incidencias se cargarán aquí dinámicamente -->
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<!-- Modal Registrar Incidencia -->
<div id="modalRegistrarIncidencia" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" action="ajax/incidencias.ajax.php">
        <!-- Modal Header -->
        <div class="modal-header" style="background:#3c8dbc; color:white">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Registrar Incidencia</h4>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">
          <div class="box-body">
            <!-- Correlativo -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-hashtag"></i></span>
                <input type="text" class="form-control input-lg" name="nuevoCorrelativo" id="nuevoCorrelativo" placeholder="Correlativo" readonly>
              </div>
            </div>

            <!-- Nombre de la Incidencia -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-exclamation-triangle"></i></span>
                <input type="text" class="form-control input-lg" name="nuevoNombreIncidencia" id="nuevoNombreIncidencia" placeholder="Nombre de la Incidencia" required>
              </div>
            </div>

            <!-- Nombre del Cliente -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                <input type="text" class="form-control input-lg" name="nuevoNombreCliente" id="nuevoNombreCliente" placeholder="Buscar Cliente..." autocomplete="off" required>
                <input type="hidden" name="idClienteSeleccionado" id="idClienteSeleccionado">
              </div>
            </div>

            <!-- Fecha -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="date" class="form-control input-lg" name="nuevaFecha" id="nuevaFecha" required>
              </div>
            </div>

            <!-- Prioridad -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-flag"></i></span>
                <select class="form-control input-lg" name="nuevaPrioridad" id="nuevaPrioridad" required>
                  <option value="">Seleccionar Prioridad</option>
                  <option value="baja">Baja</option>
                  <option value="media">Media</option>
                  <option value="alta">Alta</option>
                </select>
              </div>
            </div>

            <!-- Observaciones -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-comments"></i></span>
                <textarea class="form-control input-lg" name="nuevaObservaciones" id="nuevaObservaciones" placeholder="Observaciones" rows="3"></textarea>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Guardar Incidencia</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Editar Incidencia -->
<div id="modalEditarIncidencia" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" action="ajax/incidencias.ajax.php">
        <!-- Modal Header -->
        <div class="modal-header" style="background:#3c8dbc; color:white">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Editar Incidencia</h4>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">
          <div class="box-body">
            <!-- Correlativo -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-hashtag"></i></span>
                <input type="text" class="form-control input-lg" name="editarCorrelativo" id="editarCorrelativo" placeholder="Correlativo" readonly>
              </div>
            </div>

            <!-- Nombre de la Incidencia -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-exclamation-triangle"></i></span>
                <input type="text" class="form-control input-lg" name="editarNombreIncidencia" id="editarNombreIncidencia" placeholder="Nombre de la Incidencia" required>
              </div>
            </div>

            <!-- Nombre del Cliente -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                <input type="text" class="form-control input-lg" name="editarNombreCliente" id="editarNombreCliente" placeholder="Buscar Cliente..." autocomplete="off" required>
                <input type="hidden" name="editarIdClienteSeleccionado" id="editarIdClienteSeleccionado">
              </div>
            </div>

            <!-- Fecha -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="date" class="form-control input-lg" name="editarFecha" id="editarFecha" required>
              </div>
            </div>

            <!-- Prioridad -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-flag"></i></span>
                <select class="form-control input-lg" name="editarPrioridad" id="editarPrioridad" required>
                  <option value="">Seleccionar Prioridad</option>
                  <option value="baja">Baja</option>
                  <option value="media">Media</option>
                  <option value="alta">Alta</option>
                </select>
              </div>
            </div>

            <!-- Observaciones -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-comments"></i></span>
                <textarea class="form-control input-lg" name="editarObservaciones" id="editarObservaciones" placeholder="Observaciones" rows="3"></textarea>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-warning">Actualizar Incidencia</button>
        </div>
      </form>
    </div>
  </div>
</div>
