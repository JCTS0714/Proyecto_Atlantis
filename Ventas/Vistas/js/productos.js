$(".nuevaImagen").change(function(){
    var imagen = this.files[0];

    if(imagen["type"] != "image/jpeg" && imagen["type"] != "image/png"){
        $(".nuevaImagen").val("");
        Swal.fire({
            title:"Error al subir imagen",
            text:"Imagen debe ser de formato JPG o PNG",
            icon:"error",
            confirmButtonText:"¡Cerrar!"
        });
    }
    else if(imagen["size"] > 2000000){
        $(".nuevaImagen").val("");
        Swal.fire({
            title:"Error al subir imagen",
            text:"Imagen debe ser menor de 2MB",
            icon:"error",
            confirmButtonText:"¡Cerrar!"
        });
    }
    else {
        var datosImagen = new FileReader();
        datosImagen.readAsDataURL(imagen);

        datosImagen.onload = function(event){
            var rutaImagen = event.target.result;
            $(".previsualizar").attr("src", rutaImagen);
        }
    }
})
//**EDITAR PRODUCTO */
$(document).on("click", ".btnEditarProducto", function(){
    var idProducto = $(this).attr("idProducto");
    var datos = new FormData();
    datos.append("idProducto", idProducto);

    $.ajax({
        url: "ajax/productos.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(respuesta){
            $("#idProducto").val(respuesta["id"]);
            $("#editarCodigo").val(respuesta["codigo"]);
            $("#editarNombre").val(respuesta["nombre"]);
            $("#editarDescripcion").val(respuesta["descripcion"]);
            $("#editarStock").val(respuesta["stock"]);
            $("#editarPrecioCompra").val(respuesta["precio_compra"]);
            $("#editarPrecioVenta").val(respuesta["precio_venta"]);
            $("#editarCategoria").val(respuesta["id_categoria"]);
            $("#fotoActual").val(respuesta["imagen"]);
            if(respuesta["imagen"] != "") {
                $(".previsualizarEditar").attr("src", respuesta["imagen"]);
            }
            else{
                $(".previsualizarEditar").attr("src", "Vistas/img/productos/producto.png");
            }
        }
    })
})
/**ELIMINAR PRODUCTO */
$(document).on("click", ".btnEliminarProducto", function(){
    var idProducto = $(this).attr("idProducto");
    var fotoProducto = $(this).attr("fotoProducto");
    var codigo = $(this).attr("codigo");
    Swal.fire({
        title: '¿Está seguro de eliminar el producto?',
        text: "¡Si no lo está puede cancelar la acción!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '¡Sí, eliminar!'
    }).then((result) => {
        if (result.value) {
            window.location = "index.php?ruta=productos&idProducto=" + idProducto + "&fotoProducto=" + fotoProducto + "&codigo=" + codigo;
        }
    })
})