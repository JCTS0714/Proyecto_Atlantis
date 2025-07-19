$(document).on("click", ".btnEditarCliente", function() {
    var idCliente = $(this).attr("idCliente");
    var datos = new FormData();
    datos.append("idCliente", idCliente);

    $.ajax({
        url: "ajax/clientes.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(respuesta) {
            $("#idCliente").val(respuesta["id"]);
            $("#editarNombre").val(respuesta["nombre"]);
            $("#editarTipo").val(respuesta["tipo"]);
            $("#editarDocumento").val(respuesta["documento"]);
            $("#editarTelefono").val(respuesta["telefono"]);
            $("#editarCorreo").val(respuesta["correo"]);
            $("#editarDireccion").val(respuesta["direccion"]);
            $("#editarClasificacion").val(respuesta["clasificacion"]);
            $("#editarFechaCreacion").val(respuesta["fecha_creacion"]);
        }
    });
});
