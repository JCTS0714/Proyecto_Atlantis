<?php
/**REQUERIMOS DE CONTROLADOR */
require_once "controladores/plantilla.controlador.php";
require_once "controladores/categorias.controlador.php";
require_once "controladores/clientes.controlador.php";
require_once "controladores/productos.controlador.php";
require_once "controladores/usuarios.controlador.php";
require_once "controladores/ventas.controlador.php";
require_once "controladores/proveedor.controlador.php";
require_once "controladores/ControladorOportunidad.php";
require_once "controladores/prospectos.controlador.php";
<<<<<<< HEAD
require_once "controladores/evento.controlador.php";
require_once "controladores/calendario.controlador.php";
=======
>>>>>>> 27fc4213f1497e196cdabdb3c71cbf402171bd57

/**REQUERIMOS DE MODELOS */
require_once "modelos/categorias.modelo.php";
require_once "modelos/clientes.modelo.php";
require_once "modelos/productos.modelo.php";
require_once "modelos/usuarios.modelo.php";
require_once "modelos/ventas.modelo.php";
require_once "modelos/proveedor.modelo.php";
require_once "modelos/ModeloCRM.php";
require_once "modelos/evento.modelo.php";
require_once "modelos/calendario.modelo.php";



/**CREAMOS EL OBJETO $PLANTILLA QUE HACE INSTANCIA DE LA CLASE ControladoPlantilla Y ACCEDEMOS A SU MÉTODO */
$plantilla = new ControladorPlantilla();
$plantilla->ctrPlantilla();