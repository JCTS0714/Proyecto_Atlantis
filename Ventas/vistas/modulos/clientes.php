<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>Administrar Clientes</h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo BASE_URL; ?>/inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Administrar Clientes</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">Lista de Clientes (Oportunidades)</h3>
        <!-- Botón Mostrar/Ocultar Columnas (igual que prospectos) -->
        <div class="column-toggle-container" style="margin-top:10px;">
          <button class="btn btn-default btn-toggle-columns" onclick="toggleColumnPanel(event)" title="Mostrar/Ocultar Columnas">
            <i class="fa fa-columns"></i> Mostrar/Ocultar Columnas
          </button>
          <div class="column-toggle-panel hidden">
            <h5>Mostrar/Ocultar Columnas</h5>
            <div class="column-toggle-list">
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaClientes" data-column="col-numero" checked>
                <label>#</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaClientes" data-column="col-nombre" checked>
                <label>Nombre</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaClientes" data-column="col-tipo" checked>
                <label>Tipo</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaClientes" data-column="col-documento" checked>
                <label>Documento</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaClientes" data-column="col-telefono" checked>
                <label>Teléfono</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaClientes" data-column="col-correo" checked>
                <label>Observacion</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaClientes" data-column="col-ciudad" checked>
                <label>Ciudad</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaClientes" data-column="col-migracion" checked>
                <label>Migración</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaClientes" data-column="col-referencia" checked>
                <label>Referencia</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaClientes" data-column="col-fecha-contacto" checked>
                <label>Fecha Contacto</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaClientes" data-column="col-empresa" checked>
                <label>Empresa</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaClientes" data-column="col-fecha-creacion" checked>
                <label>Fecha Creación</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaClientes" data-column="col-cambiar-estado" checked>
                <label>Cambiar Estado</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaClientes" data-column="col-acciones" checked>
                <label>Acciones</label>
              </div>
            </div>
          </div>
        </div>
      </div>

      <?php include 'advanced_search.php'; ?>

      <div class="box-body">
        <table class="table table-bordered table-striped dt-responsive tabla" id="tablaClientes" data-ajax="ajax/datatable-clientes.ajax.php">
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
          // Mostrar clientes con estado 2 (clientes - oportunidades ganadas)
          $clientes = ControladorOportunidad::ctrMostrarClientes("estado", 2);
          foreach ($clientes as $key => $value) {
              echo '<tr>';
              echo '<td data-column="col-numero">'.($key+1).'</td>';
              echo '<td data-column="col-nombre">'.$value["nombre"].'</td>';
              echo '<td data-column="col-tipo">'.$value["tipo"].'</td>';
              echo '<td data-column="col-documento">'.$value["documento"].'</td>';
              echo '<td data-column="col-telefono">'.$value["telefono"].'</td>';
              echo '<td data-column="col-correo">'.$value["correo"].'</td>';
              echo '<td data-column="col-ciudad">'.$value["ciudad"].'</td>';
              echo '<td data-column="col-migracion">'.$value["migracion"].'</td>';
              echo '<td data-column="col-referencia">'.$value["referencia"].'</td>';
              echo '<td data-column="col-fecha-contacto">'.$value["fecha_contacto"].'</td>';
              echo '<td data-column="col-empresa">'.$value["empresa"].'</td>';
              echo '<td data-column="col-fecha-creacion">'.$value["fecha_creacion"].'</td>';
              // Select para cambiar estado
              echo '<td data-column="col-cambiar-estado">'
                   .'<select class="form-control input-sm select-estado-cliente" data-id="'.$value["id"].'">'
                      .'<option value="0"'.($value["estado"] == 0 ? ' selected' : '').'>Prospecto</option>'
                      .'<option value="1"'.($value["estado"] == 1 ? ' selected' : '').'>Seguimiento</option>'
                      .'<option value="2"'.($value["estado"] == 2 ? ' selected' : '').'>Cliente</option>'
                      .'<option value="3"'.($value["estado"] == 3 ? ' selected' : '').'>No Cliente</option>'
                      .'<option value="4"'.($value["estado"] == 4 ? ' selected' : '').'>En Espera</option>'
                   .'</select>'
              .'</td>';
              echo '<td data-column="col-acciones">
                      <div class="btn-group">
                        <button class="btn btn-warning btnEditarCliente" idCliente="'.$value["id"].'" data-toggle="modal" data-target="#modalEditarCliente"><i class="fa fa-pencil"></i></button>
                        <button class="btn btn-info btnRegistrarIncidencia" idCliente="'.$value["id"].'" nombreCliente="'.$value["nombre"].'"><i class="fa fa-exclamation-triangle"></i> Incidencia</button>';
                        if($_SESSION["perfil"] !== "Vendedor") {
                          echo '<button class="btn btn-danger btnEliminarCliente" idCliente="'.$value["id"].'"><i class="fa fa-trash"></i></button>';
                        }
              echo '      </div>
                    </td>';
              echo '</tr>';
          }
          ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<!-- Incluir el modal de filtros de clientes -->
<?php include 'modal_filtros_clientes.php'; ?>

<!-- ===============================================
     MODAL EDITAR CLIENTE
=========================================== -->
<div id="modalEditarCliente" class="modal fade" role="dialog">
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
            <div class="form-group" style="display:none;">
              <label for="editarFechaCreacion">Fecha de Creación</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="date" class="form-control input-lg" id="editarFechaCreacion" name="editarFechaCreacion">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Editar Cliente</button>
        </div>
        <?php
          ControladorCliente::ctrEditarCliente();
        ?>
      </form>
    </div>
  </div>
</div>
