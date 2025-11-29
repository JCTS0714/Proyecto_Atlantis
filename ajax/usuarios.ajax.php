<?php
require_once "../controladores/usuarios.controlador.php";
require_once "../modelos/usuarios.modelo.php";


class AjaxUsuarios{
    /**==============
     * EDITAR USUARIO 
     * ==============*/
    public $idUsuario;

    public function ajaxEditarUsuario(){
        $item = "id";
        $valor = $this->idUsuario;

        $respuesta = ControladorUsuarios::ctrMostrarUsuarios($item,$valor);

        echo json_encode($respuesta);
    }
     /**==============
     * ACTIVAR USUARIO
     * ==============*/
    public $activarUsuario;
    public $activarId;
    public function ajaxActivarUsuario(){
        $tabla = "usuarios";

        $campo = "estado";
        $valor = $this->activarUsuario;

        $item = "id";
        $id = $this->activarId;

        $respuesta = ModeloUsuarios::mdlActualizarCampoUsuario($tabla, $campo, $valor, $item, $id);

        echo $respuesta;
    }
    
}

//EDITAR USUARIO = CREAR UN OBJETO PARA VERIFICAR LOS QUE NOS VENGA DEL ID USUARIO
if(isset($_POST["idUsuario"])){
    $editar = new AjaxUsuarios();
    $editar -> idUsuario = $_POST["idUsuario"];
    $editar -> ajaxEditarUsuario();
}


//objeto para activa usuario
/**==============
     * ACTIVAR USUARIO OBJETO PARA ACTIVAR
     * ==============*/
if(isset($_POST["activarUsuario"])){
        $activarUsuario = new AjaxUsuarios();
        $activarUsuario -> activarUsuario = $_POST["activarUsuario"];
        $activarUsuario -> activarId = $_POST["activarId"];
        $activarUsuario -> ajaxActivarUsuario();
}

// ELIMINAR USUARIO POR AJAX
if (isset($_POST["eliminarUsuario"])) {
    $idUsuario = $_POST["eliminarUsuario"];
    $usuario = $_POST["usuario"];
    $fotoUsuario = $_POST["fotoUsuario"];

    // Solo intenta borrar la foto y el directorio si la ruta existe y no está vacía
    if (!empty($fotoUsuario)) {
        if (file_exists($fotoUsuario)) {
            unlink($fotoUsuario);
        }
        $directorio = '../vistas/img/usuarios/' . $usuario;
        if (is_dir($directorio)) {
            rmdir($directorio);
        }
    }

    $tabla = "usuarios";
    $respuesta = ModeloUsuarios::mdlBorrarUsuario($tabla, $idUsuario);

    if ($respuesta == "ok") {
        echo "ok";
    } else {
        echo "error";
    }
}

