function loadOportunidades() {
    $.ajax({
        url: '/Proyecto_atlantis/Ventas/ajax/oportunidades.ajax.php',
        method: 'GET',
        data: { action: 'getOportunidades' },
        dataType: 'json',
        success: function(data) {
            console.log("loadOportunidades - datos recibidos:", data);
            // Limpiar todas las columnas antes de agregar tarjetas
            const estados = ["nuevo", "calificado", "propuesto", "ganado"];
            estados.forEach(function(estado) {
                $('#' + estado).empty();
            });

            const oportunidades = data;
            oportunidades.forEach(function(oportunidad) {
                agregarTarjeta(oportunidad);
            });
        }
    });
}

function agregarTarjeta(oportunidad) {
    console.log("Agregando tarjeta para oportunidad:", oportunidad);
    var tarjeta = 
        '<div class="card mb-2" id="oportunidad-' + oportunidad.id + '" draggable="true" ondragstart="drag(event)">' +
            '<div class="card-body">' +
                '<h5 class="card-title">' + oportunidad.titulo + '</h5>' +
                '<p class="card-text">' + oportunidad.descripcion + '</p>' +
                '<p class="card-text">Valor: ' + oportunidad.valor_estimado + ' - Probabilidad: ' + oportunidad.probabilidad + '%</p>' +
                '<button class="btn btn-success" onclick="cambiarEstado(' + oportunidad.id + ', \'calificado\')">Calificado</button>' +
                '<button class="btn btn-warning" onclick="cambiarEstado(' + oportunidad.id + ', \'propuesto\')">Propuesto</button>' +
                '<button class="btn btn-info" onclick="cambiarEstado(' + oportunidad.id + ', \'ganado\')">Ganado</button>' +
                '<button class="btn btn-danger" onclick="eliminarOportunidad(' + oportunidad.id + ')">Eliminar</button>' +
            '</div>' +
        '</div>';
    $('#' + oportunidad.estado).append(tarjeta);
}

function crearOportunidad() {
    console.log("crearOportunidad called");
    var nuevaOportunidad = {
        nuevoTitulo: $('#titulo').val(),
        nuevaDescripcion: $('#descripcion').val(),
        nuevoValorEstimado: $('#valor_estimado').val(),
        nuevaProbabilidad: $('#probabilidad').val(),
        idCliente: $('#cliente_id').val(),
        idUsuario: $('#usuario_id').val(),
        nuevoEstado: 'nuevo',
        nuevaFechaCierre: $('#fecha_cierre').val()
    };

    $.ajax({
        url: '/Proyecto_atlantis/Ventas/ajax/oportunidades.ajax.php',
        method: 'POST',
        data: Object.assign({ action: 'crearOportunidad' }, nuevaOportunidad),
        dataType: 'json',
        success: function(response) {
            console.log("AJAX success response:", response);
            if (response.status === "success") {
                $('#modal-nueva-oportunidad').modal('hide');
                loadOportunidades();
            } else {
                alert(response.message);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX error:", textStatus, errorThrown);
            alert("Error en la solicitud AJAX: " + textStatus);
        }
    });
}

function cambiarEstado(id, nuevoEstado) {
    console.log("cambiarEstado llamado con id:", id, "nuevoEstado:", nuevoEstado);
    var datos = new FormData();
    datos.append('action', 'cambiarEstado');
    datos.append('idOportunidad', id);
    datos.append('nuevoEstado', nuevoEstado);

    $.ajax({
        url: '/Proyecto_atlantis/Ventas/ajax/oportunidades.ajax.php',
        method: 'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(respuesta) {
            console.log("Respuesta cambiarEstado:", respuesta);
            if (respuesta && respuesta.status === "success") {
                // Mover la tarjeta al nuevo estado solo si la base de datos respondió OK
                var tarjeta = $('#oportunidad-' + id);
                if (tarjeta.length) {
                    tarjeta.detach();
                    $('#' + nuevoEstado).append(tarjeta);
                }
            } else {
                Swal.fire({
                    title: "Error",
                    text: "No se pudo actualizar el estado en la base de datos.",
                    icon: "error",
                    confirmButtonText: "Cerrar"
                });
            }
        },
        error: function() {
            Swal.fire({
                title: "Error",
                text: "No se pudo actualizar el estado.",
                icon: "error",
                confirmButtonText: "Cerrar"
            });
        }
    });
}

function eliminarOportunidad(id) {
    console.log("eliminarOportunidad llamado con id:", id);
    $.ajax({
        url: '/Proyecto_atlantis/Ventas/ajax/oportunidades.ajax.php',
        method: 'POST',
        data: { action: 'eliminarOportunidad', id: id },
        success: function(response) {
            console.log("Respuesta eliminarOportunidad:", response);
            loadOportunidades();
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Error en eliminarOportunidad:", textStatus, errorThrown);
            alert("Error al eliminar oportunidad: " + textStatus);
        }
    });
}

function loadClientes() {
    $.ajax({
        url: '/Proyecto_atlantis/Ventas/ajax/oportunidades.ajax.php',
        method: 'GET',
        data: { action: 'getClientes' },
        dataType: 'json',
        success: function(data) {
            console.log("Respuesta AJAX getClientes:", data);
            var clientes = data;
            console.log("Clientes parseados:", clientes);
            clientes.forEach(function(cliente) {
                console.log("Agregando cliente al select:", cliente);
                $('#cliente_id').append(new Option(cliente.nombre, cliente.id));
            });
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Error en AJAX getClientes:", textStatus, errorThrown);
        }
    });
}
