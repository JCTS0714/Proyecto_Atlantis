<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>Administrar Prospectos</h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Administrar Prospectos</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="box">
      <div class="box-header with-border">
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarProspecto">
          Agregar Prospecto
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
              <th>Ciudad</th>
              <th>Migración</th>
              <th>Referencia</th>
              <th>Fecha Contacto</th>
              <th>Empresa</th>
              <th>Fecha Creación</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $item = "estado";
              $valor = 0; // Prospectos
              $prospectos = ControladorProspectos::ctrMostrarProspectos($item, $valor);

              foreach ($prospectos as $key => $value) {
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
                    <td><button class="btn '.$btnClass.' btn-xs btnEstadoCliente" idCliente="'.$value["id"].'" estadoCliente="'.$value["estado"].'">'.$estadoTexto.'</button></td>
<td>
  <div class="btn-group">
    <button class="btn btn-warning btnEditarCliente" idCliente="'.$value["id"].'" data-toggle="modal" data-target="#modalActualizarClientes"><i class="fa fa-pencil"></i></button>

    <?php if($_SESSION["perfil"] !== "Vendedor"): ?>
    <button class="btn btn-danger btnEliminarCliente" idCliente="'.$value["id"].'" data-ruta="prospectos"><i class="fa fa-times"></i></button>
    <?php endif; ?>
    <a href="index.php?ruta=crm&cliente_id='.$value["id"].'" class="btn btn-info" title="Nueva Oportunidad"><i class="fa fa-plus"></i></a>
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

<!-- Modal Agregar Prospecto -->
<div id="modalAgregarProspecto" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data">
        <div class="modal-header" style="background:#3c8dbc; color:white;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Agregar Prospecto</h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <!-- Campos para agregar prospecto -->
            <div class="form-group">
              <label for="nuevoNombre">Nombre <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                <input type="text" class="form-control input-lg" name="nuevoNombre" placeholder="Ingresar nombre" required>
              </div>
            </div>
            <div class="form-group">
            <label for="nuevoTipo">Tipo <span style="color:red">*</span></label>
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-users"></i></span>
              <select class="form-control input-lg" name="nuevoTipo" id="nuevoTipo" required>
                <option value="">Seleccionar tipo</option>
                <option value="DNI">DNI</option>
                <option value="RUC">RUC</option>
                <option value="otros" selected>otros</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="nuevoDocumento">Documento <span style="color:red">*</span></label>
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-address-card"></i></span>
              <input type="text" class="form-control input-lg" name="nuevoDocumento" id="nuevoDocumento" placeholder="Ingresar documento" value="55555555" required>
            </div>
          </div>
            <div class="form-group">
              <label for="nuevoTelefono">Teléfono <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-mobile"></i></span>
                <input type="text" class="form-control input-lg" name="nuevoTelefono" placeholder="Ingresar teléfono" maxlength="15" required>
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                  <input type="email" class="form-control input-lg" name="nuevoCorreo" placeholder="Ingresar correo">
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-home"></i></span>
                <input type="text" class="form-control input-lg" name="nuevoCiudad" placeholder="Ingresar ciudad" >
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-globe"></i></span>
                <input type="text" class="form-control input-lg" name="nuevoMigracion" placeholder="Ingresar migración" >
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-link"></i></span>
                <select class="form-control input-lg" name="nuevoReferencia">
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
              <label for="nuevoFechaContacto">Fecha de Contacto <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="date" class="form-control input-lg" name="nuevoFechaContacto" placeholder="Ingresar fecha de contacto" required>
              </div>
            </div>
            <div class="form-group">
              <label for="nuevoEmpresa">Empresa <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-building"></i></span>
                <input type="text" class="form-control input-lg" name="nuevoEmpresa" placeholder="Ingresar empresa" required>
              </div>
            </div>
            <div class="form-group" style="display:none;">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="date" class="form-control input-lg" name="nuevoFechaCreacion" placeholder="Ingresar fecha de creación">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Guardar Prospecto</button>
        </div>
          <?php
          $_POST['nuevoEstado'] = 0; // Estado 0 para prospectos
          $crearProspecto = new ControladorProspectos();
          $crearProspecto->ctrCrearProspecto();
          ?>
        <input type="hidden" name="nuevoEstado" value="0" />
        <input type="hidden" name="ruta" value="prospectos" />
      </form>
    </div>
  </div>
</div>

<!-- ===============================================
     MODAL EDITAR PROSPECTO
=========================================== -->
<div id="modalActualizarClientes" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data">
        <div class="modal-header" style="background:#3c8dbc; color:white;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Editar Prospecto</h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <input type="hidden" id="idCliente" name="idCliente">

            <!-- Campos editables similares -->
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
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                  <input type="email" class="form-control input-lg" id="editarCorreo" name="editarCorreo">
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-home"></i></span>
                <input type="text" class="form-control input-lg" id="editarCiudad" name="editarCiudad" >
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-globe"></i></span>
                <input type="text" class="form-control input-lg" id="editarMigracion" name="editarMigracion" >
              </div>
            </div>
            <div class="form-group">
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
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Editar Prospecto</button>
        </div>
          <?php
          $editarProspecto = new ControladorProspectos();
          $editarProspecto->ctrEditarProspecto();

          $eliminarProspecto = new ControladorProspectos();
          $eliminarProspecto->ctrEliminarProspecto();
        ?>
      </form>
    </div>
  </div>
</div>
<script src="vistas/js/clientes.js"></script>
<script src="vistas/js/prospectos_tipo_validacion.js"></script>

<script>
$(document).ready(function() {
  // Al cambiar el tipo, limpiar el documento
  $(document).on('change', '#nuevoTipo', function() {
    $('#nuevoDocumento').val('');
  });

  // Al cambiar el tipo en editar, limpiar el documento
  $(document).on('change', '#editarTipo', function() {
    $('#editarDocumento').val('');
  });

  // Al abrir el modal de crear, restablecer valores por defecto
  $('#modalAgregarProspecto').on('show.bs.modal', function() {
    $('#nuevoTipo').val('otros');
    $('#nuevoDocumento').val('55555555');
  });
});
</script>
