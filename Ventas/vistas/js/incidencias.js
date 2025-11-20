// Archivo JavaScript para manejar funcionalidades de incidencias

$(document).ready(function() {
    // Verificar si hay parámetros en la URL para preseleccionar cliente
    var urlParams = new URLSearchParams(window.location.search);
    var idCliente = urlParams.get('idCliente');
    var nombreCliente = urlParams.get('nombreCliente');

    if (idCliente && nombreCliente) {
        // Preseleccionar cliente y abrir modal
        $('#nuevoNombreCliente').val(decodeURIComponent(nombreCliente));
        $('#idClienteSeleccionado').val(idCliente);
        $('#nuevoNombreCliente').prop('disabled', true);

        // Generar correlativo antes de abrir el modal
        generarCorrelativo();

        $('#modalRegistrarIncidencia').modal('show');
    }

    // Generar correlativo al abrir el modal
    $('#modalRegistrarIncidencia').on('show.bs.modal', function() {
        generarCorrelativo();
    });

    // Función para generar correlativo
    function generarCorrelativo() {
        $.ajax({
            url: 'ajax/incidencias.ajax.php',
            method: 'GET',
            data: { action: 'generarCorrelativo' },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#nuevoCorrelativo').val(response.correlativo);
                }
            }
        });
    }

    // Configurar autocomplete para nombre del cliente
    $('#nuevoNombreCliente').autocomplete({
        source: function(request, response) {
            $.ajax({
                url: 'ajax/incidencias.ajax.php',
                method: 'GET',
                data: {
                    action: 'buscarClientes',
                    term: request.term
                },
                dataType: 'json',
                success: function(data) {
                    response(data);
                }
            });
        },
        minLength: 2,
        select: function(event, ui) {
            $('#nuevoNombreCliente').val(ui.item.label);
            $('#idClienteSeleccionado').val(ui.item.value);
            return false;
        },
        focus: function(event, ui) {
            $('#nuevoNombreCliente').val(ui.item.label);
            return false;
        }
    });

    // Limpiar campo oculto cuando se cambia el texto manualmente
    $('#nuevoNombreCliente').on('input', function() {
        $('#idClienteSeleccionado').val('');
    });

    // Validación y envío AJAX del formulario de crear incidencia
    $('#modalRegistrarIncidencia form').on('submit', function(e) {
        e.preventDefault(); // Prevenir envío normal del formulario

        var nombreCliente = $('#nuevoNombreCliente').val().trim();
        var idCliente = $('#idClienteSeleccionado').val();

        if (nombreCliente && !idCliente) {
            Swal.fire({
                icon: 'warning',
                title: 'Cliente no válido',
                text: 'Por favor, selecciona un cliente válido de la lista de sugerencias.',
                confirmButtonText: 'Aceptar'
            });
            $('#nuevoNombreCliente').focus();
            return false;
        }

        // Enviar formulario vía AJAX
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#modalRegistrarIncidencia').modal('hide');
                    $(this).trigger('reset'); // Limpiar formulario
                    $('#idClienteSeleccionado').val(''); // Limpiar campo oculto
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message || 'Incidencia registrada correctamente',
                        confirmButtonText: 'Aceptar'
                    }).then(function() {
                        cargarIncidencias(); // Recargar tabla
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Error al registrar la incidencia',
                        confirmButtonText: 'Aceptar'
                    });
                }
            }.bind(this),
            error: function(xhr, status, error) {
                console.error('Error AJAX crear incidencia:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al registrar la incidencia',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    });

    // Cargar datos de incidencias en la tabla al cargar la página
    cargarIncidencias();

    // Función para cargar incidencias
    function cargarIncidencias() {
        $.ajax({
            url: 'ajax/incidencias.ajax.php',
            method: 'GET',
            data: { action: 'mostrarIncidencias' },
            dataType: 'json',
            success: function(data) {
                var tbody = $('#tablaIncidencias tbody');
                tbody.empty();

                if (data && data.length > 0) {
                    data.forEach(function(incidencia, index) {
                        var fila = '<tr>' +
                            '<td>' + (index + 1) + '</td>' +
                            '<td>' + (incidencia.correlativo || '') + '</td>' +
                            '<td>' + incidencia.nombre_incidencia + '</td>' +
                            '<td>' + (incidencia.nombre_cliente || '') + '</td>' +
                            '<td>' + (incidencia.fecha_solicitud || '') + '</td>' +
                            '<td>' + incidencia.prioridad + '</td>' +
                            '<td>' + (incidencia.observaciones || '') + '</td>' +
                            '<td>' + incidencia.fecha_creacion + '</td>' +
                            '<td>' +
                                '<div class="btn-group">' +
                                    '<button class="btn btn-warning btnEditarIncidencia" idIncidencia="' + incidencia.id + '"><i class="fa fa-pencil"></i></button>' +
                                    '<button class="btn btn-danger btnEliminarIncidencia" idIncidencia="' + incidencia.id + '"><i class="fa fa-trash"></i></button>' +
                                '</div>' +
                            '</td>' +
                        '</tr>';
                        tbody.append(fila);
                    });
                } else {
                    tbody.append('<tr><td colspan="9" class="text-center">No hay incidencias registradas</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar incidencias:', error);
                $('#tablaIncidencias tbody').html('<tr><td colspan="9" class="text-center">Error al cargar las incidencias</td></tr>');
            }
        });
    }

    // Recargar tabla después de crear incidencia
    $('#modalRegistrarIncidencia').on('hidden.bs.modal', function() {
        cargarIncidencias();
    });

    // Recargar tabla después de editar incidencia
    $('#modalEditarIncidencia').on('hidden.bs.modal', function() {
        cargarIncidencias();
    });

    // Botón editar incidencia
    $(document).on('click', '.btnEditarIncidencia', function() {
        var idIncidencia = $(this).attr('idIncidencia');

        // Obtener datos de la incidencia
        $.ajax({
            url: 'ajax/incidencias.ajax.php',
            method: 'GET',
            data: {
                action: 'obtenerIncidencia',
                id: idIncidencia
            },
            dataType: 'json',
            success: function(data) {
                if (data && data.length > 0) {
                    var incidencia = data[0];

                    // Llenar el modal con los datos
                    $('#editarIdIncidencia').val(incidencia.id);
                    $('#editarCorrelativo').val(incidencia.correlativo || '');
                    $('#editarNombreIncidencia').val(incidencia.nombre_incidencia);
                    $('#editarNombreCliente').val(incidencia.nombre_cliente || '');
                    $('#editarIdClienteSeleccionado').val(incidencia.cliente_id);
                    $('#editarFecha').val(incidencia.fecha_solicitud);
                    $('#editarPrioridad').val(incidencia.prioridad);
                    $('#editarObservaciones').val(incidencia.observaciones || '');

                    // Abrir modal
                    $('#modalEditarIncidencia').modal('show');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al obtener datos de la incidencia:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al cargar los datos de la incidencia',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    });

    // Configurar autocomplete para editar cliente
    $('#editarNombreCliente').autocomplete({
        source: function(request, response) {
            $.ajax({
                url: 'ajax/incidencias.ajax.php',
                method: 'GET',
                data: {
                    action: 'buscarClientes',
                    term: request.term
                },
                dataType: 'json',
                success: function(data) {
                    response(data);
                }
            });
        },
        minLength: 2,
        select: function(event, ui) {
            $('#editarNombreCliente').val(ui.item.label);
            $('#editarIdClienteSeleccionado').val(ui.item.value);
            return false;
        },
        focus: function(event, ui) {
            $('#editarNombreCliente').val(ui.item.label);
            return false;
        }
    });

    // Limpiar campo oculto al editar cliente
    $('#editarNombreCliente').on('input', function() {
        if ($(this).val() !== $(this).data('selected-label')) {
            $('#editarIdClienteSeleccionado').val('');
        }
    });

    // Deshabilitar campo de cliente al abrir modal de edición
    $('#modalEditarIncidencia').on('show.bs.modal', function() {
        $('#editarNombreCliente').prop('disabled', true);
    });

    // Validación del formulario de edición
    $('#modalEditarIncidencia form').on('submit', function(e) {
        var nombreCliente = $('#editarNombreCliente').val().trim();
        var idCliente = $('#editarIdClienteSeleccionado').val();

        if (nombreCliente && !idCliente) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Cliente no válido',
                text: 'Por favor, selecciona un cliente válido de la lista de sugerencias.',
                confirmButtonText: 'Aceptar'
            });
            $('#editarNombreCliente').focus();
            return false;
        }

        // Mostrar mensaje de éxito con SweetAlert
        e.preventDefault(); // Prevenir envío normal del formulario

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#modalEditarIncidencia').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Incidencia actualizada correctamente',
                        confirmButtonText: 'Aceptar'
                    }).then(function() {
                        cargarIncidencias(); // Recargar tabla
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Error al actualizar la incidencia',
                        confirmButtonText: 'Aceptar'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX editar incidencia:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al actualizar la incidencia',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    });

    // Botón eliminar incidencia
    $(document).on('click', '.btnEliminarIncidencia', function() {
        var idIncidencia = $(this).attr('idIncidencia');

        // Confirmar eliminación con SweetAlert
        Swal.fire({
            title: '¿Está seguro?',
            text: '¿Desea eliminar esta incidencia? Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'ajax/incidencias.ajax.php',
                    method: 'POST',
                    data: {
                        idIncidenciaEliminar: idIncidencia
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Eliminado!',
                                text: response.message || 'Incidencia eliminada correctamente',
                                confirmButtonText: 'Aceptar'
                            }).then(function() {
                                cargarIncidencias(); // Recargar tabla
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Error al eliminar la incidencia',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error AJAX eliminar incidencia:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al eliminar la incidencia',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
            }
        });
    });
});
