<?php
// Configurar parámetros de cookie ANTES de iniciar sesión
session_set_cookie_params(30 * 24 * 60 * 60); // 30 días en segundos

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**REQUERIMOS CONFIGURACIÓN */
require_once "config/estados.php";
// Rutas base (detecta entorno y arma BASE_URL)
require_once "config/paths.php";

/**REQUERIMOS DE CONTROLADOR */
require_once "controladores/plantilla.controlador.php";
require_once "controladores/clientes.controlador.php";
require_once "controladores/usuarios.controlador.php";
require_once "controladores/ventas.controlador.php";
require_once "controladores/ControladorOportunidad.php";
require_once "controladores/prospectos.controlador.php";
require_once "controladores/evento.controlador.php";
require_once "controladores/calendario.controlador.php";


/**REQUERIMOS DE MODELOS */
require_once "modelos/clientes.modelo.php";
require_once "modelos/usuarios.modelo.php";
require_once "modelos/ventas.modelo.php";
require_once "modelos/ModeloCRM.php";
require_once "modelos/evento.modelo.php";
require_once "modelos/calendario.modelo.php";



/**CREAMOS EL OBJETO $PLANTILLA QUE HACE INSTANCIA DE LA CLASE ControladoPlantilla Y ACCEDEMOS A SU MÉTODO */

$plantilla = new ControladorPlantilla();
$plantilla->ctrPlantilla();