<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>Administrar Prospectos</h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo BASE_URL; ?>/inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
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
        <!-- Botón Mostrar/Ocultar Columnas -->
        <div class="column-toggle-container">
          <button class="btn btn-default btn-toggle-columns" onclick="toggleColumnPanel(event)" title="Mostrar/Ocultar Columnas">
            <i class="fa fa-columns"></i> Mostrar/Ocultar Columnas
          </button>
          <div class="column-toggle-panel hidden">
            <h5>Mostrar/Ocultar Columnas</h5>
            <div class="column-toggle-list">
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="example2" data-column="col-numero" checked>
                <label>#</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="example2" data-column="col-nombre" checked>
                <label>Nombre</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="example2" data-column="col-tipo" checked>
                <label>Tipo</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="example2" data-column="col-documento" checked>
                <label>Documento</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="example2" data-column="col-telefono" checked>
                <label>Teléfono</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="example2" data-column="col-correo" checked>
                <label>Observacion</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="example2" data-column="col-ciudad" checked>
                <label>Ciudad</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="example2" data-column="col-migracion" checked>
                <label>Migración</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="example2" data-column="col-referencia" checked>
                <label>Referencia</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="example2" data-column="col-fecha-contacto" checked>
                <label>Fecha Contacto</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="example2" data-column="col-empresa" checked>
                <label>Empresa</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="example2" data-column="col-fecha-creacion" checked>
                <label>Fecha Creación</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="example2" data-column="col-cambiar-estado" checked>
                <label>Cambiar Estado</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="example2" data-column="col-acciones" checked>
                <label>Acciones</label>
              </div>
            </div>
          </div>
        </div>
      </div>

      <?php include 'advanced_search.php'; ?>

      <div class="box-body">
        <table class="table table-bordered table-striped dt-responsive tabla" id="example2">
          <thead>
            <tr>
              <th data-column="col-numero">#</th>
              <th data-column="col-nombre">Nombre</th>
              <th data-column="col-tipo">Tipo</th>
              <th data-column="col-documento">Documento</th>
              <th data-column="col-telefono">Teléfono</th>
              <th data-column="col-correo">Observacion</th>
              <th data-column="col-ciudad">Ciudad</th>
              <th data-column="col-migracion">Migración</th>
              <th data-column="col-referencia">Referencia</th>
              <th data-column="col-fecha-contacto">Fecha Contacto</th>
              <th data-column="col-empresa">Empresa</th>
              <th data-column="col-fecha-creacion">Fecha Creación</th>
              <th data-column="col-cambiar-estado">Cambiar Estado</th>
              <th data-column="col-acciones">Acciones</th>
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
                    <td data-column="col-numero">'.($key + 1).'</td>
                    <td data-column="col-nombre">'.$value["nombre"].'</td>
                    <td data-column="col-tipo">'.$value["tipo"].'</td>
                    <td data-column="col-documento">'.$value["documento"].'</td>
                    <td data-column="col-telefono">'.$value["telefono"].'</td>
                    <td data-column="col-correo">'.$value["correo"].'</td>
                    <td data-column="col-ciudad">'.$value["ciudad"].'</td>
                    <td data-column="col-migracion">'.$value["migracion"].'</td>
                    <td data-column="col-referencia">'.$value["referencia"].'</td>
                    <td data-column="col-fecha-contacto">'.$value["fecha_contacto"].'</td>
                    <td data-column="col-empresa">'.$value["empresa"].'</td>
                    <td data-column="col-fecha-creacion">'.$value["fecha_creacion"].'</td>
                    <td data-column="col-cambiar-estado">
                      <select class="form-control input-sm select-estado-cliente" data-id="'.$value["id"].'">
                        <option value="0"'.($value["estado"] == 0 ? ' selected' : '').'>Prospecto</option>
                        <option value="1"'.($value["estado"] == 1 ? ' selected' : '').'>Seguimiento</option>
                        <option value="2"'.($value["estado"] == 2 ? ' selected' : '').'>Cliente</option>
                        <option value="3"'.($value["estado"] == 3 ? ' selected' : '').'>No Cliente</option>
                        <option value="4"'.($value["estado"] == 4 ? ' selected' : '').'>En Espera</option>
                      </select>
                    </td>
                    <td data-column="col-acciones">
                      <div class="btn-group">
                        <button class="btn btn-warning btnEditarCliente" idCliente="'.$value["id"].'" data-toggle="modal" data-target="#modalActualizarClientes"><i class="fa fa-pencil"></i></button>
                        <?php if($_SESSION["perfil"] !== "Vendedor"): ?>
                        <button class="btn btn-danger btnEliminarCliente" idCliente="'.$value["id"].'" data-ruta="prospectos"><i class="fa fa-times"></i></button>
                        <?php endif; ?>
                        <a href="'.BASE_URL.'/crm?cliente_id='.$value["id"].'" class="btn btn-info" title="Nueva Oportunidad"><i class="fa fa-plus"></i></a>
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
                  <input type="text" class="form-control input-lg" name="nuevoCorreo" placeholder="Ingresar Observacion">
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

<?php include 'modulos/partials/modal_editar_cliente.php'; ?>
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

  // El manejo de cambio de estado de clientes se realiza de forma centralizada en `vistas/js/clientes.js`.
  // Este archivo debe incluirse antes de este script para que el comportamiento sea consistente.
});
</script>
