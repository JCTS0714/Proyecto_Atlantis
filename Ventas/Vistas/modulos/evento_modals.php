<!-- Modal Crear Evento -->
<div class="modal fade" id="modalCrearEvento" tabindex="-1" role="dialog" aria-labelledby="modalCrearEventoLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formCrearEvento">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalCrearEventoLabel">Crear Evento</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="titulo_evento">Título <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="titulo_evento" name="titulo" required>
          </div>
          <div class="form-group">
            <label for="color_evento">Color <span class="text-danger">*</span></label>
            <input type="color" class="form-control" id="color_evento" name="color" value="#3c8dbc" required>
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

<!-- Modal Detalle / Edición Evento -->
<div class="modal fade" id="modalDetalleEvento" tabindex="-1" role="dialog" aria-labelledby="modalDetalleEventoLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formEditarEvento">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalDetalleEventoLabel">Detalle Evento</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="editar_id_evento" name="id">
          <div class="form-group">
            <label for="editar_titulo_evento">Título <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="editar_titulo_evento" name="titulo" required>
          </div>
          <div class="form-group">
            <label for="editar_color_evento">Color <span class="text-danger">*</span></label>
            <input type="color" class="form-control" id="editar_color_evento" name="color" value="#3c8dbc" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Actualizar</button>
          <button type="button" class="btn btn-danger" id="btnEliminarEvento">Eliminar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>
