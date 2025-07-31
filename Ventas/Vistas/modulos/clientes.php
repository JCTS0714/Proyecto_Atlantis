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
<!--         <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarCliente">
          Agregar Cliente
        </button> -->
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
              <th>Ciudad</th>
              <th>Migración</th>
              <th>Referencia</th>
              <th>Fecha Contacto</th>
              <th>Empresa</th>
              <th>Fecha Creación</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
              <?php
              $item = "estado";
              $valor = 1; // Clientes
              $clientes = ControladorCliente::ctrMostrarCliente($item, $valor);

              foreach ($clientes as $key => $value) {
                $estadoTexto = ($value["estado"] == 0) ? "Prospecto" : "Cliente";
                $btnClass = ($value["estado"] == 0) ? "btn-warning" : "btn-success";

                echo '
                  <tr>
                    <td>'.($key + 1).'</td>
                    <td>'.$value["nombre"].'</td>
                    <td>'.$value["tipo"].'</td>
                    <td>'.$value["documento"].'</td>
                    <td>'.$value["telefono"].'</td>
                    <td>'.$value["correo"].'</td>
                    <td>'.$value["ciudad"].'</td>
                    <td>'.$value["migracion"].'</td>
                    <td>'.$value["referencia"].'</td>
                    <td>'.$value["fecha_contacto"].'</td>
                    <td>'.$value["empresa"].'</td>
                    <td>'.$value["fecha_creacion"].'</td>
<td><button class="btn ' . (($value["estado"] == 1) ? "btn-success" : "btn-warning") . ' btn-xs btnEstadoCliente" idCliente="' . $value["id"] . '" estadoCliente="' . $value["estado"] . '" style="min-width: 90px;">' . (($value["estado"] == 1) ? "Cliente" : "Prospecto") . '</button></td>
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
              ["icon" => "home", "type" => "text", "id" => "editarCiudad", "name" => "editarCiudad"],
              ["icon" => "globe", "type" => "text", "id" => "editarMigracion", "name" => "editarMigracion"],
              ["icon" => "link", "type" => "text", "id" => "editarReferencia", "name" => "editarReferencia"],
              ["icon" => "calendar", "type" => "date", "id" => "editarFechaContacto", "name" => "editarFechaContacto"],
              ["icon" => "building", "type" => "text", "id" => "editarEmpresa", "name" => "editarEmpresa"],
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
                  <option value="DNI">DNI</option>
                  <option value="RUC">RUC</option>
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
