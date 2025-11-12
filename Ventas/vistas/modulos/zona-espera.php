<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>Zona de Espera</h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Zona de Espera</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">Clientes en Zona de Espera</h3>
      </div>

      <div class="box-body">
        <table class="table table-bordered table-striped dt-responsive tabla" id="tablaZonaEspera">
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
          // Mostrar clientes con estado 4 (zona de espera - CORREGIDO: era 5, ahora es 4)
          $zonaEspera = ControladorOportunidad::ctrMostrarClientes("estado", 4);
          foreach ($zonaEspera as $key => $value) {
              echo '<tr>';
              echo '<td>'.($key+1).'</td>';
              echo '<td>'.$value["nombre"].'</td>';
              echo '<td>'.$value["tipo"].'</td>';
              echo '<td>'.$value["documento"].'</td>';
              echo '<td>'.$value["telefono"].'</td>';
              echo '<td>'.$value["correo"].'</td>';
              echo '<td>'.$value["ciudad"].'</td>';
              echo '<td>'.$value["migracion"].'</td>';
              echo '<td>'.$value["referencia"].'</td>';
              echo '<td>'.$value["fecha_contacto"].'</td>';
              echo '<td>'.$value["empresa"].'</td>';
              echo '<td>'.$value["fecha_creacion"].'</td>';
              echo '<td><span class="label label-warning">En Espera</span></td>';
              echo '<td>
                      <div class="btn-group">
                      <button class="btn btn-warning btnEditarCliente" idCliente="'.$value["id"].'" data-toggle="modal" data-target="#modalEditarCliente"><i class="fa fa-pencil"></i></button>
                      <button class="btn btn-success btnReactivarCliente" idCliente="'.$value["id"].'"><i class="fa fa-refresh"></i></button>
                      <button class="btn btn-danger btnEliminarCliente" idCliente="'.$value["id"].'"><i class="fa fa-trash"></i></button>
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

<script src="Vistas/js/clientes.js"></script>
