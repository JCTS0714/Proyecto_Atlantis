// Archivo JavaScript para manejar funcionalidades de incidencias

$(document).ready(function() {
    // Verificar si hay parámetros en la URL para preseleccionar cliente
    var urlParams = new URLSearchParams(window.location.search);
    var idCliente = urlParams.get('idCliente');
    var nombreCliente = urlParams.get('nombreCliente');

    if (idCliente && nombreCliente) {
        // Marcar para preselección: aplicaremos la selección cuando el modal se muestre
        window.preselectIncidencia = {
            id: idCliente,
            name: decodeURIComponent(nombreCliente)
        };
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

    // Inicializar búsqueda de cliente usando Select2 (mejor experiencia que autocomplete)
    // Reutiliza el endpoint `ajax/clientes_oportunidades.ajax.php` usado por CRM
    // Mantener el input original como referencia pero ocultarlo y usar un <select> para Select2
    $('#nuevoNombreCliente').hide();
    if ($('#nuevoClienteSelect').length === 0) {
        $('<select id="nuevoClienteSelect" class="form-control input-lg" style="width:100%"></select>').insertAfter('#nuevoNombreCliente');
    }

    function initNuevoClienteSelect2() {
        var $sel = $('#nuevoClienteSelect');
        if ($sel.data('select2')) {
            try { $sel.select2('destroy'); } catch(e) { /* ignore */ }
        }

        $sel.select2({
            placeholder: 'Buscar cliente',
            minimumInputLength: 1,
            dropdownParent: $('#modalRegistrarIncidencia'),
            ajax: {
                url: 'ajax/clientes_oportunidades.ajax.php',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { q: params.term };
                },
                processResults: function(data) {
                    if (!Array.isArray(data)) return { results: [] };
                    return {
                        results: data.map(function(cliente) {
                            // Mostrar nombre del cliente + empresa/comercio si existe
                            var displayText = cliente.nombre;
                            if (cliente.empresa && cliente.empresa.trim() !== '') {
                                displayText += ' (' + cliente.empresa + ')';
                            }
                            return { id: cliente.id, text: displayText };
                        })
                    };
                },
                cache: true
            }
        });

        $sel.on('select2:select', function(e) {
            var data = e.params.data;
            $('#idClienteSeleccionado').val(data.id);
            // mantener compatibilidad visual: poner texto en el input oculto (por si algo lo lee)
            $('#nuevoNombreCliente').val(data.text);
        });

        // Cuando se limpia el select, limpiar el id oculto
        $sel.on('select2:clear', function() {
            $('#idClienteSeleccionado').val('');
            $('#nuevoNombreCliente').val('');
        });
    }

    initNuevoClienteSelect2();
    $('#modalRegistrarIncidencia').on('shown.bs.modal', function() {
        initNuevoClienteSelect2();
        // Si hay una preselección pendiente (viene desde la tabla clientes), aplicarla ahora
        if (window.preselectIncidencia && window.preselectIncidencia.id) {
            var decName = window.preselectIncidencia.name || '';
            var id = window.preselectIncidencia.id;
            try {
                var option = new Option(decName, id, true, true);
                $('#nuevoClienteSelect').empty().append(option).trigger('change');
                $('#nuevoClienteSelect').prop('disabled', true);
                $('#nuevoNombreCliente').hide();
                $('#idClienteSeleccionado').val(id);
            } catch(e) {
                // fallback: setear el input tradicional y deshabilitar
                $('#nuevoNombreCliente').val(decName).prop('disabled', true);
                $('#idClienteSeleccionado').val(id);
            }
            // Limpiar la bandera para futuros usos
            delete window.preselectIncidencia;
        } else {
            // Asegurar que cuando el modal se abre manualmente (botón registrar incidencia)
            // el select esté habilitado y vacío
            try { $('#nuevoClienteSelect').prop('disabled', false).val(null).trigger('change'); } catch(e) {}
            $('#idClienteSeleccionado').val('');
            $('#nuevoNombreCliente').val('').prop('disabled', false); // keep the original text input hidden; Select2 is primary control
        }
    });

    // Si el usuario edita el texto manualmente (por compatibilidad), limpiar el id
    $('#nuevoNombreCliente').on('input', function() {
        $('#idClienteSeleccionado').val('');
        try { $('#nuevoClienteSelect').val(null).trigger('change'); } catch(e) {}
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
                        // Recargar tabla de incidencias
                        cargarIncidencias();
                        // Notificar a otras pestañas (backlog) que se creó una incidencia
                        try {
                            localStorage.setItem('incidencia_creada', JSON.stringify({ id: response.id || null, ts: Date.now() }));
                        } catch (e) { /* ignore */ }
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
                console.error('Error AJAX crear incidencia:', status, error, xhr && xhr.responseText);
                // Intentar parsear JSON de error del servidor
                try {
                    var server = xhr && xhr.responseText ? JSON.parse(xhr.responseText) : null;
                    var msg = server && (server.message || server.error) ? (server.message || server.error) : 'Error al registrar la incidencia';
                } catch(e) { var msg = 'Error al registrar la incidencia'; }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: msg,
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    });

    // Cargar datos de incidencias en la tabla al cargar la página
    cargarIncidencias();

    // Función para cargar incidencias (llenado de tbody, plantilla.js inicializará DataTable)
    function cargarIncidencias() {
        $.ajax({
            url: 'ajax/incidencias.ajax.php',
            method: 'GET',
            data: { action: 'mostrarIncidencias' },
            dataType: 'json',
            success: function(data) {
                // Verificar si hay error de autenticación
                if (data && data.status === 'error') {
                    var tbody = $('#tablaIncidencias tbody');
                    tbody.html('<tr><td colspan="9" class="text-center text-danger">Error: ' + data.message + '</td></tr>');
                    return;
                }
                
                var tbody = $('#tablaIncidencias tbody');
                tbody.empty();

                var rows = Array.isArray(data) ? data : (data && Array.isArray(data.incidencias) ? data.incidencias : []);

                if (rows && rows.length > 0) {
                    rows.forEach(function(incidencia, index) {
                        var fila = '<tr>' +
                                '<td>' + (index + 1) + '</td>' +
                                '<td>' + (incidencia.correlativo || '') + '</td>' +
                                '<td>' + (incidencia.nombre_incidencia || '') + '</td>' +
                                '<td>' + (incidencia.nombre_cliente || '') + '</td>' +
                                '<td>' + (incidencia.fecha || '') + '</td>' +
                                '<td>' + (incidencia.prioridad || '') + '</td>' +
                                '<td>' + (incidencia.observaciones || '') + '</td>' +
                                '<td>' + (incidencia.fecha_creacion || '') + '</td>' +
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

                // Si DataTable ya está inicializado, destruirlo y reinicializarlo para refrescar
                try {
                    if ($.fn.DataTable.isDataTable('#tablaIncidencias')) {
                        $('#tablaIncidencias').DataTable().destroy();
                    }
                    // Reinicializar DataTable si hay datos
                    if (rows && rows.length > 0) {
                        $('#tablaIncidencias').DataTable({
                            "responsive": true,
                            "autoWidth": false,
                            "pageLength": 10,
                            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
                            "language": {
                                "sProcessing":     "Procesando...",
                                "sLengthMenu":     "Mostrar _MENU_ registros",
                                "sZeroRecords":    "No se encontraron resultados",
                                "sEmptyTable":     "Ningún dato disponible en esta tabla",
                                "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
                                "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0",
                                "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                                "sInfoPostFix":    "",
                                "sSearch":         "Buscar:",
                                "sUrl":            "",
                                "sInfoThousands":  ",",
                                "sLoadingRecords": "Cargando...",
                                "oPaginate": {
                                    "sFirst":    "Primero",
                                    "sLast":     "Último",
                                    "sNext":     "Siguiente",
                                    "sPrevious": "Anterior"
                                },
                                "oAria": {
                                    "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                                }
                            }
                        });
                    }
                } catch(e) { 
                    // Error silencioso al inicializar DataTable
                }
            },
            error: function(xhr, status, error) {
                try {
                    var server = xhr && xhr.responseText ? JSON.parse(xhr.responseText) : null;
                    var msg = server && (server.message || server.error) ? (server.message || server.error) : null;
                } catch(e) { var msg = null; }
                var htmlMsg = '<tr><td colspan="9" class="text-center">Error al cargar las incidencias';
                if (msg) htmlMsg += ': ' + msg;
                htmlMsg += '</td></tr>';
                $('#tablaIncidencias tbody').html(htmlMsg);
            }
        });
    }

    // Recargar tabla después de crear incidencia
    $('#modalRegistrarIncidencia').on('hidden.bs.modal', function() {
        cargarIncidencias();
        // Reset Select2 and inputs
        try { $('#nuevoClienteSelect').val(null).trigger('change'); } catch(e) {}
        $('#idClienteSeleccionado').val('');
        $('#nuevoNombreCliente').val('').prop('disabled', false); // keep hidden
    });

    // Recargar tabla después de editar incidencia
    $('#modalEditarIncidencia').on('hidden.bs.modal', function() {
        cargarIncidencias();
        // Reset edit Select2 and inputs
        try { $('#editarClienteSelect').val(null).trigger('change'); } catch(e) {}
        $('#editarIdClienteSeleccionado').val('');
        $('#editarNombreCliente').val('').prop('disabled', false); // keep hidden; Select2 is primary control
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
                // Aceptar diferentes formatos de respuesta:
                // - Array directo: [{...}]
                // - Objeto con clave `incidencias`: { success:true, incidencias: [{...}] }
                var incidencia = null;
                if (Array.isArray(data) && data.length > 0) {
                    incidencia = data[0];
                } else if (data && data.incidencias && Array.isArray(data.incidencias) && data.incidencias.length > 0) {
                    incidencia = data.incidencias[0];
                }

                if (incidencia) {

                    // Llenar el modal con los datos
                    $('#editarIdIncidencia').val(incidencia.id);
                    $('#editarCorrelativo').val(incidencia.correlativo || '');
                    $('#editarNombreIncidencia').val(incidencia.nombre_incidencia);
                    // Construir texto visible: Nombre (Empresa) si existe
                    var empresa = incidencia.empresa_cliente || '';
                    var nombreClienteVisible = incidencia.nombre_cliente || '';
                    if (empresa && empresa.trim() !== '') {
                        nombreClienteVisible = nombreClienteVisible + ' (' + empresa + ')';
                    }

                    $('#editarNombreCliente').val(nombreClienteVisible);
                    $('#editarIdClienteSeleccionado').val(incidencia.cliente_id);
                    // Si el select2 existe, setear la opción seleccionada y deshabilitarla (comportamiento previo)
                    if ($('#editarClienteSelect').length) {
                        var opt = new Option(nombreClienteVisible || '', incidencia.cliente_id, true, true);
                        $('#editarClienteSelect').empty().append(opt).trigger('change');
                        $('#editarClienteSelect').prop('disabled', true);
                        $('#editarNombreCliente').hide();
                    } else {
                        $('#editarNombreCliente').prop('disabled', true);
                    }
                    $('#editarFecha').val(incidencia.fecha);
                    $('#editarPrioridad').val(incidencia.prioridad);
                    $('#editarObservaciones').val(incidencia.observaciones || '');

                    // Abrir modal
                    $('#modalEditarIncidencia').modal('show');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al obtener datos de la incidencia:', status, error, xhr && xhr.responseText);
                try {
                    var server = xhr && xhr.responseText ? JSON.parse(xhr.responseText) : null;
                    var msg = server && (server.message || server.error) ? (server.message || server.error) : 'Error al cargar los datos de la incidencia';
                } catch(e) { var msg = 'Error al cargar los datos de la incidencia'; }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: msg,
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    });

    // Inicializar Select2 para editar cliente (similar a nuevo)
    $('#editarNombreCliente').hide();
    if ($('#editarClienteSelect').length === 0) {
        $('<select id="editarClienteSelect" class="form-control input-lg" style="width:100%"></select>').insertAfter('#editarNombreCliente');
    }

    function initEditarClienteSelect2() {
        var $sel = $('#editarClienteSelect');
        if ($sel.data('select2')) {
            try { $sel.select2('destroy'); } catch(e) { /* ignore */ }
        }

        $sel.select2({
            placeholder: 'Buscar cliente',
            minimumInputLength: 1,
            dropdownParent: $('#modalEditarIncidencia'),
            ajax: {
                url: 'ajax/clientes_oportunidades.ajax.php',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { q: params.term };
                },
                processResults: function(data) {
                    if (!Array.isArray(data)) return { results: [] };
                    return {
                        results: data.map(function(cliente) {
                            // Mostrar nombre del cliente + empresa/comercio si existe
                            var displayText = cliente.nombre;
                            if (cliente.empresa && cliente.empresa.trim() !== '') {
                                displayText += ' (' + cliente.empresa + ')';
                            }
                            return { id: cliente.id, text: displayText };
                        })
                    };
                },
                cache: true
            }
        });

        $sel.on('select2:select', function(e) {
            var data = e.params.data;
            $('#editarIdClienteSeleccionado').val(data.id);
            $('#editarNombreCliente').val(data.text);
        });

        $sel.on('select2:clear', function() {
            $('#editarIdClienteSeleccionado').val('');
            $('#editarNombreCliente').val('');
        });
    }

    initEditarClienteSelect2();
    $('#modalEditarIncidencia').on('shown.bs.modal', function() {
        initEditarClienteSelect2();
    });

    // Cuando se abra el modal de edición, deshabilitamos el select si corresponde más abajo cuando se cargue el registro

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
                console.error('Error AJAX editar incidencia:', status, error, xhr && xhr.responseText);
                try {
                    var server = xhr && xhr.responseText ? JSON.parse(xhr.responseText) : null;
                    var msg = server && (server.message || server.error) ? (server.message || server.error) : 'Error al actualizar la incidencia';
                } catch(e) { var msg = 'Error al actualizar la incidencia'; }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: msg,
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
                        console.error('Error AJAX eliminar incidencia:', status, error, xhr && xhr.responseText);
                        try {
                            var server = xhr && xhr.responseText ? JSON.parse(xhr.responseText) : null;
                            var msg = server && (server.message || server.error) ? (server.message || server.error) : 'Error al eliminar la incidencia';
                        } catch(e) { var msg = 'Error al eliminar la incidencia'; }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: msg,
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
            }
        });
    });
});
