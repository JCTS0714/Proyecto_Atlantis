<aside class="main-sidebar" >
    <section class="sidebar">
        <ul class="sidebar-menu">
            <!-- Inicio -->
            <li class="active">
                <a href="<?php echo BASE_URL; ?>/index.php?ruta=inicio">
                    <i class="fa fa-home "></i>
                    <span>Inicio</span>
                </a>
            </li>

            <!-- Usuarios -->
            <?php if($_SESSION["perfil"] !== "Vendedor"): ?>
            <li class="active">
                <a href="<?php echo BASE_URL; ?>/index.php?ruta=usuarios">
                    <i class="fa fa-user "></i>
                    <span>Usuarios</span>
                </a>
            </li>
            <?php else: ?>
            <li class="active disabled" style="pointer-events:none; opacity:0.5;">
                <a href="#">
                    <i class="fa fa-user "></i>
                    <span>Usuarios</span>
                </a>
            </li>
            <?php endif; ?>

            <li class="active">
                <a href="<?php echo BASE_URL; ?>/index.php?ruta=calendario">
                    <i class="fa fa-calendar "></i>
                    <span>calendario</span>
                </a>
            </li>
            <!-- Contactos -->
            <li class="treeview active">
                <a href="#">
                    <i class="fa fa-address-book"></i>
                    <span>Contactos</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li>
                        <a href="<?php echo BASE_URL; ?>/index.php?ruta=prospectos">
                            <i class="fa fa-circle-o"></i>
                            <span>Prospectos</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/index.php?ruta=seguimiento">
                            <i class="fa fa-circle-o"></i>
                            <span>Seguimientos</span>
                        </a>
                    </li>
                    <li >
                        <a href="<?php echo BASE_URL; ?>/index.php?ruta=clientes">
                            <i class="fa fa-circle-o"></i>
                            <span>Clientes</span>
                        </a>
                    </li>
                    <li >
                        <a href="<?php echo BASE_URL; ?>/index.php?ruta=no-clientes">
                            <i class="fa fa-circle-o"></i>
                            <span>No Clientes</span>
                        </a>
                    </li>
                    <li >
                        <a href="<?php echo BASE_URL; ?>/index.php?ruta=zona-espera">
                            <i class="fa fa-circle-o"></i>
                            <span>Zona de Espera</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- CRM -->
            <li class="active">
                <a href="<?php echo BASE_URL; ?>/index.php?ruta=crm">
                    <i class="fa fa-handshake-o "></i>
                    <span>CRM</span>
                </a>
            </li>

            <!-- Postventa -->
            <li class="treeview active">
                <a href="#">
                    <i class="fa fa-wrench"></i>
                    <span>Postventa</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li>
                        <a href="<?php echo BASE_URL; ?>/index.php?ruta=incidencias">
                            <i class="fa fa-circle-o"></i>
                            <span>Incidencias</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/index.php?ruta=backlog">
                            <i class="fa fa-circle-o"></i>
                            <span>Backlog</span>
                        </a>
                    </li>
                     <li >
                        <a href="<?php echo BASE_URL; ?>/index.php?ruta=clientes">
                            <i class="fa fa-circle-o"></i>
                            <span>Clientes</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Ventas -->
            <!-- <li class="treeview">
                <a href="#">
                    <i class="fa fa-list-ul"></i>
                    <span>Ventas</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a> -->
<!--                 <ul class="treeview-menu">
                    <li>
                        <a href="<?php echo BASE_URL; ?>/index.php?ruta=ventas">
                            <i class="fa fa-circle-o"></i>
                            <span>Administrar ventas</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/index.php?ruta=crear-venta">
                            <i class="fa fa-circle-o"></i>
                            <span>Crear venta</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/index.php?ruta=reportes">
                            <i class="fa fa-circle-o"></i>
                            <span>Reporte de ventas</span>
                        </a>
                    </li>
                </ul>
            </li> -->

            <!-- Código comentado para futuras referencias -->

            <!-- Categorías, Productos y Proveedor eliminados del árbol -->
        </ul>
    </section>
</aside>