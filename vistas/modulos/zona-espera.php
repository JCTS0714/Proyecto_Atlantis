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
    
    <!-- Búsqueda Avanzada (fuera del box para evitar errores de render) -->
    <?php include 'advanced_search.php'; ?>
    
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">Clientes en Zona de Espera</h3>
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
<?php include 'modulos/partials/modal_editar_cliente.php'; ?>
<?php include 'modulos/partials/modal_info_cliente.php'; ?>
