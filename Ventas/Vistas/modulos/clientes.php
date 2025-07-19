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
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarCliente">
          Agregar Cliente
        </button>
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
              <th>Dirección</th>
              <th>Clasificación</th>
              <th>Fecha Creación</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $item = null;
              $valor = null;
              $clientes = ControladorCliente::ctrMostrarCliente($item, $valor);

              foreach ($clientes as $key => $value) {
                echo '
                  <tr>
                    <td>'.($key + 1).'</td>
                    <td>'.$value["nombre"].'</td>
                    <td>'.$value["tipo"].'</td>
                    <td>'.$value["documento"].'</td>
                    <td>'.$value["telefono"].'</td>
                    <td>'.$value["correo"].'</td>
                    <td>'.$value["direccion"].'</td>
                    <td>'.$value["clasificacion"].'</td>
                    <td>'.$value["fecha_creacion"].'</td>
                    <td>
                      <div class="btn-group">
                        <button class="btn btn-warning btnEditarCliente" idCliente="'.$value["id"].'" data-toggle="modal" data-target="#modalActualizarClientes"><i class="fa fa-pencil"></i></button>
                        <button class="btn btn-danger btnEliminarCliente" idCliente="'.$value["id"].'"><i class="fa fa-times"></i></button>
                      </div>
                    </td>
                  </tr>';
              }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<!-- ===============================================
     MODAL AGREGAR CLIENTE
================================================== -->
<div id="modalAgregarCliente" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data">
        <div class="modal-header" style="background:#3c8dbc; color:white;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Agregar Cliente</h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <!-- Nombre -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                <input type="text" class="form-control input-lg" name="nuevoNombre" placeholder="Ingresar nombre" required>
              </div>
            </div>
            <!-- Tipo -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-users"></i></span>
                <select class="form-control input-lg" name="nuevoTipo" required>
                  <option value="">Seleccionar tipo</option>
                  <option value="persona">Persona</option>
                  <option value="empresa">Empresa</option>
                </select>
              </div>
            </div>
            <!-- Documento -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-address-card"></i></span>
                <input type="text" class="form-control input-lg" name="nuevoDocumento" placeholder="Ingresar documento" required>
              </div>
            </div>
            <!-- Teléfono -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-mobile"></i></span>
                <input type="text" class="form-control input-lg" name="nuevoTelefono" placeholder="Ingresar teléfono" required>
              </div>
            </div>
            <!-- Correo -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                <input type="email" class="form-control input-lg" name="nuevoCorreo" placeholder="Ingresar correo" required>
              </div>
            </div>
            <!-- Dirección -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-home"></i></span>
                <input type="text" class="form-control input-lg" name="nuevoDireccion" placeholder="Ingresar dirección" required>
              </div>
            </div>
            <!-- Clasificación -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-star"></i></span>
                <select class="form-control input-lg" name="nuevaClasificacion" required>
                  <option value="">Seleccionar clasificación</option>
                  <option value="nuevo">Nuevo</option>
                  <option value="recurrente">Recurrente</option>
                  <option value="vip">VIP</option>
                </select>
              </div>
            </div>
            <!-- Fecha de creación -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="date" class="form-control input-lg" name="nuevaFechaCreacion" required>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Guardar Cliente</button>
        </div>
        <?php
          $crearCliente = new ControladorCliente();
          $crearCliente->ctrCrearCliente();
        ?>
      </form>
    </div>
  </div>
</div>

<!--================================

 MODAL EDITAR USUARIO 

====================================-->


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
            <!-- Nombre -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                <input type="text" class="form-control input-lg" id="editarNombre" name="editarNombre" value="" required>
                <input type="hidden" id="idCliente" name="idCliente">
              </div>
            </div>
            <!-- Tipo -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-users"></i></span>
                <select class="form-control input-lg" id="editarTipo" name="editarTipo" required>
                  <option value="">Seleccionar tipo</option>
                  <option value="persona">Persona</option>
                  <option value="empresa">Empresa</option>
                </select>
              </div>
            </div>
            <!-- Documento -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-address-card"></i></span>
                <input type="text" class="form-control input-lg" id="editarDocumento" name="editarDocumento" value="" required>
              </div>
            </div>
            <!-- Teléfono -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-mobile"></i></span>
                <input type="text" class="form-control input-lg" id="editarTelefono" name="editarTelefono" value="" required>
              </div>
            </div>
            <!-- Correo -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                <input type="email" class="form-control input-lg" id="editarCorreo" name="editarCorreo" value="" required>
              </div>
            </div>
            <!-- Dirección -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-home"></i></span>
                <input type="text" class="form-control input-lg" id="editarDireccion" name="editarDireccion" value="" required>
              </div>
            </div>
            <!-- Clasificación -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-star"></i></span>
                <select class="form-control input-lg" id="editarClasificacion" name="editarClasificacion" required>
                  <option value="">Seleccionar clasificación</option>
                  <option value="nuevo">Nuevo</option>
                  <option value="recurrente">Recurrente</option>
                  <option value="vip">VIP</option>
                </select>
              </div>
            </div>
            <!-- Fecha de creación -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="date" class="form-control input-lg" id="editarFechaCreacion" name="editarFechaCreacion" value="" required>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Editar Cliente</button>
        </div>
        <?php
          $editarCliente = new ControladorCliente();
          $editarCliente->ctrEditarCliente();
        ?>
        <?php
         $eliminarCliente = new ControladorCliente();
         $eliminarCliente->ctrEliminarCliente();
        ?>
      </form>
    </div>
  </div>
</div>
              </div>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Editar Cliente</button>
        </div>
         <!--==========================

          LLAMAR AL MÉTODO PARA EDITAR A UN CLIENTE

          ==============================-->

        <?php
          $editarCliente = new ControladorCliente();
          $editarCliente->ctrEditarCliente();
        ?>
        <!-- LLAMAR AL MÉTODO PARA ELIMINAR LA CATEGORÍA -->

         <?php

         $eliminarCliente = new ControladorCliente();

         $eliminarCliente->ctrEliminarCliente();

         ?>


        </form>
      </div>
    </div>
</div>

