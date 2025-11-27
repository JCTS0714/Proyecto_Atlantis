<?php
  // Listar contadores
  $contadores = ControladorContador::ctrMostrarContadores(null, null);
?>
<div class="content-wrapper">
  <section class="content-header">
    <h1>Contadores</h1>
  </section>
  <section class="content">
    <div class="box">
      <div class="box-header">
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarContador">Agregar Contador</button>
      </div>

      <?php include 'advanced_search.php'; ?>

      <div class="box-body">
        <table class="table table-bordered table-striped" id="tablaContadores">
          <thead>
            <tr>
              <th>#</th>
              <th>N°</th>
              <th>Comercio</th>
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
                <td><?php echo htmlspecialchars($c['comercio']); ?></td>
                <td><?php echo htmlspecialchars($c['nom_contador']); ?></td>
                <td><?php echo htmlspecialchars($c['telefono']); ?></td>
                <td><?php echo htmlspecialchars($c['usuario']); ?></td>
                <td>
                  <div class="btn-group">
                    <button class="btn btn-warning btnEditarContador" data-id="<?php echo $c['id']; ?>" data-toggle="modal" data-target="#modalEditarContador"><i class="fa fa-pencil"></i></button>
                    <a href="contadores?idContadorEliminar=<?php echo $c['id']; ?>" class="btn btn-danger"><i class="fa fa-trash"></i></a>
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
      <form method="post">
        <div class="modal-header" style="background:#3c8dbc;color:white;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Agregar Contador</h4>
        </div>
        <div class="modal-body">
          <!-- N° se genera automáticamente al crear (visible pero no editable) -->
          <div class="form-group"><label>N°</label><input id="nuevoNro" class="form-control" name="nuevoNro" readonly></div>
          <div class="form-group"><label>Comercio</label><input class="form-control" name="nuevoComercio" required></div>
          <div class="form-group"><label>Nombre Contador</label><input class="form-control" name="nuevoNomContador"></div>
          <div class="form-group"><label>Titular Tlf</label><input class="form-control" name="nuevoTitularTlf"></div>
          <div class="form-group"><label>Teléfono</label><input class="form-control" name="nuevoTelefono"></div>
          <div class="form-group"><label>Teléfono Actual</label><input class="form-control" name="nuevoTelefonoActu"></div>
          <div class="form-group"><label>Link</label><input class="form-control" name="nuevoLink"></div>
          <div class="form-group"><label>Usuario</label><input class="form-control" name="nuevoUsuario"></div>
          <div class="form-group"><label>Contraseña</label><input class="form-control" name="nuevoContrasena" type="password"></div>
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
      <form method="post">
        <div class="modal-header" style="background:#3c8dbc;color:white;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Editar Contador</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" id="idContador" name="idContador">
          <div class="form-group"><label>N°</label><input id="editarNro" class="form-control" name="editarNro" readonly></div>
          <div class="form-group"><label>Comercio</label><input id="editarComercio" class="form-control" name="editarComercio" required></div>
          <div class="form-group"><label>Nombre Contador</label><input id="editarNomContador" class="form-control" name="editarNomContador"></div>
          <div class="form-group"><label>Titular Tlf</label><input id="editarTitularTlf" class="form-control" name="editarTitularTlf"></div>
          <div class="form-group"><label>Teléfono</label><input id="editarTelefono" class="form-control" name="editarTelefono"></div>
          <div class="form-group"><label>Teléfono Actual</label><input id="editarTelefonoActu" class="form-control" name="editarTelefonoActu"></div>
          <div class="form-group"><label>Link</label><input id="editarLink" class="form-control" name="editarLink"></div>
          <div class="form-group"><label>Usuario</label><input id="editarUsuario" class="form-control" name="editarUsuario"></div>
          <div class="form-group"><label>Contraseña</label><input id="editarContrasena" class="form-control" name="editarContrasena" type="password"></div>
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
