$(document).on("click", ".btnEditarProveedor", function() {
    var idProveedor = $(this).attr("idProveedor");
    var datos = new FormData();
    datos.append("idProveedor", idProveedor);

    $.ajax({
        url: "ajax/proveedor.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(respuesta) {
            $("#editarIdProveedor").val(respuesta["id"]);
            $("#editarRazonsocial").val(respuesta["razon_social"]);
            $("#editarRuc").val(respuesta["ruc"]);
            $("#editarDireccion").val(respuesta["direccion"]);
            $("#editarTelefono").val(respuesta["telefono"]);
            $("#editarTipoProveedor").val(respuesta["tipo_proveedor"]);
        }
    });
});

$(document).on("click", ".btnEliminarProveedor", function() {
    var idProveedor = $(this).attr("idProveedor");
    Swal.fire({
        title: '¿Está seguro de eliminar el proveedor?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location = "index.php?ruta=proveedor&idProveedorEliminar=" + idProveedor;
        }
    });
});
