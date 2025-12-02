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
    
    <!-- Búsqueda Avanzada -->
    <?php @include 'advanced_search.php'; ?>
    
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">Lista de Clientes (Oportunidades)</h3>
        <!-- Botón Agregar Cliente Postventa -->
        <button class="btn btn-success" data-toggle="modal" data-target="#modalAgregarClientePostventa" style="margin-left: 15px;">
          <i class="fa fa-plus"></i> Agregar Cliente
        </button>
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
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaClientes" data-column="col-empresa" checked>
                <label>Empresa</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaClientes" data-column="col-precio" checked>
                <label>Precio</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaClientes" data-column="col-rubro" checked>
                <label>Rubro</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaClientes" data-column="col-anio" checked>
                <label>Año</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaClientes" data-column="col-mes" checked>
                <label>Mes</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaClientes" data-column="col-link" checked>
                <label>Link</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaClientes" data-column="col-usuario" checked>
                <label>Usuario</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaClientes" data-column="col-contrasena" checked>
                <label>Contraseña</label>
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

      <div class="box-body">
        <table class="table table-bordered table-striped dt-responsive tabla" id="tablaClientes">
          <thead>
            <tr>
              <th data-column="col-numero">#</th>
              <th data-column="col-nombre">Nombre</th>
              <th data-column="col-tipo">Tipo</th>
              <th data-column="col-documento">Documento</th>
              <th data-column="col-telefono">Teléfono</th>
              <th data-column="col-correo">Observacion</th>
              <th data-column="col-ciudad">Ciudad</th>
              <th data-column="col-empresa">Empresa</th>
              <th data-column="col-precio">Precio</th>
              <th data-column="col-rubro">Rubro</th>
              <th data-column="col-anio">Año</th>
              <th data-column="col-mes">Mes</th>
              <th data-column="col-link">Link</th>
              <th data-column="col-usuario">Usuario</th>
              <th data-column="col-contrasena">Contraseña</th>
              <th data-column="col-cambiar-estado">Cambiar Estado</th>
              <th data-column="col-acciones">Acciones</th>
            </tr>
          </thead>
          <tbody>
          <?php
          // Mostrar clientes con estado 2 (clientes - oportunidades ganadas)
          $clientes = ControladorOportunidad::ctrMostrarClientes("estado", 2);
          $mesesNombre = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
          foreach ($clientes as $key => $value) {
              $mesNum = isset($value["post_mes"]) ? intval($value["post_mes"]) : 0;
              $mesNombre = isset($mesesNombre[$mesNum]) ? $mesesNombre[$mesNum] : '';
              echo '<tr>';
              echo '<td data-column="col-numero">'.($key+1).'</td>';
              echo '<td data-column="col-nombre">'.$value["nombre"].'</td>';
              echo '<td data-column="col-tipo">'.$value["tipo"].'</td>';
              echo '<td data-column="col-documento">'.$value["documento"].'</td>';
              echo '<td data-column="col-telefono">'.$value["telefono"].'</td>';
              echo '<td data-column="col-correo">'.$value["correo"].'</td>';
              echo '<td data-column="col-ciudad">'.$value["ciudad"].'</td>';
              echo '<td data-column="col-empresa">'.$value["empresa"].'</td>';
              echo '<td data-column="col-precio">'.($value["post_precio"] ?? '-').'</td>';
              echo '<td data-column="col-rubro">'.($value["post_rubro"] ?? '-').'</td>';
              echo '<td data-column="col-anio">'.($value["post_ano"] ?? '-').'</td>';
              echo '<td data-column="col-mes">'.$mesNombre.'</td>';
              echo '<td data-column="col-link">';
              if (!empty($value["post_link"])) {
                echo '<a href="'.htmlspecialchars($value["post_link"]).'" target="_blank" class="btn btn-xs btn-info"><i class="fa fa-external-link"></i></a>';
              } else {
                echo '-';
              }
              echo '</td>';
              echo '<td data-column="col-usuario">'.($value["post_usuario"] ?? '-').'</td>';
              echo '<td data-column="col-contrasena">'.($value["post_contrasena"] ?? '-').'</td>';
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
                        <button class="btn btn-warning btnEditarCliente" idCliente="'.$value["id"].'" data-toggle="modal" data-target="#modalActualizarClientes"><i class="fa fa-pencil"></i></button>
                        <button class="btn btn-info btnRegistrarIncidencia" idCliente="'.$value["id"].'" nombreCliente="'.$value["nombre"].'"><i class="fa fa-exclamation-triangle"></i> Incidencia</button>';
                        if($_SESSION["perfil"] !== "Vendedor") {
                          echo '<button class="btn btn-danger btnEliminarCliente" idCliente="'.$value["id"].'" data-ruta="clientes"><i class="fa fa-trash"></i></button>';
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
            <input type="hidden" id="rutaCliente" name="ruta" value="clientes">

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
            <!-- Campo oculto para motivo -->
            <input type="hidden" id="editarMotivo" name="editarMotivo" value="">
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

<!-- ===============================================
     MODAL AGREGAR CLIENTE POSTVENTA
=========================================== -->
<div id="modalAgregarClientePostventa" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="formAgregarClientePostventa" role="form" method="post">
        <div class="modal-header" style="background:#00a65a; color:white;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><i class="fa fa-user-plus"></i> Agregar Cliente Postventa</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <!-- Columna izquierda -->
            <div class="col-md-6">
              <!-- Comercio (empresa) -->
              <div class="form-group">
                <label for="nuevoComercio">Comercio <span style="color:red">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-building"></i></span>
                  <input type="text" class="form-control" id="nuevoComercio" name="nuevoComercio" placeholder="Nombre del comercio" required>
                </div>
              </div>

              <!-- Contacto (nombre) -->
              <div class="form-group">
                <label for="nuevoContacto">Contacto <span style="color:red">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-user"></i></span>
                  <input type="text" class="form-control" id="nuevoContacto" name="nuevoContacto" placeholder="Nombre del contacto" required>
                </div>
              </div>

              <!-- Celular (telefono) -->
              <div class="form-group">
                <label for="nuevoCelular">Celular <span style="color:red">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-mobile"></i></span>
                  <input type="text" class="form-control" id="nuevoCelular" name="nuevoCelular" placeholder="Número de celular" maxlength="15" required>
                </div>
              </div>

              <!-- Ciudad -->
              <div class="form-group">
                <label for="nuevaCiudad">Ciudad</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
                  <input type="text" class="form-control" id="nuevaCiudad" name="nuevaCiudad" placeholder="Ciudad">
                </div>
              </div>

              <!-- Precio -->
              <div class="form-group">
                <label for="nuevoPrecio">Precio <span style="color:red">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-money"></i></span>
                  <input type="number" step="0.01" min="0" class="form-control" id="nuevoPrecio" name="nuevoPrecio" placeholder="0.00" required>
                </div>
              </div>

              <!-- RUC (tipo preseleccionado + documento 11 dígitos) -->
              <div class="form-group">
                <label for="nuevoRuc">RUC <span style="color:red">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-id-card"></i></span>
                  <input type="hidden" id="nuevoTipo" name="nuevoTipo" value="RUC">
                  <input type="text" class="form-control" id="nuevoRuc" name="nuevoRuc" placeholder="11 dígitos" maxlength="11" pattern="\d{11}" title="Debe contener exactamente 11 dígitos" required>
                </div>
                <small class="text-muted">Exactamente 11 dígitos numéricos</small>
              </div>
            </div>

            <!-- Columna derecha -->
            <div class="col-md-6">
              <!-- Rubro -->
              <div class="form-group">
                <label for="nuevoRubro">Rubro</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-industry"></i></span>
                  <input type="text" class="form-control" id="nuevoRubro" name="nuevoRubro" placeholder="Rubro del negocio">
                </div>
              </div>

              <!-- Año -->
              <div class="form-group">
                <label for="nuevoAnio">Año <span style="color:red">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  <select class="form-control" id="nuevoAnio" name="nuevoAnio" required>
                    <option value="">Seleccionar año</option>
                    <?php 
                    $anioActual = date('Y');
                    for ($i = $anioActual; $i >= $anioActual - 5; $i--) {
                      $selected = ($i == $anioActual) ? 'selected' : '';
                      echo "<option value='$i' $selected>$i</option>";
                    }
                    ?>
                  </select>
                </div>
              </div>

              <!-- Mes -->
              <div class="form-group">
                <label for="nuevoMes">Mes <span style="color:red">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-calendar-o"></i></span>
                  <select class="form-control" id="nuevoMes" name="nuevoMes" required>
                    <option value="">Seleccionar mes</option>
                    <?php
                    $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                    $mesActual = date('n');
                    foreach ($meses as $num => $nombre) {
                      $valor = $num + 1;
                      $selected = ($valor == $mesActual) ? 'selected' : '';
                      echo "<option value='$valor' $selected>$nombre</option>";
                    }
                    ?>
                  </select>
                </div>
              </div>

              <!-- Link -->
              <div class="form-group">
                <label for="nuevoLink">Link</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-link"></i></span>
                  <input type="url" class="form-control" id="nuevoLink" name="nuevoLink" placeholder="https://...">
                </div>
              </div>

              <!-- Usuario (sin autocompletado) -->
              <div class="form-group">
                <label for="nuevoUsuario">Usuario</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-user-circle"></i></span>
                  <input type="text" class="form-control" id="nuevoUsuario" name="nuevoUsuario" placeholder="Usuario de acceso" autocomplete="off" data-lpignore="true" data-form-type="other">
                </div>
              </div>

              <!-- Contraseña (sin autocompletado) -->
              <div class="form-group">
                <label for="nuevoContrasena">Contraseña</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                  <input type="text" class="form-control" id="nuevoContrasena" name="nuevoContrasena" placeholder="Contraseña de acceso" autocomplete="new-password" data-lpignore="true" data-form-type="other">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Guardar Cliente</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Validación RUC - solo números y exactamente 11 dígitos
document.getElementById('nuevoRuc').addEventListener('input', function(e) {
  this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);
});

// AJAX para crear cliente postventa
document.getElementById('formAgregarClientePostventa').addEventListener('submit', function(e) {
  e.preventDefault();
  
  var ruc = document.getElementById('nuevoRuc').value;
  if (ruc.length !== 11) {
    Swal.fire({
      icon: 'warning',
      title: 'RUC inválido',
      text: 'El RUC debe tener exactamente 11 dígitos',
      confirmButtonColor: '#3c8dbc'
    });
    return;
  }
  
  var formData = new FormData(this);
  formData.append('action', 'crearClientePostventa');
  
  fetch('ajax/clientes.ajax.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    console.log('Respuesta crear cliente:', data);
    if (data.status === 'success') {
      Swal.fire({
        icon: 'success',
        title: '¡Cliente creado!',
        text: 'El cliente se ha registrado exitosamente',
        confirmButtonColor: '#3c8dbc'
      }).then(() => {
        $('#modalAgregarClientePostventa').modal('hide');
        document.getElementById('formAgregarClientePostventa').reset();
        location.reload();
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: data.message || 'No se pudo crear el cliente',
        confirmButtonColor: '#3c8dbc'
      });
    }
  })
  .catch(error => {
    console.error('Error:', error);
    Swal.fire({
      icon: 'error',
      title: 'Error de conexión',
      text: 'No se pudo conectar con el servidor',
      confirmButtonColor: '#3c8dbc'
    });
  });
});
</script>
