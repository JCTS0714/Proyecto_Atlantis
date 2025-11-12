<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>Administrar Clientes</h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Administrar Clientes</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">Lista de Clientes (Oportunidades Ganadas)</h3>
        <div class="box-tools pull-right">
          <button class="btn btn-info" data-toggle="modal" data-target="#modal-filtros-clientes">
            <i class="fa fa-filter"></i> Filtros
          </button>
        </div>
      </div>

      <div class="box-body">
        <table class="table table-bordered table-striped dt-responsive tabla" id="tablaClientes">
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
          // Mostrar clientes con estado 2 (clientes - oportunidades ganadas)
          $clientes = ControladorOportunidad::ctrMostrarClientes("estado", 2);
          foreach ($clientes as $key => $value) {
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
              echo '<td><span class="label label-success">Cliente</span></td>';
              echo '<td>
                      <div class="btn-group">
                        <button class="btn btn-warning btnEditarCliente" idCliente="'.$value["id"].'" data-toggle="modal" data-target="#modalEditarCliente"><i class="fa fa-pencil"></i></button>
                        <button class="btn btn-info btnRegistrarIncidencia" idCliente="'.$value["id"].'" nombreCliente="'.$value["nombre"].'"><i class="fa fa-exclamation-triangle"></i> Incidencia</button>
                        <?php if($_SESSION["perfil"] !== "Vendedor"): ?>
                        <button class="btn btn-danger btnEliminarCliente" idCliente="'.$value["id"].'"><i class="fa fa-trash"></i></button>
                        <?php endif; ?>
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

<!-- Incluir el modal de filtros de clientes -->
<?php include 'modal_filtros_clientes.php'; ?>
