<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>Administrar Seguimiento</h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo BASE_URL; ?>/inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Administrar Seguimiento</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="box">
      <div class="box-header with-border">
<!--         <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarCliente">
          Agregar Cliente
        </button> -->
      <!-- Botón Mostrar/Ocultar Columnas -->
      <div class="column-toggle-container" style="margin-top:10px;">
        <button class="btn btn-default btn-toggle-columns" onclick="toggleColumnPanel(event)" title="Mostrar/Ocultar Columnas">
          <i class="fa fa-columns"></i> Mostrar/Ocultar Columnas
        </button>
        <div class="column-toggle-panel hidden">
          <h5>Mostrar/Ocultar Columnas</h5>
          <div class="column-toggle-list">
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaSeguimiento" data-column="col-numero" checked>
              <label>#</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaSeguimiento" data-column="col-nombre" checked>
              <label>Nombre</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaSeguimiento" data-column="col-tipo" checked>
              <label>Tipo</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaSeguimiento" data-column="col-documento" checked>
              <label>Documento</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaSeguimiento" data-column="col-telefono" checked>
              <label>Teléfono</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaSeguimiento" data-column="col-correo" checked>
              <label>Observacion</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaSeguimiento" data-column="col-ciudad" checked>
              <label>Ciudad</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaSeguimiento" data-column="col-migracion" checked>
              <label>Migración</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaSeguimiento" data-column="col-referencia" checked>
              <label>Referencia</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaSeguimiento" data-column="col-fecha-contacto" checked>
              <label>Fecha Contacto</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaSeguimiento" data-column="col-empresa" checked>
              <label>Empresa</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaSeguimiento" data-column="col-fecha-creacion" checked>
              <label>Fecha Creación</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaSeguimiento" data-column="col-cambiar-estado" checked>
              <label>Cambiar Estado</label>
            </div>
            <div class="column-toggle-item">
              <input type="checkbox" class="column-toggle-checkbox" data-table="tablaSeguimiento" data-column="col-acciones" checked>
              <label>Acciones</label>
            </div>
          </div>
        </div>

      </div>
      <?php include 'advanced_search.php'; ?>

      <div class="box-body">
        <table class="table table-bordered table-striped dt-responsive tabla" id="tablaSeguimiento">
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
          <?php
          // Aquí se cargarán los clientes en seguimiento (estado 1)
          $clientes = ControladorOportunidad::ctrMostrarClientes("estado", 1);
          foreach ($clientes as $key => $cliente) {
              echo '<tr>';
              echo '<td data-column="col-numero">'.($key+1).'</td>';
              echo '<td data-column="col-nombre">'.$cliente["nombre"].'</td>';
              echo '<td data-column="col-tipo">'.$cliente["tipo"].'</td>';
              echo '<td data-column="col-documento">'.$cliente["documento"].'</td>';
              echo '<td data-column="col-telefono">'.$cliente["telefono"].'</td>';
              echo '<td data-column="col-correo">'.$cliente["correo"].'</td>'; // Mostrar como Observacion
              echo '<td data-column="col-ciudad">'.$cliente["ciudad"].'</td>';
              echo '<td data-column="col-migracion">'.$cliente["migracion"].'</td>';
              echo '<td data-column="col-referencia">'.$cliente["referencia"].'</td>';
              echo '<td data-column="col-fecha-contacto">'.$cliente["fecha_contacto"].'</td>';
              echo '<td data-column="col-empresa">'.$cliente["empresa"].'</td>';
              echo '<td data-column="col-fecha-creacion">'.$cliente["fecha_creacion"].'</td>';
              echo '<td data-column="col-cambiar-estado">'
                   .'<select class="form-control input-sm select-estado-cliente" data-id="'.$cliente["id"].'">'
                      .'<option value="0"'.($cliente["estado"] == 0 ? ' selected' : '').'>Prospecto</option>'
                      .'<option value="1"'.($cliente["estado"] == 1 ? ' selected' : '').'>Seguimiento</option>'
                      .'<option value="2"'.($cliente["estado"] == 2 ? ' selected' : '').'>Cliente</option>'
                      .'<option value="3"'.($cliente["estado"] == 3 ? ' selected' : '').'>No Cliente</option>'
                      .'<option value="4"'.($cliente["estado"] == 4 ? ' selected' : '').'>En Espera</option>'
              .'</td>';
              echo '<td data-column="col-acciones">
                      <div class="btn-group">
                        <button class="btn btn-warning btnEditarCliente" idCliente="'.$cliente["id"].'" data-toggle="modal" data-target="#modalActualizarClientes"><i class="fa fa-pencil"></i></button>
                        <button class="btn btn-danger btnEliminarCliente" idCliente="'.$cliente["id"].'" data-ruta="seguimiento"><i class="fa fa-trash"></i></button>
                      </div>
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
     MODAL EDITAR CLIENTE EN SEGUIMIENTO
=========================================== -->
<?php include 'modulos/partials/modal_editar_cliente.php'; ?>

<script src="vistas/js/clientes.js"></script>


