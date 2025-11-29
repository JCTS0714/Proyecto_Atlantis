<?php

class ControladorContador {

  static public function ctrMostrarContadores($item, $valor) {
    $tabla = "contador";
    $respuesta = ModeloContador::mdlMostrarContador($tabla, $item, $valor);
    return $respuesta;
  }

  static public function ctrCrearContador() {
    // Detectar envío por uno de los campos obligatorios (comercio)
    if (isset($_POST['nuevoComercio'])) {
      $datos = [
        // nro será calculado automáticamente en el modelo
        'nro' => '',
        'comercio' => $_POST['nuevoComercio'] ?? '',
        'nom_contador' => $_POST['nuevoNomContador'] ?? '',
        'titular_tlf' => $_POST['nuevoTitularTlf'] ?? '',
        'telefono' => $_POST['nuevoTelefono'] ?? '',
        'telefono_actu' => $_POST['nuevoTelefonoActu'] ?? '',
        'link' => $_POST['nuevoLink'] ?? '',
        'usuario' => $_POST['nuevoUsuario'] ?? '',
        'contrasena' => $_POST['nuevoContrasena'] ?? ''
      ];

      $tabla = 'contador';
      $respuesta = ModeloContador::mdlRegistrarContador($tabla, $datos);

      if ($respuesta == 'ok') {
        echo '<script>Swal.fire({icon:"success", title:"Contador creado", showConfirmButton:true}).then(()=>{window.location="contadores";});</script>';
      } else {
        echo '<script>Swal.fire({icon:"error", title:"Error al crear", showConfirmButton:true});</script>';
      }
    }
  }

  static public function ctrEditarContador() {
    if (isset($_POST['idContador'])) {
      $datos = [
        'id' => $_POST['idContador'],
        'comercio' => $_POST['editarComercio'] ?? '',
        'nom_contador' => $_POST['editarNomContador'] ?? '',
        'titular_tlf' => $_POST['editarTitularTlf'] ?? '',
        'telefono' => $_POST['editarTelefono'] ?? '',
        'telefono_actu' => $_POST['editarTelefonoActu'] ?? '',
        'link' => $_POST['editarLink'] ?? '',
        'usuario' => $_POST['editarUsuario'] ?? '',
        'contrasena' => $_POST['editarContrasena'] ?? ''
      ];

      $tabla = 'contador';
      $respuesta = ModeloContador::mdlEditarContador($tabla, $datos);
      if ($respuesta == 'ok') {
        echo '<script>Swal.fire({icon:"success", title:"Contador actualizado", showConfirmButton:true}).then(()=>{window.location="contadores";});</script>';
      } else {
        echo '<script>Swal.fire({icon:"error", title:"Error al actualizar", showConfirmButton:true});</script>';
      }
    }
  }

  static public function ctrEliminarContador() {
    if (isset($_GET['idContadorEliminar'])) {
      $id = intval($_GET['idContadorEliminar']);
      $tabla = 'contador';
      $respuesta = ModeloContador::mdlEliminarContador($tabla, $id);
      if ($respuesta == 'ok') {
        echo '<script>Swal.fire({icon:"success", title:"Contador eliminado", showConfirmButton:true}).then(()=>{window.location="contadores";});</script>';
      } else {
        echo '<script>Swal.fire({icon:"error", title:"Error al eliminar", showConfirmButton:true});</script>';
      }
    }
  }

}
