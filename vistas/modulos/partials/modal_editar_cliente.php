<!-- Partial: Modal editar cliente (shared) -->
<div id="modalActualizarClientes" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data">
        <div class="modal-header" style="background:#3c8dbc; color:white;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Editar Cliente</h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <input type="hidden" id="idCliente" name="idCliente">
            <input type="hidden" id="rutaCliente" name="ruta" value="">

            <!-- Campos editables -->
            <div class="form-group">
              <label for="editarNombre">Nombre <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                <input type="text" class="form-control input-lg" id="editarNombre" name="editarNombre" required>
              </div>
            </div>
            <div class="form-group">
              <label for="editarTipo">Tipo <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-users"></i></span>
                <select class="form-control input-lg" id="editarTipo" name="editarTipo" required>
                  <option value="">Seleccionar tipo</option>
                  <option value="DNI">DNI</option>
                  <option value="RUC">RUC</option>
                  <option value="otros">otros</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="editarDocumento">Documento <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-address-card"></i></span>
                <input type="text" class="form-control input-lg" id="editarDocumento" name="editarDocumento" required>
              </div>
            </div>
            <div class="form-group">
              <label for="editarTelefono">Teléfono <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-mobile"></i></span>
                <input type="text" class="form-control input-lg" id="editarTelefono" name="editarTelefono" maxlength="15" required>
              </div>
            </div>
            <div class="form-group">
              <label for="editarCorreo">Observacion</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                <input type="text" class="form-control input-lg" id="editarCorreo" name="editarCorreo">
              </div>
            </div>
            <div class="form-group">
              <label for="editarCiudad">Ciudad</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-home"></i></span>
                <input type="text" class="form-control input-lg" id="editarCiudad" name="editarCiudad">
              </div>
            </div>
            <div class="form-group">
              <label for="editarMigracion">Migración</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-globe"></i></span>
                <input type="text" class="form-control input-lg" id="editarMigracion" name="editarMigracion">
              </div>
            </div>
            <div class="form-group">
              <label for="editarReferencia">Referencia</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-link"></i></span>
                <select class="form-control input-lg" id="editarReferencia" name="editarReferencia">
                  <option value="">Seleccionar referencia</option>
                  <option value="TIK TOK">TIK TOK</option>
                  <option value="FACEBOOK">FACEBOOK</option>
                  <option value="INSTAGRAM">INSTAGRAM</option>
                  <option value="whatsapp">whatsapp</option>
                  <option value="otros">otros</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="editarFechaContacto">Fecha de Contacto <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="date" class="form-control input-lg" id="editarFechaContacto" name="editarFechaContacto" required>
              </div>
            </div>
            <div class="form-group">
              <label for="editarEmpresa">Empresa <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-building"></i></span>
                <input type="text" class="form-control input-lg" id="editarEmpresa" name="editarEmpresa" required>
              </div>
            </div>
            <div class="form-group">
              <label for="editarMotivo">Motivo</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-sticky-note"></i></span>
                <textarea class="form-control" id="editarMotivo" name="editarMotivo" rows="3"></textarea>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Editar</button>
        </div>
      </form>
    </div>
  </div>
</div>
