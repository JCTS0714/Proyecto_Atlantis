<aside class="main-sidebar" >
    <section class="sidebar">
        <ul class="sidebar-menu">
            <!-- Inicio -->
            <li class="active">
                <a href="inicio">
                    <i class="fa fa-home "></i>
                    <span>Inicio</span>
                </a>
            </li>

            <!-- Usuarios -->
            <?php if($_SESSION["perfil"] !== "Vendedor"): ?>
            <li class="active">
                <a href="usuarios">
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
                <a href="calendario">
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
                        <a href="prospectos">
                            <i class="fa fa-circle-o"></i>
                            <span>Prospectos</span>
                        </a>
                    </li>
                    <li>
                        <a href="seguimiento">
                            <i class="fa fa-circle-o"></i>
                            <span>Seguimientos</span>
                        </a>
                    </li>
                    <li >
                        <a href="clientes">
                            <i class="fa fa-circle-o"></i>
                            <span>Clientes</span>
                        </a>
                    </li>
                    <li >
                        <a href="no-clientes">
                            <i class="fa fa-circle-o"></i>
                            <span>No Clientes</span>
                        </a>
                    </li>
                    <li >
                        <a href="zona-espera">
                            <i class="fa fa-circle-o"></i>
                            <span>Zona de Espera</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- CRM -->
            <li class="active">
                <a href="crm">
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
                        <a href="incidencias">
                            <i class="fa fa-circle-o"></i>
                            <span>Incidencias</span>
                        </a>
                    </li>
                    <li>
                        <a href="backlog">
                            <i class="fa fa-circle-o"></i>
                            <span>Backlog</span>
                        </a>
                    </li>
                     <li >
                        <a href="clientes">
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
                        <a href="ventas">
                            <i class="fa fa-circle-o"></i>
                            <span>Administrar ventas</span>
                        </a>
                    </li>
                    <li>
                        <a href="crear-venta">
                            <i class="fa fa-circle-o"></i>
                            <span>Crear venta</span>
                        </a>
                    </li>
                    <li>
                        <a href="reportes">
                            <i class="fa fa-circle-o"></i>
                            <span>Reporte de ventas</span>
                        </a>
                    </li>
                </ul>
            </li> -->

            <!-- Código comentado para futuras referencias -->

            <!-- Categorías y Productos (comentados) -->
            <!--
            <li class="active">
                <a href="categorias">
                    <i class="fa fa-th"></i>
                    <span>Categorías</span>
                </a>
            </li>
            <li class="active">
                <a href="productos">
                    <i class="fa fa-product-hunt"></i>
                    <span>Productos</span>
                </a>
            </li>
            -->

            <!-- Proveedor (comentado) -->
            <!--
            <li class="active">
                <a href="proveedor">
                    <i class="fa fa-child"></i>
                    <span>Proovedor</span>
                </a>
            </li>
            -->
        </ul>
    </section>
</aside>