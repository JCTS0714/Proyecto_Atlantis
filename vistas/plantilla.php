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
      // Primero verificar que $usuario existe y es un array válido
      if (!$usuario || !is_array($usuario) || !isset($usuario["sesion_token"])) {
        session_destroy();
        echo '<script>window.location.href = "'.BASE_URL.'/login";</script>';
        exit;
      }
      
      // Luego validar que el token coincida
      if ($usuario["sesion_token"] !== $_SESSION["sesion_token"]) {
        session_destroy();
        echo '<script>window.location.href = "'.BASE_URL.'/login";</script>';
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
    <!-- Bootstrap 3.3.7 (use local bower copy) -->
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
  <!-- jQuery (local bower copy) -->
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
  <!-- AdminLTE (local) -->
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
  <?php
    $bodyClass = (isset($_SESSION["iniciarSesion"]) && $_SESSION["iniciarSesion"] == "ok")
      ? 'hold-transition skin-blue sidebar-collapse sidebar-mini'
      : 'hold-transition skin-blue sidebar-collapse sidebar-mini login-page';
  ?>
  <body class="<?php echo $bodyClass; ?>">
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
        // Búsqueda avanzada: ahora se incluye directamente en los módulos con tablas
  
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

  <script>
    // Diagnostic helper: detect missing DataTables controls (pagination / filter)
    (function(){
      function showDiag(msg, level){
        try {
          var el = document.getElementById('diagnostic-banner');
          if (!el) {
            el = document.createElement('div');
            el.id = 'diagnostic-banner';
            el.style.position = 'fixed';
            el.style.top = '10px';
            el.style.right = '10px';
            el.style.zIndex = 100000;
            el.style.background = 'rgba(255,255,200,0.98)';
            el.style.border = '1px solid #999';
            el.style.padding = '8px 12px';
            el.style.fontSize = '13px';
            el.style.boxShadow = '0 2px 6px rgba(0,0,0,0.15)';
            document.body.appendChild(el);
          }
          el.innerHTML = '<strong>Diagnóstico:</strong> ' + msg;
          if (level === 'error') el.style.borderColor = '#cc0000';
          if (level === 'warn') el.style.borderColor = '#cc9900';
        } catch(e){ console.warn('Could not render diagnostic banner', e); }
      }

      function runCheck(){
        try {
          if (!window.jQuery) { showDiag('jQuery no está cargado — DataTables no funcionará', 'error'); console.error('DIAG: jQuery missing'); return; }
          var $ = window.jQuery;
          if (!$.fn.DataTable) { showDiag('DataTables JS no está cargado (plugin faltante)', 'error'); console.error('DIAG: DataTables missing'); return; }

          var $firstTable = $('.tabla').first();
          if ($firstTable.length === 0) { showDiag('No se encontró ninguna tabla con la clase .tabla', 'warn'); console.warn('DIAG: no .tabla found'); return; }

          // If DataTable instance exists, inspect the wrapper for controls
          var dtInstance = null;
          try { if ($.fn.DataTable.isDataTable($firstTable)) dtInstance = $firstTable.DataTable(); } catch(e){}

          var $wrapper = $firstTable.closest('.dataTables_wrapper');
          var hasLength = $wrapper.find('.dataTables_length').length > 0;
          var hasFilter = $wrapper.find('.dataTables_filter').length > 0;
          var hasPaginate = $wrapper.find('.dataTables_paginate').length > 0;

          console.info('DIAG: table found:', { tableId: $firstTable.attr('id'), hasWrapper: $wrapper.length>0, hasLength: hasLength, hasFilter: hasFilter, hasPaginate: hasPaginate, dtInstance: !!dtInstance });

          if (!$wrapper.length) {
            showDiag('DataTables wrapper no encontrado — puede que DataTables no haya inicializado la tabla', 'warn');
          } else if (!hasPaginate || !hasFilter) {
            showDiag('Controles de DataTables faltantes (paginación/filtro). Mira consola para más info.', 'warn');
          } else {
            // All good — remove diag if present
            var el = document.getElementById('diagnostic-banner'); if (el) el.parentNode.removeChild(el);
            console.info('DIAG: DataTables controls present');
          }
        } catch(e){ console.error('DIAG: check failed', e); }
      }

      if (document.readyState === 'complete' || document.readyState === 'interactive') {
        setTimeout(runCheck, 500); // slight delay to allow DataTable init
      } else {
        document.addEventListener('DOMContentLoaded', function(){ setTimeout(runCheck, 500); });
      }
    })();
  </script>

  <script>
    // Global JS error overlay to help surface runtime errors that stop event handlers
    (function(){
      function renderOverlay(msg){
        try {
          var existing = document.getElementById('js-error-overlay');
          if (existing) existing.parentNode.removeChild(existing);
          var div = document.createElement('div');
          div.id = 'js-error-overlay';
          div.style.position = 'fixed';
          div.style.left = '10px';
          div.style.right = '10px';
          div.style.bottom = '10px';
          div.style.zIndex = 99999;
          div.style.background = 'rgba(255,230,230,0.98)';
          div.style.border = '1px solid #cc0000';
          div.style.padding = '12px';
          div.style.fontSize = '13px';
          div.style.boxShadow = '0 2px 6px rgba(0,0,0,0.2)';
          div.innerHTML = '<strong>JS Error:</strong> ' + msg + ' <button id="js-error-overlay-close" style="margin-left:12px;">Cerrar</button>';
          document.body.appendChild(div);
          document.getElementById('js-error-overlay-close').addEventListener('click', function(){ div.parentNode.removeChild(div); });
        } catch(e){ console.error('Could not render overlay', e); }
      }

      window.addEventListener('error', function(ev){
        try {
          var msg = (ev && ev.message) ? ev.message : String(ev || 'Unknown error');
          var src = ev && ev.filename ? (' at ' + ev.filename + ':' + ev.lineno + ':' + ev.colno) : '';
          renderOverlay(msg + src);
          console.error('Global JS error caught:', ev.error || ev, ev);
        } catch(e){ console.error('error overlay handler failure', e); }
      });

      window.addEventListener('unhandledrejection', function(ev){
        try { renderOverlay('Unhandled Promise Rejection: ' + (ev.reason && ev.reason.message ? ev.reason.message : String(ev.reason))); console.error('UnhandledRejection', ev); } catch(e){}
      });
    })();
  </script>
  </body>
  </html>
