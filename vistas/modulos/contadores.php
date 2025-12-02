<?php
  // Listar contadores con comercios relacionados
  $contadores = ModeloContador::mdlMostrarContadoresConComercios();
?>
<div class="content-wrapper">
  <section class="content-header">
    <h1>Contadores</h1>
  </section>
  <section class="content">
    
    <!-- Búsqueda Avanzada -->
    <?php @include 'advanced_search.php'; ?>
    
    <div class="box">
      <div class="box-header">
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarContador">Agregar Contador</button>
      </div>

      <div class="box-body">
        <table class="table table-bordered table-striped dt-responsive" id="tablaContadores">
          <thead>
            <tr>
              <th>#</th>
              <th>N°</th>
              <th>Comercio(s)</th>
              <th>Nombre Contador</th>
              <th>Teléfono</th>
              <th>Usuario</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($contadores as $key => $c): ?>
              <tr>
                <td><?php echo $key + 1; ?></td>
                <td><?php echo htmlspecialchars($c['nro']); ?></td>
                <td><?php echo htmlspecialchars($c['comercios_lista'] ?: $c['comercio'] ?: '-'); ?></td>
                <td><?php echo htmlspecialchars($c['nom_contador']); ?></td>
                <td><?php echo htmlspecialchars($c['telefono']); ?></td>
                <td><?php echo htmlspecialchars($c['usuario']); ?></td>
                <td>
                  <div class="btn-group">
                    <button class="btn btn-warning btnEditarContador" data-id="<?php echo $c['id']; ?>" data-toggle="modal" data-target="#modalEditarContador"><i class="fa fa-pencil"></i></button>
                    <a href="contadores?idContadorEliminar=<?php echo $c['id']; ?>" class="btn btn-danger" onclick="return confirm('¿Está seguro de eliminar este contador?');"><i class="fa fa-trash"></i></a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<!-- Modal Agregar -->
<div id="modalAgregarContador" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" id="formAgregarContador">
        <div class="modal-header" style="background:#3c8dbc;color:white;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Agregar Contador</h4>
        </div>
        <div class="modal-body">
          <!-- N° se genera automáticamente al crear (visible pero no editable) -->
          <div class="form-group">
            <label>N°</label>
            <input id="nuevoNro" class="form-control" name="nuevoNro" readonly>
          </div>
          
          <!-- Comercio(s) - Select2 múltiple con búsqueda -->
          <div class="form-group">
            <label>Comercio(s) <span class="text-muted">(buscar por empresa)</span></label>
            <div id="comerciosContainer">
              <div class="comercio-item" style="margin-bottom: 8px;">
                <select class="form-control select2-comercio" name="nuevosComercios[]" style="width: calc(100% - 45px); display: inline-block;">
                </select>
                <button type="button" class="btn btn-danger btn-sm btn-quitar-comercio" style="display: none;" title="Quitar">
                  <i class="fa fa-minus"></i>
                </button>
              </div>
            </div>
            <button type="button" class="btn btn-success btn-sm" id="btnAgregarComercio" style="margin-top: 5px;">
              <i class="fa fa-plus"></i> Agregar otro comercio
            </button>
          </div>
          
          <div class="form-group">
            <label>Nombre Contador</label>
            <input class="form-control" name="nuevoNomContador" autocomplete="off">
          </div>
          <div class="form-group">
            <label>Titular Tlf</label>
            <input class="form-control" name="nuevoTitularTlf" autocomplete="off">
          </div>
          <div class="form-group">
            <label>Teléfono</label>
            <input class="form-control" name="nuevoTelefono" autocomplete="off">
          </div>
          <div class="form-group">
            <label>Teléfono Actual</label>
            <input class="form-control" name="nuevoTelefonoActu" autocomplete="off">
          </div>
          <div class="form-group">
            <label>Link</label>
            <input class="form-control" name="nuevoLink" autocomplete="off">
          </div>
          <div class="form-group">
            <label>Usuario</label>
            <input class="form-control" name="nuevoUsuario" autocomplete="off" data-lpignore="true">
          </div>
          <div class="form-group">
            <label>Contraseña</label>
            <input class="form-control" name="nuevoContrasena" type="text" autocomplete="new-password" data-lpignore="true">
          </div>
          
          <!-- Campo oculto para IDs de comercios -->
          <input type="hidden" name="comercioIds" id="comercioIds">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
        <?php
          $crear = new ControladorContador();
          $crear->ctrCrearContador();
        ?>
      </form>
    </div>
  </div>
</div>

<!-- Modal Editar -->
<div id="modalEditarContador" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" id="formEditarContador">
        <div class="modal-header" style="background:#3c8dbc;color:white;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Editar Contador</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" id="idContador" name="idContador">
          
          <div class="form-group">
            <label>N°</label>
            <input id="editarNro" class="form-control" name="editarNro" readonly>
          </div>
          
          <!-- Comercio(s) - Select2 múltiple con búsqueda -->
          <div class="form-group">
            <label>Comercio(s) <span class="text-muted">(buscar por empresa)</span></label>
            <div id="comerciosContainerEditar">
              <div class="comercio-item" style="margin-bottom: 8px;">
                <select class="form-control select2-comercio-editar" name="editarComercios[]" style="width: calc(100% - 45px); display: inline-block;">
                </select>
                <button type="button" class="btn btn-danger btn-sm btn-quitar-comercio-editar" style="display: none;" title="Quitar">
                  <i class="fa fa-minus"></i>
                </button>
              </div>
            </div>
            <button type="button" class="btn btn-success btn-sm" id="btnAgregarComercioEditar" style="margin-top: 5px;">
              <i class="fa fa-plus"></i> Agregar otro comercio
            </button>
          </div>
          
          <div class="form-group">
            <label>Nombre Contador</label>
            <input id="editarNomContador" class="form-control" name="editarNomContador" autocomplete="off">
          </div>
          <div class="form-group">
            <label>Titular Tlf</label>
            <input id="editarTitularTlf" class="form-control" name="editarTitularTlf" autocomplete="off">
          </div>
          <div class="form-group">
            <label>Teléfono</label>
            <input id="editarTelefono" class="form-control" name="editarTelefono" autocomplete="off">
          </div>
          <div class="form-group">
            <label>Teléfono Actual</label>
            <input id="editarTelefonoActu" class="form-control" name="editarTelefonoActu" autocomplete="off">
          </div>
          <div class="form-group">
            <label>Link</label>
            <input id="editarLink" class="form-control" name="editarLink" autocomplete="off">
          </div>
          <div class="form-group">
            <label>Usuario</label>
            <input id="editarUsuario" class="form-control" name="editarUsuario" autocomplete="off" data-lpignore="true">
          </div>
          <div class="form-group">
            <label>Contraseña</label>
            <input id="editarContrasena" class="form-control" name="editarContrasena" type="text" autocomplete="new-password" data-lpignore="true">
          </div>
          
          <!-- Campo oculto para IDs de comercios -->
          <input type="hidden" name="comercioIdsEditar" id="comercioIdsEditar">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Editar</button>
        </div>
        <?php
          $editar = new ControladorContador();
          $editar->ctrEditarContador();
        ?>
      </form>
    </div>
  </div>
</div>

<script src="<?php echo BASE_URL; ?>/vistas/js/contadores.js"></script>
