<!-- =============================================== -->
<?php
// Procesar acciones de usuario ANTES de renderizar HTML
// Esto asegura que los mensajes de Swal se muestren correctamente
try {
    if (class_exists('ControladorUsuarios')) {
        ControladorUsuarios::ctrEditarUsuario();
        ControladorUsuarios::ctrBorrarUsuario();
    } else {
        error_log("ERROR: Clase ControladorUsuarios no encontrada en usuarios.php");
    }
} catch (Exception $e) {
    error_log("ERROR en usuarios.php: " . $e->getMessage());
}
?>

 <!-- Content Wrapper. Contains page content -->

 <div class="content-wrapper">

  <!-- Content Header (Page header) -->

  <section class="content-header">

   <h1>
    Administrar Usuarios
   </h1>

   <ol class="breadcrumb">

    <li><a href="<?php echo BASE_URL; ?>/inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>

    <li class="active">Administrar Usuarios</li>

   </ol>

  </section>



  <!-- Main content -->

  <section class="content">



   <!-- Default box -->

   <div class="box">

    <div class="box-header with-border">

     

     <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarUsuario">Agregar usuario</button>



    </div>

    <div class="box-body">

     <!-- TABLA DE BOOSTRAP -->

     <table class="table table-bordered table-striped dt-responsive" id="example2">

      <thead>

       <tr>

        <th>#</th>

        <th>Nombre</th>

        <th>Usuario</th>

        <th>Foto</th>

        <th>Perfil</th>

        <th>Estado</th>

        <th>Último login</th>

        <th>Acciones</th>

       </tr>

      </thead>

      <tbody>

        <!-- Esto se agrego el 13 -->
      <?php

      $item = null;
      $valor = null;
      $usuarios = ControladorUsuarios::ctrMostrarUsuarios($item,$valor);
      //var_dump($usuarios);
      foreach($usuarios as $key => $value){
        //var_dump($value["id"]);

        echo '
       <tr>

        <td>'.($key + 1).'</td>

        <td>'.$value["nombre"].'</td>

        <td>'.$value["usuario"].'</td>';

        if($value["foto"] != ""){
            echo '<td><img src="'.$value["foto"].'" class="img-thumbnail" width="40px"></td>';
        }
        else
        {
            echo '<td><img src="vistas/img/usuarios/default/perfil.png" class="img-thumbnail" width="40px"></td>';

        }

         echo '<td>'.$value["perfil"].'</td>';


       if ($value["estado"] == 1) {
  // Activado
          echo '<td><button class="btn btn-success btn-xs btnActivar" idUsuario="'.$value["id"].'" estadoUsuario="1">Activado</button></td>';
        } else {
          // Desactivado
          echo '<td><button class="btn btn-danger btn-xs btnActivar" idUsuario="'.$value["id"].'" estadoUsuario="0">Desactivado</button></td>';
        }
    
        echo '<td>'.$value["ultimo_login"].'</td>
        <td>

         <div class="btn-group">

          <button class="btn btn-warning btnEditarUsuario" idUsuario="'.$value["id"].'" data-toggle="modal" data-target="#modalEdiatrUsuario"><i class="fa fa-pencil"></i></button>

          <?php if($_SESSION["perfil"] !== "Vendedor"): ?>
           <button class="btn btn-danger btnEliminarUsuario" idUsuario="'.$value["id"].'" fotoUsuario="'.$value["foto"].'" usuario="'.$value["usuario"].'"><i class="fa fa-times"></i></button>
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

    <div class="box-footer">

     Footer

    </div>

    <!-- /.box-footer-->

   </div>

   <!-- /.box -->

  </section>

  <!-- /.content -->

 </div>

 <!-- /.content-wrapper -->

 

<!--================================

 MODAL AGREGAR USUARIO NUEVO

====================================-->

<!-- Modal -->

<div id="modalAgregarUsuario" class="modal fade" role="dialog">

 <div class="modal-dialog">



  <!-- Modal content-->

  <div class="modal-content">



   <form role="form" method="post" enctype="multipart/form-data"> <!-- enctype sirve para enviar archivos-->

   

   <!-- HEADER DEL MODAL-->

   <div class="modal-header" style="background:#3c8dbc; color:white;">



    <button type="button" class="close" data-dismiss="modal">&times;</button>

    <h4 class="modal-title">Agregar usuario</h4>

   </div>



   <!-- CUERPO DEL MODAL-->

   <div class="modal-body">

    <div class="box-body">

     <!-- CAMPO DE TEXTO PARA NOMBRE-->

     <div class="form-group">

      <label for="nuevoNombre">Nombre <span style="color:red">*</span></label>

      <div class="input-group">

       <span class="input-group-addon"><i class="fa fa-user"></i></span>

       <input type="text" class="form-control input-lg" name="nuevoNombre" placeholder="Ingresar nombre" requerid>

      </div>

     </div>

     <!-- CAMPO DE TEXTO PARA USUARIO-->

     <div class="form-group">

      <label for="nuevoUsuario">Usuario <span style="color:red">*</span></label>

      <div class="input-group">

       <span class="input-group-addon"><i class="fa fa-key"></i></span>

       <input type="text" class="form-control input-lg" name="nuevoUsuario" placeholder="Ingresar usuario" requerid>

      </div>

     </div>

     <!-- CAMPO DE TEXTO PARA CONTRASEÑA-->

     <div class="form-group">

      <label for="nuevoPassword">Contraseña <span style="color:red">*</span></label>

      <div class="input-group">

       <span class="input-group-addon"><i class="fa fa-lock"></i></span>

       <input type="password" class="form-control input-lg" name="nuevoPassword" placeholder="Ingresar contraseña" requerid>

      </div>

     </div>

     <!-- CAMPO PARA SELECCIONAR PERFIL-->

      <div class="form-group">

      <label for="nuevoPerfil">Perfil <span style="color:red">*</span></label>

      <div class="input-group">



       <span class="input-group-addon"><i class="fa fa-users"></i></span>



       <select class="form-control input-lg" name="nuevoPerfil">

        <option value="">Seleccionar perfil</option>

        <option value="Admnistrador">Administrador</option>

        <option value="Especial">Especial</option>

        <option value="Vendedor">Vendedor</option>

       </select>



      </div>

      </div>

     <!-- CAMPO PARA SUBIR FOTO DE USUARIO-->

     <div class="form-group">

      <div class="panel">Subir foto</div>

      <input type="file" class="nuevaFoto" name="nuevaFoto" style="display:none;">

      <button type="button" class="btn btn-default btnElegirArchivo">Elegir archivo</button>
      <span class="nombreArchivoSeleccionado" style="margin-left:10px;">Ningún archivo seleccionado</span>

      <p class="help-block">Peso maximo de la foto 2MB</p>

      <img src="vistas/img/usuarios/default/perfil.png" class="img-thumbnail previsualizar" width="100px">

     </div>    

    </div>

   </div>

   <!-- FOOTER O PIE DEL MODAL-->

   <div class="modal-footer">

    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

    <button type="submit" class="btn btn-primary">Guardar usuario</button>

   </div>



   <!--==========================

   LLAMAR AL MÉTODO PARA REGISTRAR A UN USUARIO

   ==============================-->

   <?php

    $crearUsuario = new ControladorUsuarios();

    $crearUsuario->ctrCrearUsuario();

   ?>



   </form>

  </div>

 </div>

</div>





<!--================================

 MODAL EDITAR USUARIO 

====================================-->

<!-- Modal -->

<div id="modalEdiatrUsuario" class="modal fade" role="dialog">

 <div class="modal-dialog">



  <!-- Modal content-->

  <div class="modal-content">



   <form role="form" method="post" enctype="multipart/form-data"> <!-- enctype sirve para enviar archivos-->

   

   <!-- HEADER DEL MODAL-->

   <div class="modal-header" style="background:#3c8dbc; color:white;">



    <button type="button" class="close" data-dismiss="modal">&times;</button>

    <h4 class="modal-title">Editar usuario</h4>

   </div>



   <!-- CUERPO DEL MODAL-->

   <div class="modal-body">

    <div class="box-body">

     <!-- CAMPO DE TEXTO PARA NOMBRE-->

     <div class="form-group">

      <label for="editarNombre">Nombre <span style="color:red">*</span></label>

      <div class="input-group">

       <span class="input-group-addon"><i class="fa fa-user"></i></span>

       <input type="text" class="form-control input-lg" id="editarNombre" name="editarNombre" value="" requerid>

      </div>

     </div>

     <!-- CAMPO DE TEXTO PARA USUARIO-->

     <div class="form-group">

      <div class="input-group">

       <span class="input-group-addon"><i class="fa fa-key"></i></span>

       <input type="text" class="form-control input-lg" id="editarUsuario" name="editarUsuario" value="" readonly>

      </div>

     </div>

     <!-- CAMPO DE TEXTO PARA CONTRASEÑA-->

     <div class="form-group">

      <label for="editarPassword">Contraseña <span style="color:red">*</span></label>

      <div class="input-group">

       <span class="input-group-addon"><i class="fa fa-lock"></i></span>

       <input type="password" class="form-control input-lg" name="editarPassword" placeholder="Ingresar nueva contraseña" requerid>
       <input type="hidden" id="passwordActual" name="passwordActual" >
       <input type="hidden" id="editarEstado" name="editarEstado" value="1">

      </div>

     </div>

     <!-- CAMPO PARA SELECCIONAR PERFIL-->

      <div class="form-group">

      <label for="editarPerfil">Perfil <span style="color:red">*</span></label>

      <div class="input-group">



       <span class="input-group-addon"><i class="fa fa-users"></i></span>



       <select class="form-control input-lg" name="editarPerfil">

        <option value="" id="editarPerfil"></option>

        <option value="Admnistrador">Administrador</option>

        <option value="Especial">Especial</option>

        <option value="Vendedor">Vendedor</option>

       </select>



      </div>

      </div>

     <!-- CAMPO PARA SUBIR FOTO DE USUARIO-->

     <div class="form-group">

      <div class="panel">Subir foto</div>

      <input type="file" class="nuevaFoto" name="editarFoto">

      <p class="help-block">Peso maximo de la foto 2MB</p>

      <img src="vistas/img/usuarios/default/perfil.png" class="img-thumbnail previsualizarEditar previsualizar" width="100px">

      <input type="hidden" name="fotoActual" id="fotoActual">

     </div>    

    </div>

   </div>

   <!-- FOOTER O PIE DEL MODAL-->

   <div class="modal-footer">

    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

    <button type="submit" class="btn btn-primary">Actualizar usuario</button>

   </div>



   <!--==========================

   LLAMAR AL MÉTODO PARA EDITAR/BORRAR USUARIO
   (Movido al inicio del archivo para correcta ejecución)

   ==============================-->



   </form>
  </div>
 </div>
</div>
