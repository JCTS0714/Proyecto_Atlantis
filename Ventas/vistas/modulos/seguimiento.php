<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>Administrar Seguimiento</h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
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
              <th>Acciones</th>
            </tr>
          </thead>
          <?php
          // Aquí se cargarán los clientes en seguimiento (estado 1)
          $clientes = ControladorOportunidad::ctrMostrarClientes("estado", 1);
          foreach ($clientes as $key => $cliente) {
              echo '<tr>';
              echo '<td>'.($key+1).'</td>';
              echo '<td>'.$cliente["nombre"].'</td>';
              echo '<td>'.$cliente["tipo"].'</td>';
              echo '<td>'.$cliente["documento"].'</td>';
              echo '<td>'.$cliente["telefono"].'</td>';
              echo '<td>'.$cliente["correo"].'</td>';
              echo '<td>'.$cliente["ciudad"].'</td>';
              echo '<td>'.$cliente["migracion"].'</td>';
              echo '<td>'.$cliente["referencia"].'</td>';
              echo '<td>'.$cliente["fecha_contacto"].'</td>';
              echo '<td>'.$cliente["empresa"].'</td>';
              echo '<td>'.$cliente["fecha_creacion"].'</td>';
              echo '<td>
                      <div class="btn-group">
                        <button class="btn btn-warning btnEditarCliente" idCliente="'.$cliente["id"].'" data-toggle="modal" data-target="#modalEditarCliente"><i class="fa fa-pencil"></i></button>
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



