<?php

class ControladorUsuarios {

  /**
   * MÉTODO PARA INGRESO DE USUARIO (LOGIN)
   * =======================================
   */
  public function ctrIngresoUsuario() {

    if (isset($_POST["ingUsuario"])) {

      if (preg_match('/^[a-zA-Z0-9]+$/', $_POST["ingUsuario"]) &&
          preg_match('/^[a-zA-Z0-9]+$/', $_POST["ingPassword"])) {

        $encriptar = crypt($_POST["ingPassword"], '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');

        $tabla = "usuarios";
        $item = "usuario";
        $valor = $_POST["ingUsuario"];

        $respuesta = ModeloUsuarios::MdlMostrarUsuarios($tabla, $item, $valor);

        // DEPURACIÓN: Verifica si la consulta devolvió datos
        if (!$respuesta || !is_array($respuesta)) {
          echo '<br><div class="alert alert-danger">Usuario no encontrado en la base de datos</div>';
          error_log("Login error: No user found for usuario=".$_POST["ingUsuario"]);
          return;
        }

        // DEPURACIÓN: Muestra datos obtenidos
        // echo '<pre>'; print_r($respuesta); echo '</pre>';

        // Verifica usuario y contraseña
        if ($respuesta["usuario"] == $_POST["ingUsuario"] && $respuesta["password"] == $encriptar) {

          if($respuesta["estado"] == 1){

            // Generar token único de sesión
            $sesion_token = bin2hex(random_bytes(32));

            // Definir expiración de sesión (ejemplo: 30 días desde ahora)
            date_default_timezone_set('America/Lima');
            $fechaExpira = date('Y-m-d H:i:s', strtotime('+30 days'));

            // Verificar si ya hay una sesión activa
            if (!empty($respuesta["sesion_token"])) {
              // Verificar si la sesión ha expirado
              $current_time = date('Y-m-d H:i:s');
              if ($respuesta["sesion_expira"] < $current_time) {
                // Token is expired, allow login and generate new token
                // (The new token will be generated below)
              } else {
                // Sesión activa, verificar token
                if (isset($_SESSION["sesion_token"]) && $_SESSION["sesion_token"] === $respuesta["sesion_token"]) {
                  // Continuar con la sesión actual
                } else {
                  echo '<br><div class="alert alert-warning">Este usuario ya tiene una sesión activa en otro lugar.</div>';
                  return;
                }
              }
            }

            $_SESSION["iniciarSesion"] = "ok";
            $_SESSION["id"] = $respuesta["id"];
            $_SESSION["nombre"] = $respuesta["nombre"];
            $_SESSION["usuario"] = $respuesta["usuario"];
            $_SESSION["foto"] = $respuesta["foto"];
            $_SESSION["perfil"] = $respuesta["perfil"];
            $_SESSION["sesion_token"] = $sesion_token;

            // Unificar zona horaria a America/Lima
            date_default_timezone_set('America/Lima');
            $fecha = date('Y-m-d');
            $hora = date('H:i:s');
            $fechaActual = $fecha.' '.$hora;

            // Actualizar último login, token de sesión y fecha de expiración
            ModeloUsuarios::mdlActualizarCampoUsuario($tabla, "ultimo_login", $fechaActual, "id", $respuesta["id"]);
            ModeloUsuarios::mdlActualizarCampoUsuario($tabla, "sesion_token", $sesion_token, "id", $respuesta["id"]);
            ModeloUsuarios::mdlActualizarCampoUsuario($tabla, "sesion_expira", $fechaExpira, "id", $respuesta["id"]);

            // Eliminar manejo de "Recordarme" para simplificar sesión persistente
            // La sesión PHP se mantendrá activa con cookie de sesión extendida

            // Configurar cookie de sesión persistente (30 días)
            session_set_cookie_params(30 * 24 * 60 * 60); // 30 días en segundos

            echo '<script>window.location = "inicio";</script>';

          } else {
            echo '<br><div class="alert alert-danger">El usuario está inactivo</div>';
            error_log("Login error: Usuario inactivo usuario=".$_POST["ingUsuario"]);
          }

        } else {
          echo '<br><div class="alert alert-danger">Usuario o contraseña incorrectos</div>';
          error_log("Login error: Contraseña incorrecta para usuario=".$_POST["ingUsuario"]);
        }

      } else {
        echo '<br><div class="alert alert-danger">Formato de usuario o contraseña inválido</div>';
        error_log("Login error: Formato inválido usuario=".$_POST["ingUsuario"]);
      }
    }
  }

  /**
   * MÉTODO PARA REGISTRAR UN NUEVO USUARIO
   * =======================================
   */

  static public function ctrCrearUsuario(){
        if(isset($_POST["nuevoUsuario"])){
            /**preg_match SIRVE PARA VALIDAR EL CAMPO QUE SOLO ACEPTE CIERTOS CARACTERES */
            if(preg_match('/^[\p{L}\p{N}\p{P}\p{M}\s]+$/u',$_POST["nuevoNombre"])&&
               preg_match('/^[a-zA-Z0-9]+$/',$_POST["nuevoUsuario"])&&
               preg_match('/^[a-zA-Z0-9]+$/',$_POST["nuevoPassword"])){

                /*======================================
                    VALIDAR IMAGEN
                ========================================*/
                $ruta = ""; /**PARA VALIDAR QUE ESTE VACIO SI NO CARGA NINGUNA IMAGEN */

                if(isset($_FILES["nuevaFoto"]["tmp_name"]) && !empty($_FILES["nuevaFoto"]["tmp_name"])){ // <-- Asegura que el archivo existe
                    list($ancho,$alto) = getimagesize($_FILES["nuevaFoto"]["tmp_name"]);
                    $nuevoAncho = 500;
                    $nuevoAlto = 500;

                    $directorio = "vistas/img/usuarios/".$_POST["nuevoUsuario"];

                    // Verifica si el directorio ya existe antes de crearlo
                    if(!is_dir($directorio)){
                        if(!mkdir($directorio, 0755, true)){
                            echo '<br><div class="alert alert-danger">No se pudo crear el directorio para la imagen</div>';
                        }
                    }

                    if($_FILES["nuevaFoto"]["type"] == "image/jpeg"){
                        $aleatorio = mt_rand(100,999);
                        $ruta = $directorio."/".$aleatorio.".jpg";

                        $origen = imagecreatefromjpeg($_FILES["nuevaFoto"]["tmp_name"]);
                        $destino = imagecreatetruecolor($nuevoAncho,$nuevoAlto);
                        imagecopyresized($destino,$origen,0,0,0,0,$nuevoAncho,$nuevoAlto,$ancho,$alto);
                        imagejpeg($destino,$ruta);
                    }
                    if($_FILES["nuevaFoto"]["type"] == "image/png"){
                        $aleatorio = mt_rand(100,999);
                        $ruta = $directorio."/".$aleatorio.".png";

                        $origen = imagecreatefrompng($_FILES["nuevaFoto"]["tmp_name"]);
                        $destino = imagecreatetruecolor($nuevoAncho,$nuevoAlto);
                        imagecopyresized($destino,$origen,0,0,0,0,$nuevoAncho,$nuevoAlto,$ancho,$alto);
                        imagepng($destino,$ruta);
                    }
                }

                $tabla = "usuarios";

                $encriptar = crypt($_POST["nuevoPassword"],'$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');

                $datos = array("nombre" => $_POST["nuevoNombre"],
                               "usuario" => $_POST["nuevoUsuario"],
                               "password" => $encriptar, //$encriptar
                               "perfil" => $_POST["nuevoPerfil"],
                               "foto" => $ruta);
                
                $respuesta = ModeloUsuarios::mdlRegistrarUsuario($tabla,$datos);

                if($respuesta == "ok" ){

                    echo '<script>
                
                    swal.fire({
                        icon: "success",
                        title: "¡El usuario ha sido registrado correctamente!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar",
                        showCloseButton: true

                    }).then((result=>{
                    
                        if(result.value){
                            window.location = "usuarios";
                        }

                    }));

                </script>';

                }

            }
            else
            {
                echo '<script>
                
                    swal.fire({
                        icon: "error",
                        title: "¡El campo usuario no puede estar vacio o con caracteres especiales!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar",
                        showCloseButton: true

                    }).then((result=>{
                    
                        if(result.value){
                            window.location = "usuarios";
                        }

                    }));

                </script>';
            }
        }
    }
  /*=============================================
	Mostrar USUARIO
	=============================================*/
  static public function ctrMostrarUsuarios($item,$valor){
        $tabla = "usuarios";

        $respuesta = ModeloUsuarios::MdlMostrarUsuarios($tabla,$item,$valor);

        return $respuesta;
    }
  /*=============================================
	EDITAR USUARIO
	=============================================*/

	static public function ctrEditarUsuario(){

		if(isset($_POST["editarUsuario"])){

			if(preg_match('/^[\p{L}\p{N}\p{P}\p{M}\s]+$/u', $_POST["editarNombre"])){

				/*=============================================
				VALIDAR IMAGEN
				=============================================*/

				$ruta = $_POST["fotoActual"];

				if(isset($_FILES["editarFoto"]["tmp_name"]) && !empty($_FILES["editarFoto"]["tmp_name"])){

					list($ancho, $alto) = getimagesize($_FILES["editarFoto"]["tmp_name"]);

					$nuevoAncho = 500;
					$nuevoAlto = 500;

					/*=============================================
					CREAMOS EL DIRECTORIO DONDE VAMOS A GUARDAR LA FOTO DEL USUARIO
					=============================================*/

					$directorio = "vistas/img/usuarios/".$_POST["editarUsuario"];

					/*=============================================
					PRIMERO PREGUNTAMOS SI EXISTE OTRA IMAGEN EN LA BD
					=============================================*/

					if(!empty($_POST["fotoActual"])){

						unlink($_POST["fotoActual"]);

					}else{

						mkdir($directorio, 0755);

					}	

					/*=============================================
					DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP
					=============================================*/

					if($_FILES["editarFoto"]["type"] == "image/jpeg"){

						/*=============================================
						GUARDAMOS LA IMAGEN EN EL DIRECTORIO
						=============================================*/

						$aleatorio = mt_rand(100,999);

						$ruta = "vistas/img/usuarios/".$_POST["editarUsuario"]."/".$aleatorio.".jpg";

						$origen = imagecreatefromjpeg($_FILES["editarFoto"]["tmp_name"]);						

						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

						imagejpeg($destino, $ruta);

					}

					if($_FILES["editarFoto"]["type"] == "image/png"){

						/*=============================================
						GUARDAMOS LA IMAGEN EN EL DIRECTORIO
						=============================================*/

						$aleatorio = mt_rand(100,999);

						$ruta = "vistas/img/usuarios/".$_POST["editarUsuario"]."/".$aleatorio.".png";

						$origen = imagecreatefrompng($_FILES["editarFoto"]["tmp_name"]);						

						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

						imagepng($destino, $ruta);

					}

				}

				$tabla = "usuarios";

				if($_POST["editarPassword"] != ""){

					if(preg_match('/^[a-zA-Z0-9]+$/', $_POST["editarPassword"])){

						$encriptar = crypt($_POST["editarPassword"], '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');

					}else{

						echo'<script>

								swal({
									  type: "error",
									  title: "¡La contraseña no puede ir vacía o llevar caracteres especiales!",
									  showConfirmButton: true,
									  confirmButtonText: "Cerrar"
									  }).then(function(result) {
										if (result.value) {

										window.location = "usuarios";

										}
									})

						  	</script>';

						  	return;

					}

				}else{

					$encriptar = $_POST["passwordActual"];

				}

				$datos = array("nombre" => $_POST["editarNombre"],
							   "usuario" => $_POST["editarUsuario"],
							   "password" => $encriptar,
							   "perfil" => $_POST["editarPerfil"],
							   "foto" => $ruta,
							   "estado" => $_POST["editarEstado"]);

				$respuesta = ModeloUsuarios::mdlEditarUsuario($tabla, $datos);

				if($respuesta == "ok"){

					echo '<script>
            Swal.fire({
              icon: "success",
              title: "¡El usuario ha sido Editado correctamente!",
              showConfirmButton: true,
              confirmButtonText: "Cerrar",
              showCloseButton: true
            }).then((result) => {
              if(result.value){
                window.location = "usuarios";
              }
            });
          </script>';

				}


			}else{

				echo'<script>

					Swal.fire({
						  type: "error",
						  title: "¡El nombre no puede ir vacío o llevar caracteres especiales!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result) {
							if (result.value) {
							window.location = "usuarios";
							}
						});
			  	</script>';
			}
		}
  }
/**========================================

* MÉTODO PARA BORRAR USUARIOS

* =======================================

*/

static public function ctrBorrarUsuario(){

  if(isset($_GET["idUsuario"])){

    // Verificar permisos: solo Administrador puede borrar usuarios
    if($_SESSION["perfil"] == "Vendedor"){

      echo '<script>

        Swal.fire({

          icon: "error",

          title: "¡No tienes permisos para eliminar usuarios!",

          showConfirmButton: true,

          confirmButtonText: "Cerrar",

          showCloseButton: true

        }).then((result) => {

          if(result.isConfirmed){

            window.location = "usuarios";

          }

        });

      </script>';

      return;

    }

    $tabla = "usuarios";

    $datos = $_GET["idUsuario"];

    if($_GET["fotoUsuario"] != ""){

      unlink($_GET["fotoUsuario"]);

      rmdir('vistas/img/usuarios/'.$_GET["usuario"]);

    }

    $respuesta = ModeloUsuarios::mdlBorrarUsuario($tabla,$datos);

    if($respuesta == "ok"){

          echo '<script>

          Swal.fire({

            icon: "success",

            title: "¡El usuario se ha borrado correctamente!",

            showConfirmButton: true,

            confirmButtonText: "Cerrar",

            showCloseButton: true

          }).then((result) => {

            if(result.isConfirmed){

              window.location = "usuarios";

            }

          });

        </script>';

        }

  }

}





}

