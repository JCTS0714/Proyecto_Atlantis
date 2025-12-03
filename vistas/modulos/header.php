<header class="main-header">
<!-- Logo -->
     <a href="#" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><img src="<?php echo BASE_URL; ?>/vistas/img/plantilla/logo_alta_calidad.png" alt="ATL" style="width: 35px; height: 35px;"></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><img src="<?php echo BASE_URL; ?>/vistas/img/plantilla/grupo-atlantis-20231202171034.png" class="img-responsive" style="width: 150px; height: 50px; margin: 0 auto;" alt="Logo"></span>
    </a>

    <!--==========
    BARRA DE NAVEGACIÓN 
    ===============-->
    <nav class="navbar navbar-static-top" role="navegation">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class= "sr-only">Barra de navegación</span>
        </a>
  

    <!--==========
    PERFÍL DE USUARIO
    ===============-->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- Notification Bell -->
                <li class="dropdown notifications-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="dropdown-notificaciones-toggle" aria-expanded="false" aria-haspopup="true" role="button">
                        <i class="fa fa-bell" aria-hidden="true"></i>
                        <span class="label label-warning" id="contador-notificaciones">0</span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdown-notificaciones-toggle" role="menu">
                        <li class="header" id="header-notificaciones">No tienes notificaciones</li>
                        <li>
                            <ul class="menu" id="lista-notificaciones" role="list">
                                <!-- Notificaciones dinámicas aquí -->
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">

                        <?php

                        if($_SESSION["foto"] != "")
                        {

                            echo '<img src="'.BASE_URL.'/'.$_SESSION["foto"].'" class="user-image">';
                        }
                        else
                        {
                            echo '<img src="'.BASE_URL.'/vistas/img/usuarios/default/perfil.png" class="user-image">';

                        }
                        ?>
                    <span class="hidden-xs"> <?php echo $_SESSION["nombre"]; ?> </span>
                    </a>
                    <input type="hidden" id="usuario_id" value="<?php echo $_SESSION['id']; ?>" />

                    <!--DROPWN-->
                    <ul class="dropdown-menu">
                        <li class="user-body">
                            <div class="pull-right">
                                <a href="<?php echo BASE_URL; ?>/salir" class="btn btn-default btn-flat">Salir</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>
