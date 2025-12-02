<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>No Clientes</h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo BASE_URL; ?>/inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">No Clientes</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    
    <!-- Búsqueda Avanzada (fuera del box para evitar errores de render) -->
    <?php /* TEMPORALMENTE DESHABILITADO - include 'advanced_search.php'; */ ?>
    
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">Lista de No Clientes (Oportunidades Perdidas)</h3>
        <!-- Botón Mostrar/Ocultar Columnas -->
        <div class="column-toggle-container" style="margin-top:10px;">
        <button class="btn btn-default btn-toggle-columns" onclick="toggleColumnPanel(event)" title="Mostrar/Ocultar Columnas">
          <i class="fa fa-columns"></i> Mostrar/Ocultar Columnas
        </button>
        <div class="column-toggle-panel hidden">
          <h5>Mostrar/Ocultar Columnas</h5>
          <div class="column-toggle-list">
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaNoClientes" data-column="col-numero" checked>
              <label>#</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaNoClientes" data-column="col-nombre" checked>
              <label>Nombre</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaNoClientes" data-column="col-tipo" checked>
              <label>Tipo</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaNoClientes" data-column="col-documento" checked>
              <label>Documento</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaNoClientes" data-column="col-telefono" checked>
              <label>Teléfono</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaNoClientes" data-column="col-correo" checked>
              <label>Observacion</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaNoClientes" data-column="col-ciudad" checked>
              <label>Ciudad</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaNoClientes" data-column="col-migracion" checked>
              <label>Migración</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaNoClientes" data-column="col-referencia" checked>
              <label>Referencia</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaNoClientes" data-column="col-fecha-contacto" checked>
              <label>Fecha Contacto</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaNoClientes" data-column="col-empresa" checked>
              <label>Empresa</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaNoClientes" data-column="col-fecha-creacion" checked>
              <label>Fecha Creación</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaNoClientes" data-column="col-cambiar-estado" checked>
              <label>Cambiar Estado</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaNoClientes" data-column="col-acciones" checked>
              <label>Acciones</label>
            </div>
          </div>
        </div>
        </div>
      </div>

      <div class="box-body">
        <table class="table table-bordered table-striped dt-responsive tabla" id="tablaNoClientes">
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
          // LOG TEMPORAL: Verificar datos recibidos
          error_log("=== INICIO LOG NO CLIENTES ===");
          error_log("Consultando clientes con estado 3...");
          
          // Mostrar clientes con estado 3 (no-clientes - oportunidades perdidas)
          $noClientes = ControladorOportunidad::ctrMostrarClientes("estado", 3);
          
          error_log("Número de clientes encontrados: " . count($noClientes));
          if (!empty($noClientes)) {
              error_log("Clientes encontrados:");
              foreach ($noClientes as $key => $value) {
                  error_log("Cliente " . ($key+1) . ": ID=" . $value["id"] . ", Nombre=" . $value["nombre"] . ", Estado=" . $value["estado"]);
                  echo '<tr>';
                  echo '<td data-column="col-numero">'.($key+1).'</td>';
                  echo '<td data-column="col-nombre">'.$value["nombre"].'</td>';
                  echo '<td data-column="col-tipo">'.$value["tipo"].'</td>';
                  echo '<td data-column="col-documento">'.$value["documento"].'</td>';
                  echo '<td data-column="col-telefono">'.$value["telefono"].'</td>';
                  echo '<td data-column="col-correo">'.$value["correo"].'</td>'; // Muestra el valor almacenado en la columna 'correo' (ahora etiquetada Observacion)
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
                  echo '<td data-column="col-acciones">
                          <div class="btn-group">
                            <button class="btn btn-warning btnEditarCliente" idCliente="'.$value["id"].'" data-toggle="modal" data-target="#modalActualizarClientes"><i class="fa fa-pencil"></i></button>';
                            if($_SESSION["perfil"] !== "Vendedor") {
                              echo '<button class="btn btn-danger btnEliminarCliente" idCliente="'.$value["id"].'" data-ruta="no-clientes"><i class="fa fa-trash"></i></button>';
                            }
                  echo '    </div>
                        </td>';
                  echo '</tr>';
              }
          } else {
              error_log("No se encontraron clientes con estado 3");
              echo '<tr><td colspan="14" class="text-center">No hay clientes en esta lista</td></tr>';
          }
          
          error_log("=== FIN LOG NO CLIENTES ===");
          ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<!-- ===============================================
     MODAL EDITAR CLIENTE EN NO CLIENTES
=========================================== -->
<?php include 'partials/modal_editar_cliente.php'; ?>

<!-- Incluir script específico para No Clientes -->
<script src="vistas/js/clientes.js"></script>
<script src="vistas/js/no-clientes.js"></script>
<script>
// Si la URL contiene ?editar_id=ID se abre el modal de edición y se rellena
(function() {
  function getParam(name) {
    var regex = new RegExp('[\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? null : decodeURIComponent(results[1].replace(/\+/g, ' '));
  }

  var editarId = getParam('editar_id');
  var focusField = getParam('focus');
  if (editarId) {
    // Solicitar datos del cliente y rellenar modal (usa mismo endpoint que el listado)
    $.ajax({
      url: 'ajax/clientes.ajax.php',
      method: 'POST',
      data: { idCliente: editarId },
      dataType: 'json'
    }).done(function(data) {
      if (!data || !data.id) return;
      $('#idCliente').val(data.id);
      $('#editarNombre').val(data.nombre || '');
      $('#editarTipo').val(data.tipo || '');
      $('#editarDocumento').val(data.documento || '');
      $('#editarTelefono').val(data.telefono || '');
      $('#editarCorreo').val(data.correo || '');
      $('#editarCiudad').val(data.ciudad || '');
      $('#editarMigracion').val(data.migracion || '');
      $('#editarReferencia').val(data.referencia || '');
      $('#editarFechaContacto').val(data.fecha_contacto || '');
      $('#editarEmpresa').val(data.empresa || '');
      $('#editarFechaCreacion').val(data.fecha_creacion || '');

      // Mostrar modal y enfocar campo si se solicita
      $('#modalActualizarClientes').modal('show');
      if (focusField) {
        setTimeout(function() {
          var $f = $('#' + focusField);
          if ($f && $f.length) {
            $f.focus();
            // marcar visualmente con un pequeño highlight
            $f.css('box-shadow', '0 0 0 3px rgba(255,165,0,0.25)');
            setTimeout(function() { $f.css('box-shadow', ''); }, 2000);
          }
        }, 300);
      }

      // Limpiar parámetros de URL para evitar reapertura al recargar
      if (window.history && window.history.replaceState) {
        var url = new URL(window.location);
        url.searchParams.delete('editar_id');
        url.searchParams.delete('focus');
        window.history.replaceState({}, document.title, url.toString());
      }
    }).fail(function() {
      console.warn('No se pudieron obtener los datos del cliente para edición');
    });
  }
})();
</script>
