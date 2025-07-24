<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>Administrar Clientes</h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Administrar Clientes</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="box">
      <div class="box-header with-border">
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarCliente">
          Agregar Cliente
        </button>
      </div>

      <div class="box-body">
        <table class="table table-bordered table-striped dt-responsive tabla" id="example2">
          <thead>
            <tr>
              <th>#</th>
              <th>Nombre</th>
              <th>Tipo</th>
              <th>Documento</th>
              <th>Teléfono</th>
              <th>Correo</th>
              <th>Dirección</th>
              <th>Fecha Creación</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $item = null;
              $valor = null;
              $clientes = ControladorCliente::ctrMostrarCliente($item, $valor);

              foreach ($clientes as $key => $value) {
                echo '
                  <tr>
                    <td>'.($key + 1).'</td>
                    <td>'.$value["nombre"].'</td>
                    <td>'.$value["tipo"].'</td>
                    <td>'.$value["documento"].'</td>
                    <td>'.$value["telefono"].'</td>
                    <td>'.$value["correo"].'</td>
                    <td>'.$value["direccion"].'</td>
                    <td>'.$value["fecha_creacion"].'</td>
                    <td>
                      <div class="btn-group">
                        <button class="btn btn-warning btnEditarCliente" idCliente="'.$value["id"].'" data-toggle="modal" data-target="#modalActualizarClientes"><i class="fa fa-pencil"></i></button>
                        <button class="btn btn-danger btnEliminarCliente" idCliente="'.$value["id"].'"><i class="fa fa-times"></i></button>
                      </div>
                    </td>
                  </tr>';
              }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<!-- ===============================================
     MODAL AGREGAR CLIENTE
================================================== -->
<div id="modalAgregarCliente" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data">
        <div class="modal-header" style="background:#3c8dbc; color:white;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Agregar Cliente</h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <!-- Campos de formulario -->
            <?php
              $campos = [
                ["icon" => "user", "type" => "text", "name" => "nuevoNombre", "placeholder" => "Ingresar nombre"],
                ["icon" => "address-card", "type" => "text", "name" => "nuevoDocumento", "placeholder" => "Ingresar documento"],
                ["icon" => "mobile", "type" => "text", "name" => "nuevoTelefono", "placeholder" => "Ingresar teléfono"],
                ["icon" => "envelope", "type" => "email", "name" => "nuevoCorreo", "placeholder" => "Ingresar correo"],
                ["icon" => "home", "type" => "text", "name" => "nuevoDireccion", "placeholder" => "Ingresar dirección"]
              ];
              foreach ($campos as $campo) {
                echo '
                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-'.$campo["icon"].'"></i></span>
                    <input type="'.$campo["type"].'" class="form-control input-lg" name="'.$campo["name"].'" placeholder="'.$campo["placeholder"].'" required>
                  </div>
                </div>';
              }
            ?>

            <!-- Tipo -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-users"></i></span>
                <select class="form-control input-lg" name="nuevoTipo" required>
                  <option value="">Seleccionar tipo</option>
                  <option value="persona">Persona</option>
                  <option value="empresa">Empresa</option>
                </select>
              </div>
            </div>


            <!-- Fecha de creación -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="date" class="form-control input-lg" name="nuevaFechaCreacion" required>
              </div>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Guardar Cliente</button>
        </div>

        <?php
          $crearCliente = new ControladorCliente();
          $crearCliente->ctrCrearCliente();
        ?>
      </form>
    </div>
  </div>
</div>

<!-- ==========================================
     MODAL EDITAR CLIENTE
=========================================== -->
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

            <!-- Campos editables similares -->
            <?php
              $camposEditar = [
                ["icon" => "user", "type" => "text", "id" => "editarNombre", "name" => "editarNombre"],
                ["icon" => "address-card", "type" => "text", "id" => "editarDocumento", "name" => "editarDocumento"],
                ["icon" => "mobile", "type" => "text", "id" => "editarTelefono", "name" => "editarTelefono"],
                ["icon" => "envelope", "type" => "email", "id" => "editarCorreo", "name" => "editarCorreo"],
                ["icon" => "home", "type" => "text", "id" => "editarDireccion", "name" => "editarDireccion"],
                ["icon" => "calendar", "type" => "date", "id" => "editarFechaCreacion", "name" => "editarFechaCreacion"]
              ];
              foreach ($camposEditar as $campo) {
                echo '
                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-'.$campo["icon"].'"></i></span>
                    <input type="'.$campo["type"].'" class="form-control input-lg" id="'.$campo["id"].'" name="'.$campo["name"].'" required>
                  </div>
                </div>';
              }
            ?>

            <!-- Tipo -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-users"></i></span>
                <select class="form-control input-lg" id="editarTipo" name="editarTipo" required>
                  <option value="">Seleccionar tipo</option>
                  <option value="persona">Persona</option>
                  <option value="empresa">Empresa</option>
                </select>
              </div>
            </div>

            

          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Editar Cliente</button>
        </div>

        <?php
          $editarCliente = new ControladorCliente();
          $editarCliente->ctrEditarCliente();

          $eliminarCliente = new ControladorCliente();
          $eliminarCliente->ctrEliminarCliente();
        ?>
      </form>
    </div>
  </div>
</div>
