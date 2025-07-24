<?php
class ControladorCliente {

  /* 🟢 Crear cliente */
  static public function ctrCrearCliente() {
    if (isset($_POST["nuevoNombre"])) {
      $tabla = "clientes";
      $datos = array(
        "nombre" => $_POST["nuevoNombre"],
        "tipo" => $_POST["nuevoTipo"],
        "documento" => $_POST["nuevoDocumento"],
        "telefono" => $_POST["nuevoTelefono"],
        "correo" => $_POST["nuevoCorreo"],
        "direccion" => $_POST["nuevoDireccion"],
        "fecha_creacion" => $_POST["nuevaFechaCreacion"]
      );

      $respuesta = ModeloCliente::mdlRegistrarCliente($tabla, $datos);

      if ($respuesta == "ok") {
        echo '
          <script>
            Swal.fire({
              icon: "success",
              title: "Cliente registrado",
              text: "El cliente ha sido guardado exitosamente",
              confirmButtonText: "Cerrar"
            }).then(function(result) {
              if (result.isConfirmed) {
                window.location = "clientes";
              }
            });
          </script>';
      } else {
        echo '
          <script>
            Swal.fire({
              icon: "error",
              title: "Error al registrar",
              text: "No se pudo guardar el cliente. Intente nuevamente.",
              confirmButtonText: "Cerrar"
            });
          </script>';
      }
    }
  }

  /* 📄 Mostrar cliente por campo */
  static public function ctrMostrarCliente($item, $valor) {
    return ModeloCliente::mdlMostrarCliente("clientes", $item, $valor);
  }

  /* 📄 Mostrar todos los clientes */
  static public function ctrMostrarClientes() {
    return ModeloCliente::mdlMostrarClientes();
  }

  /* 📝 Editar cliente */
  static public function ctrEditarCliente() {
    if (isset($_POST["editarNombre"])) {
      $tabla = "clientes";
      $datos = array(
        "id" => $_POST["idCliente"],
        "nombre" => $_POST["editarNombre"],
        "tipo" => $_POST["editarTipo"],
        "documento" => $_POST["editarDocumento"],
        "telefono" => $_POST["editarTelefono"],
        "correo" => $_POST["editarCorreo"],
        "direccion" => $_POST["editarDireccion"],
        "fecha_creacion" => $_POST["editarFechaCreacion"]
      );

      $respuesta = ModeloCliente::mdlEditarCliente($tabla, $datos);

      if ($respuesta == "ok") {
        echo '
          <script>
            Swal.fire({
              icon: "success",
              title: "Cliente actualizado",
              text: "Los datos del cliente han sido actualizados correctamente",
              confirmButtonText: "Cerrar"
            }).then(function(result) {
              if (result.isConfirmed) {
                window.location = "clientes";
              }
            });
          </script>';
      } else {
        echo '
          <script>
            Swal.fire({
              icon: "error",
              title: "Error al actualizar",
              text: "No se pudo actualizar el cliente. Intente nuevamente.",
              confirmButtonText: "Cerrar"
            });
          </script>';
      }
    }
  }

  /* ❌ Eliminar cliente */
  static public function ctrEliminarCliente() {
    if (isset($_GET["idClienteEliminar"])) {
      $tabla = "clientes";
      $id = $_GET["idClienteEliminar"];
      $respuesta = ModeloCliente::mdlEliminarCliente($tabla, $id);

      if ($respuesta == "ok") {
        echo '
          <script>
            Swal.fire({
              icon: "success",
              title: "Cliente eliminado",
              text: "El cliente ha sido eliminado correctamente",
              confirmButtonText: "Cerrar"
            }).then(function(result) {
              if (result.isConfirmed) {
                window.location = "clientes";
              }
            });
          </script>';
      } else {
        echo '
          <script>
            Swal.fire({
              icon: "error",
              title: "Error al eliminar",
              text: "No se pudo eliminar el cliente. Intente nuevamente.",
              confirmButtonText: "Cerrar"
            });
          </script>';
      }
    }
  }
}
