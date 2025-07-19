<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="es-PE">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title> GRUPO | ATLANTIS</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<!--=================================
  PLUGINS DE CSS
=====================================-->
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="vistas/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="vistas/bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="vistas/bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->

  <link rel="stylesheet" href="vistas/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">

  <link rel="stylesheet" href="vistas/bower_components/datatables.net-bs/css/responsive.bootstrap.min.css">

  

   <!--=================================
  CAMBIAMOS LA HOJA DE ESTILO DE AdminLTE a solo.cc
=====================================-->
  <link rel="stylesheet" href="vistas/dist/css/AdminLTE.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="vistas/dist/css/skins/_all-skins.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

  <link rel="stylesheet" href="vistas/bower_components/datatables.net-bs/css/datatables.bootstrap.min.css">

  <!--=================================
  ==========PLUGINS DE JAVASCRIP
  =====================================-->
<!-- jQuery 3 -->
<script src="vistas/bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="vistas/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="vistas/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="vistas/bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="vistas/dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="vistas/dist/js/demo.js"></script>

<script src="vistas/bower_components/datatables.net-bs/js/dataTables.responsive.min.js"></script>
<script src="vistas/bower_components/datatables.net-bs/js/responsive.bootstrap.min.js"></script>

<script src="vistas/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="vistas/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>





<script src="vistas/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>

<script src="vistas/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body class="hold-transition skin-blue sidebar-collapse sidebar-mini login-page">
<!-- Site wrapper -->


     <?php

     if(isset($_SESSION["iniciarSesion"])&& $_SESSION["iniciarSesion"] == "ok")
     {
      echo '<div class="wrapper">';

      /**==================
       *  HEADER(CABEZERA),ESTÁTICO
       ==================== */
      include "modulos/header.php";
 
     /**==================
       *  MENU LATERAL(ESTÁTICO)
       ==================== */
       include "modulos/menu.php";
 
       /**==================
       *  CONTENIDO PRINCIPAL
       ==================== */
       if(isset($_GET["ruta"])){
         if($_GET["ruta"]=="inicio"      || 
            $_GET["ruta"]=="usuarios"    || 
            $_GET["ruta"]=="categorias"  ||
            $_GET["ruta"]=="productos"   ||
            $_GET["ruta"]=="clientes"    ||
            $_GET["ruta"]=="ventas"      ||
            $_GET["ruta"]=="crear-venta" ||
            $_GET["ruta"]=="reportes"    ||
            $_GET["ruta"]=="proveedor"   ||
            $_GET["ruta"]=="crm" ||
            $_GET["ruta"]=="salir"    
            ){
           include "modulos/".$_GET["ruta"].".php";
         }
         else
         {
           include "modulos/404.php";
         }
       }
       else
       {
         include "modulos/inicio.php";
        }
       /**==================
       *  FOOTER O PIE DE LA PAGINA
       ==================== */
       include "modulos/footer.php";
 
       echo '</div>';
     }
     else
     {
      include "modulos/login.php";
     }

     ?>
<!-- ./wrapper -->

 <script src="vistas/js/plantilla.js"></script>
  <script src="vistas/js/usuarios.js"></script>
  <script src="vistas/js/categorias.js"></script>
  <script src="vistas/js/productos.js"></script>
  <script src="vistas/js/clientes.js"></script>
  <script src="vistas/js/proveedor.js"></script>
  <script src="vistas/js/ventas.js"></script>
  <script src="vistas/js/crm.js"></script>


</body>
</html>
