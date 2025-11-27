<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>Zona de Espera</h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo BASE_URL; ?>/inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Zona de Espera</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">Clientes en Zona de Espera</h3>
      </div>
      <!-- Botón Mostrar/Ocultar Columnas -->
      <div class="column-toggle-container" style="margin-top:10px;">
        <button class="btn btn-default btn-toggle-columns" onclick="toggleColumnPanel(event)" title="Mostrar/Ocultar Columnas">
          <i class="fa fa-columns"></i> Mostrar/Ocultar Columnas
        </button>
        <div class="column-toggle-panel hidden">
          <h5>Mostrar/Ocultar Columnas</h5>
          <div class="column-toggle-list">
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaZonaEspera" data-column="col-numero" checked>
              <label>#</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaZonaEspera" data-column="col-nombre" checked>
              <label>Nombre</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaZonaEspera" data-column="col-tipo" checked>
              <label>Tipo</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaZonaEspera" data-column="col-documento" checked>
              <label>Documento</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaZonaEspera" data-column="col-telefono" checked>
              <label>Teléfono</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaZonaEspera" data-column="col-correo" checked>
              <label>Observacion</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaZonaEspera" data-column="col-motivo" checked>
              <label>Motivo</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaZonaEspera" data-column="col-ciudad" checked>
              <label>Ciudad</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaZonaEspera" data-column="col-migracion" checked>
              <label>Migración</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaZonaEspera" data-column="col-referencia" checked>
              <label>Referencia</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaZonaEspera" data-column="col-fecha-contacto" checked>
              <label>Fecha Contacto</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaZonaEspera" data-column="col-empresa" checked>
              <label>Empresa</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaZonaEspera" data-column="col-fecha-creacion" checked>
              <label>Fecha Creación</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaZonaEspera" data-column="col-cambiar-estado" checked>
              <label>Cambiar Estado</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaZonaEspera" data-column="col-acciones" checked>
              <label>Acciones</label>
            </div>
          </div>
        </div>

            <script>
            // Abrir modal de edición y enfocar motivo si la URL contiene ?open_motivo_id=ID
            $(function(){
              try {
                var params = new URLSearchParams(window.location.search);
                if (params.has('open_motivo_id')) {
                  var idCliente = params.get('open_motivo_id');
                  if (idCliente) {
                    var datos = new FormData();
                    datos.append('idCliente', idCliente);
                    fetch('ajax/clientes.ajax.php', { method: 'POST', body: datos })
                      .then(function(res){ return res.json(); })
                      .then(function(cliente){
                        if (!cliente || !cliente.id) return;
                        // Rellenar campos del modal de edición
                        $('#idCliente').val(cliente.id || '');
                        $('#editarNombre').val(cliente.nombre || '');
                        $('#editarTipo').val(cliente.tipo || '');
                        $('#editarDocumento').val(cliente.documento || '');
                        $('#editarTelefono').val(cliente.telefono || '');
                        $('#editarCorreo').val(cliente.correo || '');
                        $('#editarMotivo').val(cliente.motivo || '');
                        $('#editarCiudad').val(cliente.ciudad || '');
                        $('#editarMigracion').val(cliente.migracion || '');
                        $('#editarReferencia').val(cliente.referencia || '');
                        $('#editarFechaContacto').val(cliente.fecha_contacto || '');
                        $('#editarEmpresa').val(cliente.empresa || '');
                        // Mostrar modal y enfocar motivo
                        $('#modalActualizarClientes').modal('show');
                        setTimeout(function(){ $('#editarMotivo').focus(); }, 300);
                        // Remover el parámetro de la URL
                        params.delete('open_motivo_id');
                        var newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                        window.history.replaceState({}, document.title, newUrl);
                      }).catch(function(err){ console.error('Error al cargar cliente para motivo', err); });
                  }
                }
              } catch(e) { console.error(e); }
            });
            </script>
      </div>

      <div class="box-body">
        <table class="table table-bordered table-striped dt-responsive tabla" id="tablaZonaEspera">
          <thead>
            <tr>
              <th data-column="col-numero">#</th>
              <th data-column="col-nombre">Nombre</th>
              <th data-column="col-tipo">Tipo</th>
              <th data-column="col-documento">Documento</th>
              <th data-column="col-telefono">Teléfono</th>
              <th data-column="col-correo">Observacion</th>
              <th data-column="col-motivo">Motivo</th>
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
          // Mostrar clientes con estado 4 (zona de espera - CORREGIDO: era 5, ahora es 4)
          $zonaEspera = ControladorOportunidad::ctrMostrarClientes("estado", 4);
          foreach ($zonaEspera as $key => $value) {
              echo '<tr>';
              echo '<td data-column="col-numero">'.($key+1).'</td>';
              echo '<td data-column="col-nombre">'.$value["nombre"].'</td>';
              echo '<td data-column="col-tipo">'.$value["tipo"].'</td>';
              echo '<td data-column="col-documento">'.$value["documento"].'</td>';
              echo '<td data-column="col-telefono">'.$value["telefono"].'</td>';
              echo '<td data-column="col-correo">'.$value["correo"].'</td>'; // Mostrar como Observacion
              echo '<td data-column="col-motivo">'.(isset($value["motivo"]) ? $value["motivo"] : '').'</td>';
              echo '<td data-column="col-ciudad">'.$value["ciudad"].'</td>';
              echo '<td data-column="col-migracion">'.$value["migracion"].'</td>';
              echo '<td data-column="col-referencia">'.$value["referencia"].'</td>';
              echo '<td data-column="col-fecha-contacto">'.$value["fecha_contacto"].'</td>';
              echo '<td data-column="col-empresa">'.$value["empresa"].'</td>';
              echo '<td data-column="col-fecha-creacion">'.$value["fecha_creacion"].'</td>';
              echo '<td data-column="col-cambiar-estado">'
                   .'<select class="form-control input-sm select-estado-cliente" data-id="'.$value["id"].'">'
                      .'<option value="0"'.($value["estado"] == 0 ? ' selected' : '').'>Prospecto</option>'
                      .'<option value="1"'.($value["estado"] == 1 ? ' selected' : '').'>Seguimiento</option>'
                      .'<option value="2"'.($value["estado"] == 2 ? ' selected' : '').'>Cliente</option>'
                      .'<option value="3"'.($value["estado"] == 3 ? ' selected' : '').'>No Cliente</option>'
                      .'<option value="4"'.($value["estado"] == 4 ? ' selected' : '').'>En Espera</option>'
                   .'</select>'
              .'</td>';
              echo '<td data-column="col-acciones">'
                  .'<div class="btn-group">'
                  .'<button class="btn btn-warning btnEditarCliente" idCliente="'.$value["id"].'" data-toggle="modal" data-target="#modalActualizarClientes"><i class="fa fa-pencil"></i></button>'
                  .'<button class="btn btn-success btnReactivarCliente" idCliente="'.$value["id"].'"><i class="fa fa-refresh"></i></button>'
                  .'<button class="btn btn-info btnInfoCliente" idCliente="'.$value["id"].'" title="Info"><i class="fa fa-info"></i></button>';
                      if($_SESSION["perfil"] !== "Vendedor") {
                        echo '<button class="btn btn-danger btnEliminarCliente" idCliente="'.$value["id"].'" data-ruta="zona-espera"><i class="fa fa-trash"></i></button>';
                      }
              echo '    </div>
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

<!-- ===============================================
     MODAL EDITAR CLIENTE EN ZONA DE ESPERA
=========================================== -->
<div id="modalActualizarClientes" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data">
        <div class="modal-header" style="background:#3c8dbc; color:white;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Editar Cliente en Zona de Espera</h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <input type="hidden" id="idCliente" name="idCliente">
            <input type="hidden" name="ruta" value="zona-espera">

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
              <label for="editarMotivo">Motivo</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-sticky-note"></i></span>
                <textarea class="form-control input-lg" id="editarMotivo" name="editarMotivo" rows="2" placeholder="Escribir motivo de zona de espera"></textarea>
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

<script src="vistas/js/clientes.js"></script>

<!-- MODAL INFO CLIENTE (SOLO LECTURA) -->
<div id="modalInfoCliente" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background:#00a65a; color:white;">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Información - Cliente / Oportunidad</h4>
      </div>
      <div class="modal-body">
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active"><a href="#tabProspecto" aria-controls="tabProspecto" role="tab" data-toggle="tab">Prospecto</a></li>
          <li role="presentation"><a href="#tabOportunidad" aria-controls="tabOportunidad" role="tab" data-toggle="tab">Oportunidad</a></li>
        </ul>

        <div class="tab-content" style="margin-top:15px;">
          <div role="tabpanel" class="tab-pane active" id="tabProspecto">
            <div class="row">
              <div class="col-md-6"><b>Nombre:</b> <span id="infoNombre"></span></div>
              <div class="col-md-6"><b>Tipo:</b> <span id="infoTipo"></span></div>
            </div>
            <div class="row" style="margin-top:8px;">
              <div class="col-md-6"><b>Documento:</b> <span id="infoDocumento"></span></div>
              <div class="col-md-6"><b>Teléfono:</b> <span id="infoTelefono"></span></div>
            </div>
            <div class="row" style="margin-top:8px;">
              <div class="col-md-6"><b>Observación:</b> <span id="infoCorreo"></span></div>
              <div class="col-md-6"><b>Motivo:</b> <span id="infoMotivo"></span></div>
            </div>
            <div class="row" style="margin-top:8px;">
              <div class="col-md-6"><b>Ciudad:</b> <span id="infoCiudad"></span></div>
              <div class="col-md-6"><b>Migración:</b> <span id="infoMigracion"></span></div>
            </div>
            <div class="row" style="margin-top:8px;">
              <div class="col-md-6"><b>Referencia:</b> <span id="infoReferencia"></span></div>
              <div class="col-md-6"><b>Empresa:</b> <span id="infoEmpresa"></span></div>
            </div>
            <div class="row" style="margin-top:8px;">
              <div class="col-md-6"><b>Fecha de Contacto:</b> <span id="infoFechaContacto"></span></div>
              <div class="col-md-6"><b>Fecha de Creación:</b> <span id="infoFechaCreacion"></span></div>
            </div>
          </div>

          <div role="tabpanel" class="tab-pane" id="tabOportunidad">
            <div class="row">
              <div class="col-md-12"><b>Título:</b> <span id="infoOportTitulo"></span></div>
            </div>
            <div class="row" style="margin-top:8px;">
              <div class="col-md-12"><b>Descripción:</b> <div id="infoOportDescripcion"></div></div>
            </div>
            <div class="row" style="margin-top:8px;">
              <div class="col-md-4"><b>Valor Estimado:</b> <span id="infoOportValor"></span></div>
              <div class="col-md-4"><b>Probabilidad:</b> <span id="infoOportProbabilidad"></span></div>
              <div class="col-md-4"><b>Estado:</b> <span id="infoOportEstado"></span></div>
            </div>
            <div class="row" style="margin-top:8px;">
              <div class="col-md-6"><b>Fecha Cierre Estimada:</b> <span id="infoOportFechaCierre"></span></div>
              <div class="col-md-6"><b>Actividad:</b> <span id="infoOportActividad"></span></div>
            </div>
            <div class="row" style="margin-top:8px;">
              <div class="col-md-6"><b>Fecha Actividad:</b> <span id="infoOportFechaActividad"></span></div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
