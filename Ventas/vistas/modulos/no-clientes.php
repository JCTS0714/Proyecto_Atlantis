<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>No Clientes</h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">No Clientes</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">Lista de No Clientes (Oportunidades Perdidas)</h3>
      </div>

      <div class="box-body">
        <table class="table table-bordered table-striped dt-responsive tabla" id="tablaNoClientes">
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
                  echo '<td><span class="label label-danger">No Cliente</span></td>';
                  echo '<td>
                          <div class="btn-group">
                            <button class="btn btn-warning btnEditarCliente" idCliente="'.$value["id"].'" data-toggle="modal" data-target="#modalEditarCliente"><i class="fa fa-pencil"></i></button>
                            <button class="btn btn-danger btnEliminarCliente" idCliente="'.$value["id"].'"><i class="fa fa-trash"></i></button>
                          </div>
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

<!-- Incluir script específico para No Clientes -->
<script src="vistas/js/no-clientes.js"></script>
