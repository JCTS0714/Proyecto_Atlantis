/**SUbir foto de usuario */

$(".nuevaFoto").change(function(){

    var imagen = this.files[0];

    /**validar la extension de la imagen. jpg o png  */

    if(imagen["type"] != "image/jpeg" && imagen["type"] != "image/png"){

        $(".nuevaFoto").val("");
        swal.fire({
            title: "Error al subir imagen",
            text: "La imagen debe ser de formato JPG o PNG",
            icon: "Error",
            confirmButtonText: "¡Cerrar!"
        });
    } 
    else if(imagen["size"] > 2000000){
        $(".nuevaFoto").val("");
        swal.fire({
            title: "Error al subir imagen",
            text: "La imagen no debe pesar mas de 2 MG",
            icon: "Error",
            confirmButtonText: "¡Cerrar!"
        });
    }
    else
    {
        var datosImagen = new FileReader;
        datosImagen.readAsDataURL(imagen);
        $(datosImagen).on("load",function(event){
            var rutaImagen = event.target.result;
            $(".previsualizar").attr("src",rutaImagen);
        })
    }
})

//EDITAR USUARIO
$(document).on("click",".btnEditarUsuario",function(){
    var idUsuario = $(this).attr("idUsuario");
    var datos = new FormData();
    datos.append("idUsuario", idUsuario);

    $.ajax({
        url: "ajax/usuarios.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType:false,
        processData:false,
        dataType:"json",
        success: function(respuesta){
            $("#editarNombre").val(respuesta["nombre"]);
            $("#editarUsuario").val(respuesta["usuario"]);
            $("#editarPerfil").html(respuesta["perfil"]);
            $("#editarPerfil").val(respuesta["perfil"]);
            $("#fotoActual").val(respuesta["foto"]);
            $("#passwordActual").val(respuesta["password"]);

            if(respuesta["foto"] !=""){
                $(".previsualizarEditar").attr("src",respuesta["foto"]);

            }
            else{
                $(".previsualizarEditar").attr("src","vistas/img/usuarios/default/perfil.png");

            }
        }
    })
})
//**ACTIVAR EL USUARIO */
$(document).off("click", ".btnActivar").on("click", ".btnActivar", function(event) {
  event.preventDefault();
  var $btn = $(this);
  var idUsuario = $btn.attr("idUsuario");
  var estadoUsuario = parseInt($btn.attr("estadoUsuario"));

  // Calcula el nuevo estado
  var nuevoEstado = estadoUsuario === 1 ? 0 : 1;

  // Actualiza el botón y el atributo para reflejar el nuevo estado
  if (nuevoEstado === 1) {
    $btn.removeClass("btn-danger").addClass("btn-success").html("Activado").attr("estadoUsuario", 1);
  } else {
    $btn.removeClass("btn-success").addClass("btn-danger").html("Desactivado").attr("estadoUsuario", 0);
  }

  // Envía el nuevo estado al backend
  var datos = new FormData();
  datos.append("activarId", idUsuario);
  datos.append("activarUsuario", nuevoEstado);

  $.ajax({
    url: "ajax/usuarios.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    success: function(respuesta) {
      // Puedes usar SweetAlert aquí para dar feedback si quieres
    }
  });
})
/*=============================================

ELIMINAR USUARIO

=============================================*/

$(document).on("click",".btnEliminarUsuario",function(){



  var idUsuario = $(this).attr("idUsuario");

  var fotoUsuario = $(this).attr("fotoUsuario");
  


  var usuario = $(this).attr("usuario");
  


  Swal.fire({

    title: "¿Estas seguro de borrar el usuario?",

    text: "¡Si no estas de acuerdo puede cancelar!",

    icon: "warning",

    showCancelButton: true,

    confirmButtonColor: "#3085d6",

    cancelButtonColor: "#d33",

    cancelButtonText: "Cancelar",

    confirmButtonText: "Si, borrar usuario"

    }).then((result) => {

    if (result.value) {



      window.location = "index.php?ruta=usuarios&idUsuario="+idUsuario+"&usuario="+usuario+"&fotoUsuario="+fotoUsuario;





      // Swal.fire({

// title: "Deleted!",

// text: "Your file has been deleted.",

// icon: "success"

    }

  })

})

  
// Limpiar formulario al abrir modal Agregar Usuario
$('#modalAgregarUsuario').on('show.bs.modal', function (e) {
  // Limpiar inputs de texto y password
  $(this).find('input[type="text"], input[type="password"]').val('');
  // Limpiar select
  $(this).find('select').val('');
  // Resetear imagen de previsualización a la imagen por defecto
  $(this).find('img.previsualizar').attr('src', 'vistas/img/usuarios/default/perfil.png');
});

// Botón personalizado para elegir archivo en modal Agregar Usuario
$(document).on('click', '.btnElegirArchivo', function() {
  $(this).siblings('input.nuevaFoto').click();
});

$(document).on('change', 'input.nuevaFoto', function() {
  var nombreArchivo = $(this).val().split('\\').pop();
  if(nombreArchivo) {
    $(this).siblings('.nombreArchivoSeleccionado').text(nombreArchivo);
  } else {
    $(this).siblings('.nombreArchivoSeleccionado').text('Ningún archivo seleccionado');
  }
});

