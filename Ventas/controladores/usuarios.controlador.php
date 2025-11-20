<?php

class ControladorUsuarios {

    // Ingreso (login)
    public function ctrIngresoUsuario() {
        if (!isset($_POST["ingUsuario"])) return;

        $ingUsuario = isset($_POST["ingUsuario"]) ? trim($_POST["ingUsuario"]) : '';
        $ingPassword = isset($_POST["ingPassword"]) ? $_POST["ingPassword"] : '';

        error_log("ctrIngresoUsuario: intento de login para usuario={$ingUsuario} | pwd_len=" . strlen($ingPassword));

        if ($ingUsuario === '' || $ingPassword === '') {
            echo '<br><div class="alert alert-danger">Formato de usuario o contraseña inválido</div>';
            return;
        }

        $tabla = "usuarios";
        $item = "usuario";
        $valor = $ingUsuario;

        $respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);
        if (!$respuesta || !is_array($respuesta)) {
            echo '<br><div class="alert alert-danger">Usuario no encontrado en la base de datos</div>';
            return;
        }

        $passwordVerified = false;
        $stored = isset($respuesta["password"]) ? $respuesta["password"] : '';

        // 1) modern verify
        if ($stored !== '' && password_verify($ingPassword, $stored)) {
            $passwordVerified = true;
        }

        // 2) legacy crypt salt
        if (!$passwordVerified && $stored !== '') {
            $legacy_salt = '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$';
            if (hash_equals($stored, crypt($ingPassword, $legacy_salt))) {
                $passwordVerified = true;
                $newHash = password_hash($ingPassword, PASSWORD_DEFAULT);
                ModeloUsuarios::mdlActualizarCampoUsuario($tabla, "password", $newHash, "id", $respuesta["id"]);
            }
        }

        // 3) MD5 legacy
        if (!$passwordVerified && strlen($stored) === 32) {
            if (hash_equals($stored, md5($ingPassword))) {
                $passwordVerified = true;
                $newHash = password_hash($ingPassword, PASSWORD_DEFAULT);
                ModeloUsuarios::mdlActualizarCampoUsuario($tabla, "password", $newHash, "id", $respuesta["id"]);
            }
        }

        if ($respuesta["usuario"] == $ingUsuario && $passwordVerified) {
            if ($respuesta["estado"] == 1) {
                $sesion_token = bin2hex(random_bytes(32));
                date_default_timezone_set('America/Lima');
                $fechaExpira = date('Y-m-d H:i:s', strtotime('+30 days'));

                $_SESSION["iniciarSesion"] = "ok";
                $_SESSION["id"] = $respuesta["id"];
                $_SESSION["nombre"] = $respuesta["nombre"];
                $_SESSION["usuario"] = $respuesta["usuario"];
                $_SESSION["foto"] = $respuesta["foto"];
                $_SESSION["perfil"] = $respuesta["perfil"];
                $_SESSION["sesion_token"] = $sesion_token;

                $fechaActual = date('Y-m-d H:i:s');
                ModeloUsuarios::mdlActualizarCampoUsuario($tabla, "ultimo_login", $fechaActual, "id", $respuesta["id"]);
                ModeloUsuarios::mdlActualizarCampoUsuario($tabla, "sesion_token", $sesion_token, "id", $respuesta["id"]);
                ModeloUsuarios::mdlActualizarCampoUsuario($tabla, "sesion_expira", $fechaExpira, "id", $respuesta["id"]);

                session_set_cookie_params(30 * 24 * 60 * 60);
                error_log("Login success: usuario=" . $ingUsuario . " | id=" . $respuesta["id"]);
                echo '<script>window.location = "inicio";</script>';

            } else {
                echo '<br><div class="alert alert-danger">El usuario está inactivo</div>';
            }
        } else {
            error_log("Login error: Contraseña incorrecta para usuario=" . $ingUsuario);
            echo '<br><div class="alert alert-danger">Usuario o contraseña incorrectos</div>';
        }
    }

    // Crear usuario
    static public function ctrCrearUsuario() {
        if (!isset($_POST["nuevoUsuario"])) return;

        $nuevoNombre = isset($_POST["nuevoNombre"]) ? trim($_POST["nuevoNombre"]) : '';
        $nuevoUsuario = isset($_POST["nuevoUsuario"]) ? trim($_POST["nuevoUsuario"]) : '';
        $nuevoPassword = isset($_POST["nuevoPassword"]) ? $_POST["nuevoPassword"] : '';

        if (!preg_match('/^[\\p{L}\\p{N}\\p{P}\\p{M}\\s]+$/u', $nuevoNombre) ||
            !preg_match('/^[\\p{L}\\p{N}._\\-\\s]+$/u', $nuevoUsuario) ||
            strlen($nuevoPassword) == 0) {
            echo '<script>swal.fire({icon: "error", title: "¡El campo usuario no puede estar vacio o con caracteres inválidos!", showConfirmButton: true, confirmButtonText: "Cerrar"}).then(()=>{ window.location = "usuarios"; });</script>';
            return;
        }

        $ruta = "";
        if (isset($_FILES["nuevaFoto"]["tmp_name"]) && !empty($_FILES["nuevaFoto"]["tmp_name"])) {
            list($ancho, $alto) = getimagesize($_FILES["nuevaFoto"]["tmp_name"]);
            $nuevoAncho = 500; $nuevoAlto = 500;
            $directorio = "vistas/img/usuarios/" . $nuevoUsuario;
            if (!is_dir($directorio)) mkdir($directorio, 0755, true);
            if ($_FILES["nuevaFoto"]["type"] == "image/jpeg") {
                $aleatorio = mt_rand(100,999);
                $ruta = $directorio . "/" . $aleatorio . ".jpg";
                $origen = imagecreatefromjpeg($_FILES["nuevaFoto"]["tmp_name"]);
                $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
                imagecopyresized($destino, $origen, 0,0,0,0, $nuevoAncho, $nuevoAlto, $ancho, $alto);
                imagejpeg($destino, $ruta);
            } elseif ($_FILES["nuevaFoto"]["type"] == "image/png") {
                $aleatorio = mt_rand(100,999);
                $ruta = $directorio . "/" . $aleatorio . ".png";
                $origen = imagecreatefrompng($_FILES["nuevaFoto"]["tmp_name"]);
                $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
                imagecopyresized($destino, $origen, 0,0,0,0, $nuevoAncho, $nuevoAlto, $ancho, $alto);
                imagepng($destino, $ruta);
            }
        }

        $tabla = "usuarios";
        $encriptar = password_hash($nuevoPassword, PASSWORD_DEFAULT);
        $datos = array(
            "nombre" => $nuevoNombre,
            "usuario" => $nuevoUsuario,
            "password" => $encriptar,
            "perfil" => (isset($_POST["nuevoPerfil"]) ? $_POST["nuevoPerfil"] : ''),
            "foto" => $ruta
        );

        $respuesta = ModeloUsuarios::mdlRegistrarUsuario($tabla, $datos);
        if ($respuesta === "ok") {
            echo '<script>swal.fire({icon: "success", title: "¡El usuario ha sido registrado correctamente!", showConfirmButton: true, confirmButtonText: "Cerrar"}).then(()=>{ window.location = "usuarios"; });</script>';
        } else {
            error_log("mdlRegistrarUsuario: ERROR para usuario=" . $nuevoUsuario . " | respuesta=" . json_encode($respuesta));
        }
    }

    // Mostrar usuario
    static public function ctrMostrarUsuarios($item, $valor) {
        $tabla = "usuarios";
        $respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);
        return $respuesta;
    }

    // Editar usuario
    static public function ctrEditarUsuario() {
        if (!isset($_POST["editarUsuario"])) return;

        if (!preg_match('/^[\\p{L}\\p{N}\\p{P}\\p{M}\\s]+$/u', $_POST["editarNombre"])) {
            echo '<script>swal.fire({icon: "error", title: "¡El nombre no puede ir vacío o llevar caracteres especiales!"}).then(()=>{ window.location = "usuarios"; });</script>';
            return;
        }

        $ruta = isset($_POST["fotoActual"]) ? $_POST["fotoActual"] : '';
        if (isset($_FILES["editarFoto"]["tmp_name"]) && !empty($_FILES["editarFoto"]["tmp_name"])) {
            list($ancho, $alto) = getimagesize($_FILES["editarFoto"]["tmp_name"]);
            $nuevoAncho = 500; $nuevoAlto = 500;
            $directorio = "vistas/img/usuarios/" . $_POST["editarUsuario"];
            if (!empty($_POST["fotoActual"])) {
                @unlink($_POST["fotoActual"]);
            } else {
                @mkdir($directorio, 0755);
            }
            if ($_FILES["editarFoto"]["type"] == "image/jpeg") {
                $aleatorio = mt_rand(100,999);
                $ruta = "vistas/img/usuarios/" . $_POST["editarUsuario"] . "/" . $aleatorio . ".jpg";
                $origen = imagecreatefromjpeg($_FILES["editarFoto"]["tmp_name"]);
                $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
                imagecopyresized($destino, $origen, 0,0,0,0, $nuevoAncho, $nuevoAlto, $ancho, $alto);
                imagejpeg($destino, $ruta);
            } elseif ($_FILES["editarFoto"]["type"] == "image/png") {
                $aleatorio = mt_rand(100,999);
                $ruta = "vistas/img/usuarios/" . $_POST["editarUsuario"] . "/" . $aleatorio . ".png";
                $origen = imagecreatefrompng($_FILES["editarFoto"]["tmp_name"]);
                $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
                imagecopyresized($destino, $origen, 0,0,0,0, $nuevoAncho, $nuevoAlto, $ancho, $alto);
                imagepng($destino, $ruta);
            }
        }

        $tabla = "usuarios";
        if (isset($_POST["editarPassword"]) && strlen($_POST["editarPassword"]) > 0) {
            $encriptar = password_hash($_POST["editarPassword"], PASSWORD_DEFAULT);
        } else {
            $encriptar = isset($_POST["passwordActual"]) ? $_POST["passwordActual"] : '';
        }

        $datos = array(
            "nombre" => $_POST["editarNombre"],
            "usuario" => $_POST["editarUsuario"],
            "password" => $encriptar,
            "perfil" => $_POST["editarPerfil"],
            "foto" => $ruta,
            "estado" => $_POST["editarEstado"]
        );

        $respuesta = ModeloUsuarios::mdlEditarUsuario($tabla, $datos);
        if ($respuesta == "ok") {
            echo '<script>swal.fire({icon: "success", title: "¡El usuario ha sido Editado correctamente!"}).then(()=>{ window.location = "usuarios"; });</script>';
        }
    }

    // Borrar usuario
    static public function ctrBorrarUsuario() {
        if (!isset($_GET["idUsuario"])) return;

        if ($_SESSION["perfil"] == "Vendedor") {
            echo '<script>swal.fire({icon: "error", title: "¡No tienes permisos para eliminar usuarios!"}).then(()=>{ window.location = "usuarios"; });</script>';
            return;
        }

        $tabla = "usuarios";
        $datos = $_GET["idUsuario"];
        if (isset($_GET["fotoUsuario"]) && $_GET["fotoUsuario"] != "") {
            @unlink($_GET["fotoUsuario"]);
            @rmdir('vistas/img/usuarios/' . $_GET["usuario"]);
        }

        $respuesta = ModeloUsuarios::mdlBorrarUsuario($tabla, $datos);
        if ($respuesta == "ok") {
            echo '<script>swal.fire({icon: "success", title: "¡El usuario se ha borrado correctamente!"}).then(()=>{ window.location = "usuarios"; });</script>';
        }
    }

  }
  