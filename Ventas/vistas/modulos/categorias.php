<!-- =============================================== -->

<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">



 <!-- Content Header (Page header) -->

 <section class="content-header">

  <h1>Administrar Categorías</h1>

  <ol class="breadcrumb">

   <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>

   <li class="active">Administrar Categorías</li>

  </ol>

 </section>



 <!-- Main content -->

 <section class="content">



  <!-- Default box -->

  <div class="box">

   <div class="box-header with-border">

    <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarCategoria">

     Agregar Categoría

    </button>

   </div>



   <div class="box-body">



    <!-- TABLA DE BOOTSTRAP -->

    <table class="table table-bordered table-striped dt-responsive" id="example2">

     <thead>

      <tr>

       <th>#</th>

       <th>Categoría</th>

       <th>Fecha</th>

       <th>Acciones</th>

      </tr>

     </thead>

     <tbody>

      <?php

       $item = null;

       $valor = null;

       $categorias = ControladorCategoria::ctrMostrarCategoria($item, $valor);



       foreach ($categorias as $key => $value) {

        echo '

         <tr>

          <td>'.($key + 1).'</td>

          <td>'.$value["categoria"].'</td>

          <td>'.$value["fecha"].'</td>

          <td>
           <div class="btn-group">
            <button class="btn btn-warning btnEditarcategoria" idCategoria="'.$value["id"].'" data-toggle="modal" data-target="#modalActualizarCategoria"><i class="fa fa-pencil"></i></button>

            <?php if($_SESSION["perfil"] !== "Vendedor"): ?>
            <button class="btn btn-danger btnEliminarCategoria" idCategoria="'.$value["id"].'"><i class="fa fa-times"></i></button>
            <?php endif; ?>

           </div>

          </td>

         </tr>';

       }

      ?>

     </tbody>

    </table>



   </div>



   <!-- /.box-body -->

   <div class="box-footer">Footer</div>

   <!-- /.box-footer -->



  </div>

  <!-- /.box -->



 </section>

 <!-- /.content -->



</div>

<!-- /.content-wrapper -->



<!-- ============================================== -->

<!-- MODAL AGREGAR CATEGORÍA NUEVA -->

<!-- ============================================== -->



<div id="modalAgregarCategoria" class="modal fade" role="dialog">

 <div class="modal-dialog">



  <!-- Modal content-->

  <div class="modal-content">

   <form role="form" method="post" enctype="multipart/form-data">



    <!-- HEADER DEL MODAL -->

    <div class="modal-header" style="background:#3c8dbc; color:white;">

     <button type="button" class="close" data-dismiss="modal">&times;</button>

     <h4 class="modal-title">Agregar Categoría</h4>

    </div>



    <!-- CUERPO DEL MODAL -->

    <div class="modal-body">

     <div class="box-body">

      <!-- CAMPO DE TEXTO PARA CATEGORÍA -->

      <div class="form-group">

       <label for="nuevaCategoria">Categoría <span style="color:red">*</span></label>

       <div class="input-group">

        <span class="input-group-addon"><i class="fa fa-user"></i></span>

        <input type="text" class="form-control input-lg" name="nuevaCategoria" placeholder="Ingresar Categoría" required>

       </div>

      </div>

     </div>

    </div>



    <!-- FOOTER DEL MODAL -->

    <div class="modal-footer">

     <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

     <button type="submit" class="btn btn-primary">Guardar Categoría</button>

    </div>



    <!-- LLAMAR AL MÉTODO PARA REGISTRAR LA CATEGORÍA -->

    <?php

     $crearCategoria = new ControladorCategoria();

     $crearCategoria->ctrCrearCategoria();

    ?>



   </form>

  </div>

 </div>

</div>



<!-- ============================================== -->

<!-- MODAL EDITAR CATEGORÍA -->

<!-- ============================================== -->



<div id="modalActualizarCategoria" class="modal fade" role="dialog">

 <div class="modal-dialog">



  <!-- Modal content-->

  <div class="modal-content">

   <form role="form" method="post" enctype="multipart/form-data">



    <!-- HEADER DEL MODAL -->

    <div class="modal-header" style="background:#3c8dbc; color:white;">

     <button type="button" class="close" data-dismiss="modal">&times;</button>

     <h4 class="modal-title">Actualizar Categoría</h4>

    </div>



    <!-- CUERPO DEL MODAL -->

    <div class="modal-body">

     <div class="box-body">

      <!-- CAMPO DE TEXTO PARA CATEGORÍA -->

      <div class="form-group">

       <label for="editarCategoria">Categoría <span style="color:red">*</span></label>

       <div class="input-group">

        <span class="input-group-addon"><i class="fa fa-user"></i></span>

        <input type="text" class="form-control input-lg" id="editarCategoria" name="editarCategoria"  required>
        <input type="hidden" id="idCategoria" name="idCategoria">

       </div>

      </div>

     </div>

    </div>



    <!-- FOOTER DEL MODAL -->

    <div class="modal-footer">

     <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

     <button type="submit" class="btn btn-primary">Actualizar Categoría</button>

    </div>



    <!-- LLAMAR AL MÉTODO PARA ELIMINAR LA CATEGORÍA -->

    <?php

     $eliminarCategoria = new ControladorCategoria();

     $eliminarCategoria->ctrEliminarCategoria();

    ?>

    <!-- LLAMAR AL MÉTODO PARA EDITAR LA CATEGORÍA -->  
    <?php

     $editarCategoria = new ControladorCategoria();

     $editarCategoria->ctrEditarCategoria();
    ?>



   </form>

  </div>

 </div>

</div>