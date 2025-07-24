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
            $("#editarFechaCreacion").val(respuesta["fecha_creacion"]);
        }
    });
});


$(document).on("click", ".btnEliminarCliente", function () {

  var idCliente = $(this).attr("idCliente");
  var cliente = $(this).attr("cliente"); // opcional, si quieres mostrar el nombre

  Swal.fire({
    title: "¿Estás seguro de borrar el cliente?",
    text: "¡Si no estás de acuerdo, puedes cancelar esta acción!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    cancelButtonText: "Cancelar",
    confirmButtonText: "Sí, borrar cliente"
  }).then((result) => {
    if (result.isConfirmed) {
      window.location = "index.php?ruta=clientes&idClienteEliminar=" + idCliente;
    }
  });

});
