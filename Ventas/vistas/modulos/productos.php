<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!-- Content Header -->
  <section class="content-header">
    <h1>Administrar Productos</h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Administrar Productos</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="box">
      <div class="box-header with-border">
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarProducto">
          Agregar Producto
        </button>
      </div>

      <div class="box-body">
        <table class="table table-bordered table-striped dt-responsive tabla" id="tablaProductos">
          <thead>
            <tr>
              <th>#</th>
              <th>Nombre</th>
              <th>Código</th>
              <th>Descripción</th>
              <th>Imagen</th>
              <th>Stock</th>
              <th>Precio Compra</th>
              <th>Precio Venta</th>
              <th>Ventas</th>
              <th>Fecha</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $item = null;
            $valor = null;
            $productos = ControladorProductos::ctrMostrarProductos($item, $valor);
            
            foreach ($productos as $key => $value) {
              echo '<tr>
                <td>'.($key+1).'</td>
                <td>'.$value["nombre"].'</td>
                <td>'.$value["codigo"].'</td>
                <td>'.$value["descripcion"].'</td>';
              
              if ($value["imagen"] != "" && file_exists($value["imagen"])) {
                echo '<td><img src="'.$value["imagen"].'" class="img-thumbnail" width="40"></td>';
              } else {
                echo '<td><img src="vistas/img/productos/default/producto.png" class="img-thumbnail" width="40"></td>';
              }

              echo '<td>'.$value["stock"].'</td>
                <td>S/ '.number_format($value["precio_compra"], 2).'</td>
                <td>S/ '.number_format($value["precio_venta"], 2).'</td>
                <td>'.$value["ventas"].'</td>
                <td>'.$value["fecha"].'</td>
                <td>
                  <div class="btn-group">
                    <button class="btn btn-warning btnEditarProducto" idProducto="'.$value["id"].'" data-toggle="modal" data-target="#modalEditarProducto"><i class="fa fa-pencil"></i>
                    </button>
                    <?php if($_SESSION["perfil"] !== "Vendedor"): ?>
                    <button class="btn btn-danger btnEliminarProducto" idProducto="'.$value["id"].'" fotoProducto="'.$value["imagen"].'" codigo="'.$value["codigo"].'"><i class="fa fa-times"></i>
                    </button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>';
            }
            ?>
          </tbody>
        </table>
      </div>

      <div class="box-footer">
        <small>Total de productos: <?php echo count($productos); ?></small>
      </div>
    </div>
  </section>
</div>

<!--=====================================
MODAL AGREGAR PRODUCTO
========================================-->
<div id="modalAgregarProducto" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data">
        <div class="modal-header" style="background:#3c8dbc; color:white;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Agregar producto</h4>
        </div>

        <div class="modal-body">
          <div class="box-body">
            <!-- NOMBRE -->
            <div class="form-group">
              <label for="nuevoNombre">Nombre <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-tag"></i></span>
                <input type="text" class="form-control input-lg" name="nuevoNombre" placeholder="Nombre del producto" required>
              </div>
            </div>

            <!-- CÓDIGO -->
            <div class="form-group">
              <label for="nuevoCodigo">Código <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-barcode"></i></span>
                <input type="text" class="form-control input-lg" name="nuevoCodigo" placeholder="Código" required>
              </div>
            </div>

            <!-- DESCRIPCIÓN -->
            <div class="form-group">
              <label for="nuevaDescripcion">Descripción <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-file-text"></i></span>
                <input type="text" class="form-control input-lg" name="nuevaDescripcion" placeholder="Descripción" required>
              </div>
            </div>

            <!-- CATEGORÍA -->
            <div class="form-group">
              <label for="nuevaCategoria">Categoría <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-folder"></i></span>
                <select class="form-control input-lg" name="nuevaCategoria" required>
                  <option value="">Seleccionar categoría</option>
                  <?php
                  $item = null;
                  $valor = null;
                  $categorias = ControladorCategoria::ctrMostrarCategoria($item, $valor);
                  foreach ($categorias as $key => $value) {
                    echo '<option value="'.$value["id"].'">'.$value["categoria"].'</option>';
                  }
                  ?>
                </select>
              </div>
            </div>

            <!-- STOCK -->
            <div class="form-group">
              <label for="nuevoStock">Stock <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-cubes"></i></span>
                <input type="number" class="form-control input-lg" name="nuevoStock" min="0" placeholder="Stock" required>
              </div>
            </div>

            <!-- PRECIOS -->
            <div class="row">
              <div class="col-xs-6">
                <div class="form-group">
                  <label for="nuevoPrecioCompra">Precio Compra <span style="color:red">*</span></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-arrow-up"></i></span>
                    <input type="number" class="form-control input-lg" name="nuevoPrecioCompra" min="0" step="0.01" placeholder="Precio compra" required>
                  </div>
                </div>
              </div>
              <div class="col-xs-6">
                <div class="form-group">
                  <label for="nuevoPrecioVenta">Precio Venta <span style="color:red">*</span></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-arrow-down"></i></span>
                    <input type="number" class="form-control input-lg" name="nuevoPrecioVenta" min="0" step="0.01" placeholder="Precio venta" required>
                  </div>
                </div>
              </div>
            </div>
            <!-- Ventas -->
            <div class="form-group">
              <label for="nuevaCantidadVentas">Cantidad de Ventas <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-shopping-cart"></i></span>
                <input type="number" class="form-control input-lg" name="nuevaCantidadVentas" placeholder="Cantidad de ventas" min="0" required>
              </div>
            </div>

            <!-- IMAGEN -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-image"></i></span>
                <input type="file" class="form-control input-lg nuevaFoto" name="nuevaFoto" accept="image/jpeg, image/png">
              </div>
              <p class="help-block">Peso máximo de la foto 2MB</p>
              <img src="vistas/img/productos/default/producto.png" class="img-thumbnail previsualizar" width="100px">
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Guardar producto</button>
        </div>
        <?php
          $crearProducto = new ControladorProductos();
          $crearProducto->ctrCrearProducto();
        ?>

      </form>
    </div>
  </div>
</div>
<!--=====================================
MODAL EDITAR PRODUCTO
========================================-->
<div id="modalEditarProducto" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data">
        <div class="modal-header" style="background:#3c8dbc; color:white;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Editar Producto</h4>
        </div>

        <div class="modal-body">
          <div class="box-body">
            <!-- NOMBRE -->
            <div class="form-group">
              <label for="editarNombre">Nombre <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-tag"></i></span>
                <input type="text" class="form-control input-lg" id="editarNombre" name="editarNombre" value="" required>
                <input type="hidden" name="idProducto" id="idProducto">
              </div>
            </div>

            <!-- CÓDIGO -->
            <div class="form-group">
              <label for="editarCodigo">Código <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-barcode"></i></span>
                <input type="text" class="form-control input-lg" id="editarCodigo" name="editarCodigo"  value="" required>
              </div>
            </div>

            <!-- DESCRIPCIÓN -->
            <div class="form-group">
              <label for="editarDescripcion">Descripción <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-file-text"></i></span>
                <input type="text" class="form-control input-lg" id="editarDescripcion" name="editarDescripcionProducto" value="" required>
              </div>
            </div>

            <!-- CATEGORÍA -->
            <div class="form-group">
              <label for="editarCategoria">Categoría <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-folder"></i></span>
                <select class="form-control input-lg" id="editarCategoria" name="editarCategoria"required>
                  <option value="">Seleccionar categoría</option>
                  <?php
                  $item = null;
                  $valor = null;
                  $categorias = ControladorCategoria::ctrMostrarCategoria($item, $valor);
                  foreach ($categorias as $key => $value) {
                    echo '<option value="'.$value["id"].'">'.$value["categoria"].'</option>';
                  }
                  ?>
                </select>
              </div>
            </div>

            <!-- STOCK -->
            <div class="form-group">
              <label for="editarStock">Stock <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-cubes"></i></span>
                <input type="number" class="form-control input-lg" id="editarStock" min="0" name="editarStock" value="" required>
              </div>
            </div>

            <!-- PRECIOS -->
            <div class="row">
              <div class="col-xs-6">
                <div class="form-group">
                  <label for="editarPrecioCompra">Precio Compra <span style="color:red">*</span></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-arrow-up"></i></span>
                    <input type="number" class="form-control input-lg" id="editarPrecioCompra" min="0" step="0.01" name="editarPrecioCompra" value="" required>
                  </div>
                </div>
              </div>
              <div class="col-xs-6">
                <div class="form-group">
                  <label for="editarPrecioVenta">Precio Venta <span style="color:red">*</span></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-arrow-down"></i></span>
                    <input type="number" class="form-control input-lg" id="editarPrecioVenta" min="0" step="0.01" name="editarPrecioVenta" value="" required>
                  </div>
                </div>
              </div>
            </div>
            <!-- Ventas -->
            <div class="form-group">
              <label for="editarCantidadVentas">Cantidad de Ventas <span style="color:red">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-shopping-cart"></i></span>
                <input type="number" class="form-control input-lg" id="editarCantidadVentas" name="editarCantidadVentas" min="0" value="" required>
              </div>
            </div>

            <!-- IMAGEN -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-image"></i></span>
                <input type="file" class="nuevaFoto" name="editarFoto" accept="image/jpeg, image/png">
              </div>
              <p class="help-block">Peso máximo de la foto 2MB</p>
              <img src="vistas/img/productos/default/producto.png" class="img-thumbnail previsualizarEditar previsualizar" width="100px">
              <input type="hidden" name="fotoActual" id="fotoActual">

            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Actualizar Producto</button>
        </div>
        <?php
          $editarProducto = new ControladorProductos();
          $editarProducto->ctrEditarProducto();
        ?>
        <!-- Eliminar producto -->
         <?php
          $eliminarProducto = new ControladorProductos();
          $eliminarProducto->ctrEliminarProducto();
        ?>
        
         </form>
    </div>
  </div>
</div>
