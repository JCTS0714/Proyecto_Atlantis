  <?php
    /**
     * PLANTILLA PRINCIPAL - ATLANTIS CRM
     * 
     * Sistema de enrutamiento y gestión de plantilla
     * Incluye validación de sesión y sesión_token único
     * 
     * @version 2.0
     * @date 2025-11-12
     */

    // Validación de sesión existente
    // Fallback seguro: si BASE_URL no está definida en producción, definirla vacía
    if (!defined('BASE_URL')) {
      define('BASE_URL', '');
    }
    if (isset($_SESSION["iniciarSesion"]) && $_SESSION["iniciarSesion"] == "ok") {
      $tabla = "usuarios";
      $item = "id";
      $valor = $_SESSION["id"];
      
      // Obtener datos de usuario de BD
      $usuario = ModeloUsuarios::MdlMostrarUsuarios($tabla, $item, $valor);

      // Validar token de sesión único (protección contra acceso múltiple)
      if (!$usuario || $usuario["sesion_token"] !== $_SESSION["sesion_token"]) {
        session_destroy();
          echo '<script>
            window.location.href = "'.BASE_URL.'/login";
          </script>';
        exit;
      }

      // Si hay sesión pero no hay ruta específica, redirigir a inicio
      if (!isset($_GET["ruta"])) {
        echo '<script>
            window.location.href = "'.BASE_URL.'/inicio";
        </script>';
        exit;
      }
    }
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
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/vistas/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/vistas/bower_components/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/vistas/bower_components/Ionicons/css/ionicons.min.css">

    <!-- fullCalendar -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/vistas/bower_components/fullcalendar/dist/fullcalendar.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/vistas/bower_components/fullcalendar/dist/fullcalendar.print.min.css" media="print">
    <!-- jQuery UI -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/vistas/bower_components/jquery-ui/jquery-ui.min.css">
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/vistas/bower_components/select2/dist/css/select2.min.css">
    
    <!-- Column Toggle CSS - Sistema mostrar/ocultar columnas -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/column-toggle.css">
    
    <!-- Estilos personalizados para Kanban -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/estilos_kanban.css">
    
    <!-- Responsive Tables CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/responsive-tables.css">

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/vistas/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/vistas/bower_components/datatables.net-bs/css/responsive.bootstrap.min.css">

    <!--=================================
    CAMBIAMOS LA HOJA DE ESTILO DE AdminLTE a solo.cc
  =====================================-->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/vistas/dist/css/AdminLTE.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
        folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/vistas/dist/css/skins/_all-skins.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <!-- Custom background style -->
    <style>
    body {
      background-image: url('../fonds.jpg');
      background-size: cover;
      background-repeat: no-repeat;
      background-attachment: fixed;
    }
    .content-wrapper {
      background: rgba(255, 255, 255, 0.9);
    }
    </style>

    <?php
    if(isset($_SESSION["perfil"]) && $_SESSION["perfil"] == "Vendedor") {
      echo '<style>[class*="btnEliminar"] { display: none !important; }</style>';
    }
    ?>

    <!--=================================
    ==========PLUGINS DE JAVASCRIP
    =====================================-->
  <!-- jQuery 3 -->
  <script src="<?php echo BASE_URL; ?>/vistas/bower_components/jquery/dist/jquery.min.js"></script>

  <!-- jQuery UI -->
  <script src="<?php echo BASE_URL; ?>/vistas/bower_components/jquery-ui/jquery-ui.min.js"></script>
  <!-- fullCalendar -->
  <script src="<?php echo BASE_URL; ?>/vistas/bower_components/moment/moment.js"></script>
  <script src="<?php echo BASE_URL; ?>/vistas/bower_components/fullcalendar/dist/fullcalendar.min.js"></script>
  <!-- Select2 JS -->
  <script src="<?php echo BASE_URL; ?>/vistas/bower_components/select2/dist/js/select2.min.js"></script>
  <!-- Calendario JS -->
  <!-- moved to end of body to avoid duplicate execution -->
    <!-- Chart.js para gráficos del dashboard -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <!-- Bootstrap 3.3.7 -->
  <script src="<?php echo BASE_URL; ?>/vistas/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
  <!-- SlimScroll -->
  <script src="<?php echo BASE_URL; ?>/vistas/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
  <!-- FastClick -->
  <script src="<?php echo BASE_URL; ?>/vistas/bower_components/fastclick/lib/fastclick.js"></script>
  <!-- AdminLTE App -->
  <script src="<?php echo BASE_URL; ?>/vistas/dist/js/adminlte.min.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="<?php echo BASE_URL; ?>/vistas/dist/js/demo.js"></script>

  <script src="<?php echo BASE_URL; ?>/vistas/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
  <script src="<?php echo BASE_URL; ?>/vistas/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
  <script src="<?php echo BASE_URL; ?>/vistas/bower_components/datatables.net-bs/js/dataTables.responsive.min.js"></script>
  <script src="<?php echo BASE_URL; ?>/vistas/bower_components/datatables.net-bs/js/responsive.bootstrap.min.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <!-- Sistema de Mostrar/Ocultar Columnas v2 -->
  <script src="<?php echo BASE_URL; ?>/vistas/js/column-toggle-v2.js"></script>
  
  <!-- Responsive Tables Script -->
  <script src="<?php echo BASE_URL; ?>/vistas/js/responsive-tables.js"></script>

  </head>
  <body class="hold-transition skin-blue sidebar-collapse sidebar-mini login-page">
  <!-- Site wrapper -->

  <script>
    window.BASE_URL = '<?php echo BASE_URL; ?>';
  </script>


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
        // Incluir buscador avanzado (parcial global)
        include "modulos/advanced_search.php";
  
        /**==================
         *  CONTENIDO PRINCIPAL
         ==================== */
        if(isset($_GET["ruta"])){
          if($_GET["ruta"]=="inicio"       ||
            $_GET["ruta"]=="usuarios"     ||
            $_GET["ruta"]=="clientes"     ||
            $_GET["ruta"]=="reuniones-archivadas" ||
            $_GET["ruta"]=="ventas"       ||
            $_GET["ruta"]=="crear-venta"  ||
            $_GET["ruta"]=="reportes"     ||
            $_GET["ruta"]=="prospectos"   ||
            $_GET["ruta"]=="calendario"   ||
            $_GET["ruta"]=="crm"          ||
            $_GET["ruta"]=="seguimiento"  ||
            $_GET["ruta"]=="no-clientes"   ||
            $_GET["ruta"]=="zona-espera"   ||
            $_GET["ruta"]=="incidencias"   ||
            $_GET["ruta"]=="backlog"       ||
            $_GET["ruta"]=="salir" ||
            $_GET["ruta"]=="contadores"
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

  <script src="<?php echo BASE_URL; ?>/vistas/js/plantilla.js"></script>
  <script src="<?php echo BASE_URL; ?>/vistas/js/usuarios.js"></script>
  <script src="<?php echo BASE_URL; ?>/vistas/js/clientes.js"></script>
  <script src="<?php echo BASE_URL; ?>/vistas/js/incidencias.js"></script>
  <script src="<?php echo BASE_URL; ?>/vistas/js/ventas.js"></script>
  <script src="<?php echo BASE_URL; ?>/vistas/js/oportunidades.js"></script>
  <script src="<?php echo BASE_URL; ?>/vistas/js/prospectos.js"></script>
  <script src="<?php echo BASE_URL; ?>/vistas/js/calendario.js"></script>
  <script src="<?php echo BASE_URL; ?>/vistas/js/evento.js"></script>
  <script src="<?php echo BASE_URL; ?>/vistas/js/dashboard.js"></script>
  <script src="<?php echo BASE_URL; ?>/vistas/js/notificaciones.module.js"></script>
  <script src="<?php echo BASE_URL; ?>/vistas/js/notificaciones.js"></script>
  <script src="<?php echo BASE_URL; ?>/vistas/js/alarma.js"></script>
  <script src="<?php echo BASE_URL; ?>/vistas/js/modal-detalles.js"></script>
  <script src="<?php echo BASE_URL; ?>/vistas/js/advanced_search.js"></script>

  </body>
  </html>
