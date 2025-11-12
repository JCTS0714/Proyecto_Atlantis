<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!-- Content Header -->
  <section class="content-header">
    <h1>Administrar Proveedores</h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Administrar Proveedores</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="box">
      <div class="box-header with-border">
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarProveedor">
          Agregar Proveedor
        </button>
      </div>

      <div class="box-body">
        <table class="table table-bordered table-striped dt-responsive tabla" id="example2">
          <thead>
            <tr>
              <th>#</th>
              <th>Razón Social</th>
              <th>RUC</th>
              <th>Dirección</th>
              <th>Teléfono</th>
              <th>Tipo Proveedor</th>
              <th>Fecha Registro</th>
              <th>Id Usuario</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
          <?php
            $item = null;
            $valor = null;
            $proveedores = ControladorProveedores::ctrMostrarProveedores($item, $valor);

            foreach ($proveedores as $key => $value) {
              echo '
              <tr>
                <td>' . ($key + 1) . '</td>
                <td>' . $value["razon_social"] . '</td>
                <td>' . $value["ruc"] . '</td>
                <td>' . $value["direccion"] . '</td>
                <td>' . $value["telefono"] . '</td>
                <td>' . $value["tipo_proveedor"] . '</td>
                <td>' . $value["fecha_registro"] . '</td>
                <td>' . $value["id_usuarios"] . '</td>
                <td>
                  <div class="btn-group">
                    <button class="btn btn-warning btnEditarProveedor" idProveedor="'.$value["id"].'" data-toggle="modal" data-target="#modalEditarProveedor"><i class="fa fa-pencil"></i></button>
                    <?php if($_SESSION["perfil"] !== "Vendedor"): ?>
                    <button class="btn btn-danger btnEliminarProveedor" idProveedor="'.$value["id"].'"><i class="fa fa-times"></i></button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>';
            }
          ?>
          </tbody>
        </table>
      </div>

      <div class="box-footer">Footer</div>
    </div>
  </section>
</div>

<!-- MODAL AGREGAR PROVEEDOR -->
<div id="modalAgregarProveedor" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data">

        <div class="modal-header" style="background:#3c8dbc; color:white;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Agregar Proveedor</h4>
        </div>

        <div class="modal-body">
          <div class="box-body">

            <div class="form-group">
              <label for="nuevoRazonsocial">Razón Social <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                <input type="text" class="form-control input-lg" name="nuevoRazonsocial" placeholder="Ingresar razón social" required>
              </div>
            </div>

            <div class="form-group">
              <label for="nuevoRuc">RUC <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-credit-card"></i></span>
                <input type="text" class="form-control input-lg" name="nuevoRuc" placeholder="Ingresar RUC" required>
              </div>
            </div>

            <div class="form-group">
              <label for="nuevoDireccion">Dirección <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-home"></i></span>
                <input type="text" class="form-control input-lg" name="nuevoDireccion" placeholder="Ingresar dirección" required>
              </div>
            </div>

            <div class="form-group">
              <label for="nuevoTelefono">Teléfono <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                <input type="number" class="form-control input-lg" name="nuevoTelefono" placeholder="Ingresar teléfono" required>
              </div>
            </div>

            <!-- CAMPO CORREGIDO: Tipo Proveedor -->
            <div class="form-group">
              <label for="nuevoTipo_proveedor">Tipo Proveedor <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-list"></i></span>
                <select class="form-control input-lg" name="nuevoTipo_proveedor" required>
                  <option value="">Seleccionar tipo proveedor</option>
                  <option value="Juridico">Juridico(20)</option>
                  <option value="Natural">Natural(10)</option>
                </select>
              </div>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Guardar Proveedor</button>
        </div>

        <?php
          $crearProveedor = new ControladorProveedores();
          $crearProveedor->ctrCrearProveedor();
        ?>
      </form>
    </div>
  </div>
</div>
<!-- MODAL EDITAR PROVEEDOR -->
<div id="modalEditarProveedor" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data">
        <div class="modal-header" style="background:#3c8dbc; color:white;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Editar Proveedor</h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <input type="hidden" id="editarIdProveedor" name="editarIdProveedor">
            <div class="form-group">
              <label for="editarRazonsocial">Razón Social <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                <input type="text" class="form-control input-lg" id="editarRazonsocial" name="editarRazonsocial" value="" required>
              </div>
            </div>
            <div class="form-group">
              <label for="editarRuc">RUC <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-credit-card"></i></span>
                <input type="text" class="form-control input-lg" id="editarRuc" name="editarRuc" value="" required>
              </div>
            </div>
            <div class="form-group">
              <label for="editarDireccion">Dirección <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-home"></i></span>
                <input type="text" class="form-control input-lg" id="editarDireccion" name="editarDireccion" value="" required>
              </div>
            </div>
            <div class="form-group">
              <label for="editarTelefono">Teléfono <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                <input type="number" class="form-control input-lg" id="editarTelefono" name="editarTelefono" value=""  required>
              </div>
            </div>
            <div class="form-group">
              <label for="editarTipoProveedor">Tipo Proveedor <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-list"></i></span>
                <select class="form-control input-lg" id="editarTipoProveedor" name="editarTipoProveedor" value="" required>
                  <option value="">Seleccionar tipo proveedor</option>
                  <option value="Juridico">Juridico(20)</option>
                  <option value="Natural">Natural(10)</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Actualizar Proveedor</button>
        </div>
        <?php
          $editarProveedor = new ControladorProveedores();
          $editarProveedor->ctrEditarProveedor();
        ?>
        <?php
            $eliminarProveedor = new ControladorProveedores();
            $eliminarProveedor->ctrEliminarProveedor();
        ?>
      </form>
    </div>
  </div>
</div>


