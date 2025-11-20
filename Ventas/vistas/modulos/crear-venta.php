<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Crear venta
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
        <li class="active">Crear venta</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <div class="row">

        <!-- FORMULARIO -->
        <div class="col-lg-5 col-xs-12">

          <div class="box box-success">

            <div class="box-header with-border"></div>

            <form role="form" method="post">

            <div class="box-body">

                <div class="box">

                  <!-- EL VENDEDOR -->

                  <div class="form-group">

                    <div class="input-group">

                      <span class="input-group-addon"><i class="fa fa-user"></i></span>
                      <input type="text" class="form-control" id="nuevoVendedor" name="nombre" value="<?php echo $_SESSION["nombre"];?>" readonly>
                       <!-- EL hidden sirve para que sea invisible -->
                      <input type="hidden"  id="idVendedor" name="idVendedor" value="<?php echo $_SESSION["id"];?>" readonly>
                    </div>

                  </div>

                  <!-- EL VENDEDOR -->

                  <div class="form-group">

                    <div class="input-group">

                      <span class="input-group-addon"><i class="fa fa-key"></i></span>

                      <?php
                      $item = null;
                      $valor = null;
                      $ventas = ControladorVentas::ctrMostrarVentas($item, $valor);

                      if(!$ventas){
                        echo '<input type="text" class="form-control" id="nuevaVenta" name="nuevaVenta" value="1001" readonly>';
                      } 
                      else 
                      {
                        foreach ($ventas as $key => $value) {
                          
                        }
                        $codigo = $value["codigo"]+1;
                        echo '<input type="text" class="form-control" id="nuevaVenta" name="nuevaVenta" value="'.$codigo.'" readonly>';

                      }

                      ?>
                      
                    </div>

                  </div>

                  <!-- EL CLIENTE -->

                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-users"></i></span>
                      <select class="form-control" id="seleccionarCliente" name="seleccionarCliente" requerid>
                        <option value="">Seleccionar cliente</option>
                        <?php
                        $item = null;
                        $valor = null;
                        $clientes = ControladorCliente::ctrMostrarCliente($item, $valor);
                        foreach ($clientes as $key => $value) {
                        echo '<option value="'.$value["id"].'">'.$value["nombre"].'</option>';
                      }
                      ?>
                      </select>
                      
                      <span class="input-group-addon"><button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#modalAgregarCliente" data-dismiss="modal">Agregar cliente</button></span>
                    </div>

                  </div>
                  <!-- CÓDIGO PARA AGREGAR PRODUCTO -->
                   <div class="form-group row nuevoProducto">

                    <!-- Descripción del producto --><!--
                    <div class="col-xs-6" style="padding-right:0px">

                      <div class="input-group">

                        <span class="input-group-addon"><button type="button" class="btn btn-danger btn-xs"><i class="fa fa-times"></i></button></span>
                        <input type="text" class="form-control" id="agregarProducto" name="agregarProducto" placeholder="Descripcion del producto" requerid>

                      </div>

                    </div>
                    <CANTIDAD del producto --><!--
                    <div class="col-xs-3">

                      <input type="number" class="form-control"  id="nuevaCantidadProducto" name="nuevaCantidadProducto" min="1" placeholder="0" requerid>

                    </div>
                      PRECIO del producto --><!--
                      <div class="col-xs-3" style="padding-left:px">

                        <div class="input-group">
                          <span class="input-group-addon"><i class="ion ion-social-usd"></i></span>
                          <input type="number" min="1" class="form-control" id="nuevoPrecioProducto" name="nuevoPrecioProducto" placeholder="000000" readonly requerid>
                          
                        </div>
                      </div>-->
                   </div>
                    <!-- BOTON PARA AGREGAR PRODUCTO -->
                   <button type="button" class="btn btn-default hidden-lg  btnAgregarProductos">Agregar producto</button>
                   <hr>
                   <div class="row">
                    <!-- IMPUESTO -->
                    <div class="col-xs-8 pull-right">

                      <table class="table">

                        <thead>
                          <tr>
                            <th>Impuesto</th>
                            <th>Total</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td style="width:50%">

                              <div class="input-group">

                                <input type="number" class="form-control" min="0" id="nuevoImpuestoVenta" name="nuevoImpuestoVenta" placeholder="0" requerid>
                                <span class="input-group-addon"><i class="fa fa-percent"></i></span>

                              </div>

                            </td style="width=50%">
                            <td>
                              <div class="input-group">

                                <span class="input-group-addon"><i class="ion ion-social-usd"></i></span>
                                <input type="number" min="1" class="form-control" id="nuevoTotalVenta" name="nuevoTotalVenta" placeholder="0000" readonly requerid>
                                

                              </div>
                            </td>
                          </tr>
                        </tbody>

                      </table>

                    </div>

                   </div>

                   <hr>
                   <!-- MEDIO DE PAGO -->
                   <div class="form-group row">

                    <div class="col-xs-6" style="padding-right:0px">
                      <div class="input-group">
                      <select class="form-control" name="nuevoMetodoPago" id="nuevoMetodoPago" requerid>
                        <option value="">Seleccione método de pago</option>
                        <option value="">Efectivo</option>
                        <option value="">Tarjeta de crédito</option>
                        <option value="">Tarjeta de debito</option>
                      </select>
                   </div>
                    </div>

                      <div class="col-xs-6" style="padding-left:0px">

                        <div class="input-group">

                          <input type="text" class="form-control" id="nuevoCodigoTransaccion" name="nuevoCodigoTransaccion" placeholder="Código de transacción" requerid>
                          <span class="input-group-addon"><i class="fa fa-lock"></i></span>

                          </select>
                        </div>

                      </div>
                      

                   </div>
                   <br>
                   
                </div>

                
    
            </div>
            <div class="box-footer">
  
              <button type="submit" class="btn btn-primary pull-right">Guardar venta</button>

            </div>

            </form>

          </div>

        </div>

        <!-- LA TABLA CON LOS PRODUCTOS -->

        <div class="col-lg-7 hidden-md hidden-sm hidden-xs">

          <div class="box box-warning">

            <div class="box-header with-border"></div>

            <div class="box-body">

              <table class="table table-bordered table-striped dt-responsive tablaVentas">
                <thead>
                  <tr>
                    <th style="width:10px"></th>
                    <th>Imágen</th>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>1.</td>
                    <td><img src="vistas/img/productos/default/producto.png" class="img-thumbnail" width="40px"></td>
                    <td>00123</td>
                    <td>Lorem ipsum</td>
                    <td>20</td>
                    <td>
                      <div class="btn-group">
                        <button type="button" class="btn btn-primary">Agregar</button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>

            </div>


          </div>

        </div><!-- PARA PANTALLA MAS PEQUEÑAS SE OCULTA LA TABLA -->

      </div>

    </section>
    <!-- /.content -->
  </div>
  <!-- /AGREGAR CLIENTE -->
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
              <label for="nuevoNombre">Nombre <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                <input type="text" class="form-control input-lg" name="nuevoNombre" placeholder="Ingresar nombre" required>
              </div>
            </div>

            <!-- Documento -->
            <div class="form-group">
              <label for="nuevoDocumento">Documento <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-address-card"></i></span>
                <input type="number" class="form-control input-lg" name="nuevoDocumento" placeholder="Ingresar documento" required>
              </div>
            </div>

            <!-- Observacion (antes Email) -->
            <div class="form-group">
              <label for="nuevoEmail">Observacion <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                <input type="text" class="form-control input-lg" name="nuevoEmail" placeholder="Ingresar Observacion" required>
              </div>
            </div>

            <!-- Teléfono -->
            <div class="form-group">
              <label for="nuevoTelefono">Teléfono <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-mobile"></i></span>
                <input type="number" class="form-control input-lg" name="nuevoTelefono" placeholder="Ingresar teléfono" required>
              </div>
            </div>

            <!-- Dirección -->
            <div class="form-group">
              <label for="nuevoDireccion">Dirección <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-home"></i></span>
                <input type="text" class="form-control input-lg" name="nuevoDireccion" placeholder="Ingresar dirección" required>
              </div>
            </div>

            <!-- Fecha de nacimiento -->
            <div class="form-group">
              <label for="nuevaFechaN">Fecha de Nacimiento <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="date" class="form-control input-lg" name="nuevaFechaN" placeholder="Ingresar fecha de nacimiento" required>
              </div>
            </div>

            <!-- Compras -->
            <div class="form-group">
              <label for="nuevoCompra">Cantidad de Compras <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-shopping-cart"></i></span>
                <input type="number" class="form-control input-lg" name="nuevoCompra" placeholder="Cantidad de compras" required>
              </div>
            </div>

            <!-- Última compra -->
            <div class="form-group">
              <label for="nuevoUltimaCompra">Fecha de Última Compra <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-shopping-bag"></i></span>
                <input type="date" class="form-control input-lg" name="nuevoUltimaCompra" placeholder="Fecha de última compra" required>
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