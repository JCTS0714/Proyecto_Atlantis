<?php
// Procesar edición de cliente/prospecto antes de renderizar
if (isset($_POST["editarNombre"]) && isset($_POST["idCliente"])) {
  ControladorCliente::ctrEditarCliente();
}
?>
<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>Retirados</h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo BASE_URL; ?>/inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Retirados</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    
    <!-- Búsqueda Avanzada -->
    <?php @include 'advanced_search.php'; ?>
    
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">Lista de Retirados</h3>
        <div class="export-buttons-container pull-right" id="export-tablaRetirados"></div>
      </div>

      <div class="box-body">
        <table class="table table-bordered table-striped dt-responsive tabla" id="tablaRetirados">
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
              <th data-column="col-cambiar-estado" class="no-export">Cambiar Estado</th>
              <th data-column="col-acciones" class="no-export">Acciones</th>
            </tr>
          </thead>
          <tbody>
          <?php
            // Mostrar clientes con estado 5 (Retirados)
            $retirados = ControladorOportunidad::ctrMostrarClientes("estado", 5);

            if (!empty($retirados)) {
              foreach ($retirados as $key => $value) {
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
                echo '<td data-column="col-cambiar-estado">';
                echo '<select class="form-control input-sm select-estado-cliente" data-id="'.$value["id"].'">'
                  .'<option value="0"'.($value["estado"] == 0 ? ' selected' : '').'>Prospecto</option>'
                  .'<option value="1"'.($value["estado"] == 1 ? ' selected' : '').'>Seguimiento</option>'
                  .'<option value="2"'.($value["estado"] == 2 ? ' selected' : '').'>Cliente</option>'
                  .'<option value="3"'.($value["estado"] == 3 ? ' selected' : '').'>No Cliente</option>'
                  .'<option value="4"'.($value["estado"] == 4 ? ' selected' : '').'>En Espera</option>'
                  .'<option value="5"'.($value["estado"] == 5 ? ' selected' : '').'>Retirados</option>'
                  .'</select>';
                echo '</td>';
                echo '<td data-column="col-acciones">';
                echo '<div class="btn-group">';
                echo '<button class="btn btn-warning btnEditarCliente" idCliente="'.$value["id"].'" data-toggle="modal" data-target="#modalActualizarClientes"><i class="fa fa-pencil"></i></button>';
                if($_SESSION["perfil"] !== "Vendedor") {
                  echo '<button class="btn btn-danger btnEliminarCliente" idCliente="'.$value["id"].'" data-ruta="retirados"><i class="fa fa-trash"></i></button>';
                }
                echo '</div>';
                echo '</td>';
                echo '</tr>';
              }
            } else {
              echo '<tr><td colspan="14" class="text-center">No hay clientes en esta lista</td></tr>';
            }
          ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<!-- MODAL EDITAR CLIENTE -->
<?php include 'partials/modal_editar_cliente.php'; ?>

<!-- Scripts -->
<script src="vistas/js/clientes.js"></script>
<script src="vistas/js/no-clientes.js"></script>

<script>
// Abrir modal si ?editar_id=ID
(function() {
  function getParam(name) {
    var regex = new RegExp('[\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? null : decodeURIComponent(results[1].replace(/\+/g, ' '));
  }

  var editarId = getParam('editar_id');
  if (editarId) {
    $.ajax({
      url: 'ajax/clientes.ajax.php',
      method: 'POST',
      data: { idCliente: editarId },
      dataType: 'json'
    }).done(function(data) {
      if (!data || !data.id) return;
      $('#idCliente').val(data.id);
      $('#editarNombre').val(data.nombre);
      $('#editar_documento').val(data.documento);
      $('#editar_telefono').val(data.telefono);
      $('#editar_correo').val(data.correo);
      $('#editar_ciudad').val(data.ciudad);
      $('#editar_empresa').val(data.empresa);
      $('#editar_estado').val(data.estado);
      $('#modalActualizarClientes').modal('show');
    });
  }
})();
</script>
