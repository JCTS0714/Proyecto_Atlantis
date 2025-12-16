// Archivo JavaScript para manejar funcionalidades de incidencias

try { console.log('incidencias.js cargado'); } catch(e) {}

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

        var $form = $(this);

        // Enviar formulario vía AJAX
        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            cache: false,
            success: function(response) {
                if (response && response.status === 'success') {
                    $('#modalRegistrarIncidencia').modal('hide');
                    $form[0].reset();
                    $('#idClienteSeleccionado').val('');
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message || 'Incidencia registrada correctamente',
                        confirmButtonText: 'Aceptar'
                    }).then(function() {
                        cargarIncidencias();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: (response && response.message) ? response.message : 'Error al registrar la incidencia',
                        confirmButtonText: 'Aceptar'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX crear incidencia:', status, error, xhr && xhr.responseText);
                var msg = 'Error al registrar la incidencia';
                try { var srv = xhr && xhr.responseText ? JSON.parse(xhr.responseText) : null; if (srv && (srv.message || srv.error)) msg = srv.message || srv.error; } catch(e) {}
                Swal.fire({ icon: 'error', title: 'Error', text: msg, confirmButtonText: 'Aceptar' });
            }
        });
    });

    // Function: cargarIncidencias - carga datos desde el servidor y actualiza la tabla
    function cargarIncidencias() {
        var $tabla = $('#tablaIncidencias');
        var $tbody = $tabla.find('tbody');

        // Debug panel removed in production: no visual debug elements created

        $.ajax({
            url: 'ajax/incidencias.ajax.php',
            method: 'GET',
            data: { action: 'mostrarIncidencias', _t: Date.now() },
            dataType: 'json',
            cache: false,
            success: function(data) {
                // server response received

                var rawRows = [];
                if (Array.isArray(data)) rawRows = data;
                else if (data && Array.isArray(data.incidencias)) rawRows = data.incidencias;
                else if (data && Array.isArray(data.data)) rawRows = data.data;
                else if (data && Array.isArray(data.rows)) rawRows = data.rows;
                else if (typeof data === 'string') {
                    try { var parsed = JSON.parse(data); if (Array.isArray(parsed)) rawRows = parsed; else if (parsed && Array.isArray(parsed.incidencias)) rawRows = parsed.incidencias; } catch(e) { rawRows = []; }
                }

                // resolved rows count:
                // resolved rows count processed

                // Force-show table and tbody in case CSS hides it
                try { $tabla.css('display','table'); $tabla.find('tbody tr').css('display','table-row'); } catch(e) {}

                // diagnostics removed

                // Helper to escape HTML
                function esc(s){ if(s===null||s===undefined) return ''; return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\"/g,'&quot;'); }

                var dataRows = rawRows.map(function(incidencia, index){
                    var actions = '<div class="btn-group">' +
                                    '<button class="btn btn-warning btnEditarIncidencia" idIncidencia="' + esc(incidencia.id) + '"><i class="fa fa-pencil"></i></button>' +
                                    '<button class="btn btn-danger btnEliminarIncidencia" idIncidencia="' + esc(incidencia.id) + '"><i class="fa fa-trash"></i></button>' +
                                  '</div>';
                    return [
                        (index + 1),
                        esc(incidencia.correlativo || ''),
                        esc(incidencia.nombre_incidencia || ''),
                        esc(incidencia.nombre_cliente || ''),
                        esc(incidencia.fecha || ''),
                        esc(incidencia.prioridad || ''),
                        esc(incidencia.observaciones || ''),
                        esc(incidencia.fecha_creacion || ''),
                        actions
                    ];
                });

                // Update via DataTables API if initialized
                try {
                    if ($.fn.DataTable.isDataTable('#tablaIncidencias')) {
                        var dt = $tabla.DataTable();
                        dt.clear();
                        if (dataRows.length) dt.rows.add(dataRows);
                        dt.draw(false);
                        console.log('cargarIncidencias: DataTable updated, rows=', dataRows.length);
                        // DataTable updated

                        // If DataTable reports rows but tbody is empty, try a safe retry to handle race conditions
                        var rendered = $tabla.find('tbody tr').length;
                        console.log('cargarIncidencias: rendered tbody rows=', rendered);
                        if (dataRows.length > 0 && rendered === 0) {
                            console.warn('cargarIncidencias: detected zero rendered rows after update — retrying reflow');
                            try {
                                dt.columns.adjust();
                                // small retry after paint
                                setTimeout(function(){
                                    try {
                                        dt.clear();
                                        dt.rows.add(dataRows);
                                        dt.draw(false);
                                        // after retry
                                        $tabla.find('tbody').promise().done(function(){
                                            console.log('cargarIncidencias: retry draw rendered tbody rows=', $tabla.find('tbody tr').length);
                                            // If still zero, force-rebuild the table DOM as last resort
                                            var renderedAfter = $tabla.find('tbody tr').length;
                                                if (renderedAfter === 0) {
                                                console.error('cargarIncidencias: rows still not rendered after retry — rebuilding table DOM');
                                                // sample rows suppressed in production
                                                // Build new table HTML
                                                var newTableHtml = '<table class="table table-bordered table-striped dt-responsive tabla" id="tablaIncidencias">' +
                                                    '<thead>' +
                                                    '<tr>' +
                                                    '<th>#</th><th>Correlativo</th><th>Nombre de la Incidencia</th><th>Nombre del Cliente</th><th>Fecha</th><th>Prioridad</th><th>Observaciones</th><th>Fecha Creación</th><th class="no-export">Acciones</th>' +
                                                    '</tr>' +
                                                    '</thead>' +
                                                    '<tbody></tbody>' +
                                                    '</table>';

                                                // Replace old table and re-populate
                                                $tabla.replaceWith(newTableHtml);
                                                $tabla = $('#tablaIncidencias');
                                                var $newTbody = $tabla.find('tbody');
                                                dataRows.forEach(function(r){
                                                    var tr = '<tr>' + r.map(function(cell){ return '<td>' + cell + '</td>'; }).join('') + '</tr>';
                                                    $newTbody.append(tr);
                                                });
                                                try {
                                                    $tabla.DataTable({
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
                                                } catch(e) { console.error('cargarIncidencias: reinit after rebuild failed', e); }
                                            }
                                        });
                                    } catch(e2) { console.error('cargarIncidencias: retry failed', e2); }
                                }, 80);
                            } catch(e2) { console.error('cargarIncidencias: reflow error', e2); }
                        }

                        return;
                    }
                } catch(e) { console.error('cargarIncidencias: update existing DataTable failed', e); }

                // Not initialized: render tbody and init DataTable if needed
                $tbody.empty();
                if (dataRows.length) {
                    dataRows.forEach(function(r){
                        var tr = '<tr>' + r.map(function(cell){ return '<td>' + cell + '</td>'; }).join('') + '</tr>';
                        $tbody.append(tr);
                    });

                    $tabla.DataTable({
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
                } else {
                    $tbody.append('<tr><td colspan="9" class="text-center">No hay incidencias registradas</td></tr>');
                }

                console.log('cargarIncidencias: rendered tbody rows=', $tabla.find('tbody tr').length);
                // rendered tbody rows counted
            },
            error: function(xhr, status, error) {
                console.error('cargarIncidencias: AJAX error', status, error, xhr && xhr.responseText);
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

    // Cargar incidencias inicialmente (al terminar inicialización de handlers)
    try { cargarIncidencias(); } catch(e) { console.error('cargarIncidencias inicial error', e); }

});
