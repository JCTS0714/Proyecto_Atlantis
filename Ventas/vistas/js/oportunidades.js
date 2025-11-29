function loadOportunidades(filters) {
    var dataParams = { action: 'getOportunidades', filtrarUltimaSemana: true };
    if (filters && typeof filters === 'object') {
        // map our filter keys to GET params
        ['nombre','telefono','documento','periodo','fecha_inicio','fecha_fin'].forEach(function(k){
            if (filters[k]) dataParams[k] = filters[k];
        });
    }

    $.ajax({
        url: 'ajax/oportunidades.ajax.php',
        method: 'GET',
        data: dataParams,
        dataType: 'json',
        success: function(data) {
            // Limpiar todas las columnas antes de agregar tarjetas
            const estados = ["1", "2", "3", "4"]; // Estados numéricos: 1:seguimiento, 2:calificado, 3:propuesto, 4:ganado
            estados.forEach(function(estado) {
                $('#' + estado).empty();
            });

            // Filtrar oportunidades: mostrar estados 1, 2, 3 en el kanban
            // Excluir estados 4 (Ganado), 5 (Zona de espera) y 6 (Perdido) del kanban
            const oportunidades = data.filter(function(oportunidad) {
                return oportunidad.estado !== "4" && oportunidad.estado !== "5" && oportunidad.estado !== "6"; 
            });
            
            oportunidades.forEach(function(oportunidad) {
                agregarTarjeta(oportunidad);
            });

            // Aplicar filtros si están activos (compatibilidad retro)
            if (window.filtrosActivos) {
                window.filtrarOportunidades(
                    window.filtrosActivos.estado || "",
                    window.filtrosActivos.periodo || "",
                    window.filtrosActivos.fechaInicio || "",
                    window.filtrosActivos.fechaFin || ""
                );
            }
        }
    });
}

function agregarTarjeta(oportunidad) {
    var estadoClase = "estado-" + oportunidad.estado; // Clase para el cuadro de estado
    var tarjeta = 
        '<div class="kanban-card panel-kanban" id="oportunidad-' + oportunidad.id + '" data-fecha-cierre="' + (oportunidad.fecha_cierre_estimada || '') + '" data-fecha-apertura="' + (oportunidad.fecha_apertura || oportunidad.fecha_creacion || oportunidad.fechaCreacion || '') + '" onclick="mostrarDetalles(' + oportunidad.id + ')" draggable="true" ondragstart="drag(event)">' +
            '<h5>' + oportunidad.titulo + '</h5>' + // Título de la oportunidad
            '<p>' + oportunidad.nombre_cliente + '</p>' + // Nombre del prospecto
        '</div>';
    $('#' + oportunidad.estado).append(tarjeta);
}

function crearOportunidad() {
    return new Promise(function(resolve, reject) {
        // Solo enviar los campos necesarios: título (rellenado por usuario) y cliente (seleccionado).
        // Empresa y teléfono se obtienen del cliente/prospecto y no son necesarios para la creación
        // Validaciones cliente-lado
        var tituloVal = $('#titulo').val().trim();
        if (!tituloVal) {
            alert('El título es obligatorio.');
            reject(new Error('Título vacío'));
            return;
        }

        var clienteIdVal = $('#cliente_id').val();
        var prospectFlag = $('#prospect_only').val() || '';
        // Si el modal fue abierto como "prospecto-only", exigir selección de cliente
        if (prospectFlag === '1' && (!clienteIdVal || clienteIdVal === '')) {
            alert('Debes seleccionar un cliente prospecto antes de crear la oportunidad.');
            reject(new Error('Cliente prospecto no seleccionado'));
            return;
        }

        var nuevaOportunidad = {
            nuevoTitulo: tituloVal,
            nuevaDescripcion: $('#descripcion').val() || '',
            nuevoValorEstimado: $('#valor_estimado').val() || 50,
            nuevaProbabilidad: $('#probabilidad').val() || 50,
            idCliente: clienteIdVal,
            idUsuario: $('#usuario_id').val(),
            nuevoEstado: '1', // Estado 1: seguimiento
            nuevaFechaCierre: $('#fecha_cierre').val() || '',
            prospect_only: prospectFlag
        };

        $.ajax({
            url: 'ajax/oportunidades.ajax.php',
            method: 'POST',
            data: Object.assign({ action: 'crearOportunidad' }, nuevaOportunidad),
            dataType: 'json',
            success: function(response) {
                if (response.status === "success") {
                    $('#modal-nueva-oportunidad').modal('hide');
                    loadOportunidades();
                    resolve();
                } else {
                    alert(response.message);
                    reject(new Error(response.message));
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert("Error en la solicitud AJAX: " + textStatus);
                reject(new Error(textStatus));
            }
        });
    });
}

function cambiarEstado(id, nuevoEstado) {
    $.ajax({
        url: 'ajax/oportunidades.ajax.php',
        method: 'POST',
        data: {
            action: 'cambiarEstado',
            idOportunidad: id,
            nuevoEstado: nuevoEstado
        },
        dataType: 'json',
        success: function(respuesta) {
            if (respuesta && respuesta.status === "success") {
                // Si el nuevo estado es 5 (Perdido) o 6 (Zona de Espera), remover la tarjeta del kanban
                if (nuevoEstado == "5" || nuevoEstado == "6") {
                    $('#oportunidad-' + id).remove();

                    // Si estamos en la página de No Clientes, recargar la página completa
                    if (window.location.pathname.includes('NoClientes')) {
                        location.reload();
                    }
                } else {
                    // Mover la tarjeta al nuevo estado solo si la base de datos respondió OK
                    var tarjeta = $('#oportunidad-' + id);
                    if (tarjeta.length) {
                        tarjeta.detach();
                        $('#' + nuevoEstado).append(tarjeta);
                    }
                }
            } else {
                // Mostrar mensaje de error específico si está disponible
                var mensajeError = respuesta && respuesta.message ? respuesta.message : "No se pudo actualizar el estado en la base de datos.";
                console.log("Error en cambiarEstado:", mensajeError);
                Swal.fire({
                    title: "Error",
                    text: mensajeError,
                    icon: "error",
                    confirmButtonText: "Cerrar"
                });
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            // Intentar parsear la respuesta como JSON si está disponible
            let errorMessage = "No se pudo actualizar el estado.";

            try {
                if (jqXHR.responseText) {
                    const response = JSON.parse(jqXHR.responseText);
                    if (response && response.message) {
                        errorMessage = response.message;
                    }
                }
            } catch (e) {
                // Si hay contenido en responseText pero no es JSON válido, podría ser un error PHP
                if (jqXHR.responseText && jqXHR.responseText.includes('Warning') || jqXHR.responseText.includes('Error')) {
                    errorMessage = "Error interno del servidor. Verifique la consola para más detalles.";
                }
            }

            Swal.fire({
                title: "Error",
                text: errorMessage,
                icon: "error",
                confirmButtonText: "Cerrar"
            });
        }
    });
}

$(document).on('click', '.btn-cambiar-estado', function() {
    var id = $(this).data('id');
    var estado = $(this).data('estado');
    var estadoTexto = obtenerTextoEstado(estado);

    // Confirmación especial para estado "Perdido"
    if (estado == "5") {
        Swal.fire({
            title: "¿Marcar como Perdido?",
            text: "¿Estás seguro de marcar esta oportunidad como Perdida? Esta acción no se puede deshacer.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Sí, marcar como Perdido",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                cambiarEstado(id, estado);
                // Cerrar el modal después de confirmar
                $('#modal-detalles-oportunidad').modal('hide');
            }
        });
    } else {
        cambiarEstado(id, estado);
        // Cerrar el modal para otros estados
        $('#modal-detalles-oportunidad').modal('hide');
    }
});

function obtenerTextoEstado(estado) {
    var estados = {
        "2": "Calificado",
        "3": "Propuesto", 
        "4": "Ganado",
        "5": "Perdido",
        "6": "Zona de Espera"
    };
    return estados[estado] || "Estado " + estado;
}

$(document).on('click', '.btn-eliminar-oportunidad', function() {
    var id = $(this).data('id');
    eliminarOportunidad(id);
});

$(document).on('click', '.btn-zona-espera', function() {
    var id = $(this).data('id');
    moverAZonaEspera(id);
});

// React to Advanced Search events
window.addEventListener('advancedSearch:apply', function(e) {
    try {
        var filters = e.detail || {};
        loadOportunidades(filters);
    } catch(err) {
        console.error('Error applying advanced search filters:', err);
    }
});

window.addEventListener('advancedSearch:clear', function() {
    loadOportunidades(); // reload default
});

function moverAZonaEspera(id) {
    Swal.fire({
        title: "¿Mover a Zona de Espera?",
        text: "¿Estás seguro de mover esta oportunidad a la zona de espera? Esta acción no se puede deshacer.",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#ffc107",
        cancelButtonColor: "#6c757d",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, mover a zona de espera"
    }).then((result) => {
        if (result.isConfirmed) {
            // Llamada AJAX para cambiar estado y luego redirigir al listado zona-espera
            $.ajax({
                url: 'ajax/oportunidades.ajax.php',
                method: 'POST',
                data: { action: 'cambiarEstado', idOportunidad: id, nuevoEstado: '6' },
                dataType: 'json',
                success: function(respuesta) {
                    if (respuesta && respuesta.status === 'success') {
                        // Cerrar el modal de detalles
                        $('#modal-detalles-oportunidad').modal('hide');

                        // Obtener la oportunidad para conocer el cliente asociado
                        $.ajax({
                            url: 'ajax/oportunidades.ajax.php',
                            method: 'GET',
                            data: { action: 'getOportunidad', id: id },
                            dataType: 'json',
                            success: function(oportunidad) {
                                // ctrMostrarOportunidades devuelve un array
                                var clienteId = null;
                                if (Array.isArray(oportunidad) && oportunidad.length > 0) {
                                    clienteId = oportunidad[0].cliente_id || oportunidad[0].clienteId || null;
                                } else if (oportunidad && oportunidad.cliente_id) {
                                    clienteId = oportunidad.cliente_id;
                                }

                                var base = (typeof window.BASE_URL !== 'undefined') ? window.BASE_URL : '';
                                if (clienteId) {
                                    // Redirigir a zona-espera indicando que abra modal motivo para ese cliente
                                    window.location.href = base + '/zona-espera?open_motivo_id=' + encodeURIComponent(clienteId);
                                } else {
                                    // Si no se pudo obtener cliente, mostrar el modal de últimos
                                    setTimeout(function() { mostrarUltimosEnZonaEspera(); }, 300);
                                }
                            },
                            error: function() {
                                setTimeout(function() { mostrarUltimosEnZonaEspera(); }, 300);
                            }
                        });
                    } else {
                        var msg = respuesta && respuesta.message ? respuesta.message : 'No se pudo mover a zona de espera';
                        Swal.fire('Error', msg, 'error');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire('Error', 'No se pudo cambiar el estado. Intente nuevamente.', 'error');
                }
            });
        }
    });
}

function eliminarOportunidad(id) {
    Swal.fire({
        title: "¿Estás seguro de eliminar la tarjeta?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sí, eliminar"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/oportunidades.ajax.php',
                method: 'POST',
                data: { action: 'eliminarOportunidad', id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.status === "success") {
                        loadOportunidades();
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: response.message,
                            confirmButtonText: "Cerrar"
                        });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert("Error al eliminar oportunidad: " + textStatus);
                }
            });
        }
    });
}

function mostrarUltimosEnZonaEspera() {
    $.ajax({
        url: 'ajax/oportunidades.ajax.php',
        method: 'GET',
        data: { action: 'getUltimosZonaEspera' },
        dataType: 'json',
        success: function(data) {
            // Limpiar el contenido del modal
            $('#modal-zona-espera-lista').empty();

            if (data.length === 0) {
                $('#modal-zona-espera-lista').html('<p>No hay prospectos en zona de espera</p>');
            } else {
                // Crear lista de los últimos 10 prospectos
                data.forEach(function(oportunidad, index) {
                    var item = '<div class="list-group-item">' +
                               '<h6 class="mb-1">' + (index + 1) + '. ' + (oportunidad.titulo || 'Sin título') + '</h6>' +
                               '<small class="text-muted">Cliente: ' + (oportunidad.nombre_cliente || 'N/A') + '</small>' +
                               '<br><small class="text-muted">Fecha: ' + (oportunidad.fecha_modificacion || oportunidad.fecha_apertura || 'N/A') + '</small>' +
                               '</div>';
                    $('#modal-zona-espera-lista').append(item);
                });
            }

            // Mostrar el modal
            $('#modal-zona-espera').modal('show');
        },
        error: function(jqXHR, textStatus, errorThrown) {
            // Mostrar modal vacío en caso de error
            $('#modal-zona-espera-lista').html('<p>Error al cargar los datos. Verifique la consola para más detalles.</p>');
            $('#modal-zona-espera').modal('show');
        }
    });
}

function loadClientes() {
    $.ajax({
        url: 'ajax/oportunidades.ajax.php',
        method: 'GET',
        data: { action: 'getClientes' },
        dataType: 'json',
        success: function(data) {
            var clientes = data;
            clientes.forEach(function(cliente) {
                $('#cliente_id').append(new Option(cliente.nombre, cliente.id));
            });
        },
        error: function(jqXHR, textStatus, errorThrown) {
            // console.error("Error en AJAX getClientes:", textStatus, errorThrown);
        }
    });
}

// Función para filtrar oportunidades según los filtros aplicados
window.filtrarOportunidades = function(filtroEstado, filtroPeriodo, fechaInicio, fechaFin) {
    // Guardar filtros activos globalmente para reaplicar después de recarga
    window.filtrosActivos = {
        estado: filtroEstado,
        periodo: filtroPeriodo,
        fechaInicio: fechaInicio,
        fechaFin: fechaFin
    };

    // Convertir filtroEstado a string para comparación
    filtroEstado = filtroEstado ? filtroEstado.toString() : "";

    // Filtrar columnas Kanban según filtroEstado
    $('.kanban-column').each(function() {
        var columnaId = $(this).attr('id');
        if (filtroEstado && columnaId !== filtroEstado) {
            $(this).hide();
        } else {
            $(this).show();
        }
    });

    // Filtrar tarjetas dentro de columnas según periodo y fechas
    $('.panel-kanban').each(function() {
        var fechaCierre = $(this).data('fecha-cierre');
        var mostrar = true;

            if (filtroPeriodo && filtroPeriodo !== '') {
                // Normalizar fechas (ignorar horas)
                function parseDateYMD(d) {
                    if (!d) return null;
                    // d expected format YYYY-MM-DD
                    var parts = String(d).split('-');
                    if (parts.length !== 3) return new Date(d);
                    return new Date(parseInt(parts[0],10), parseInt(parts[1],10)-1, parseInt(parts[2],10));
                }

                // Priorizar fecha_apertura (creación) sobre fecha_cierre_estimada
                var fechaAperturaAttr = $(this).data('fecha-apertura');
                var fechaCierreAttr = $(this).data('fecha-cierre');
                var fechaAperturaParsed = parseDateYMD(fechaAperturaAttr);
                var fechaCierreParsed = parseDateYMD(fechaCierreAttr);
                var fecha = fechaAperturaParsed || fechaCierreParsed; // el día que representa la tarjeta

                var hoy = new Date();
                var hoyCero = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate());

                switch (filtroPeriodo) {
                    case 'diario':
                        if (!fecha) { mostrar = false; }
                        else { mostrar = fecha.getTime() === hoyCero.getTime(); }
                        break;

                    case 'semanal':
                        if (!fecha) { mostrar = false; }
                        else {
                            // Rango desde hoy (inclusive) hasta 7 días hacia adelante
                            var finSemana = new Date(hoyCero);
                            finSemana.setDate(hoyCero.getDate() + 7);
                            mostrar = fecha.getTime() >= hoyCero.getTime() && fecha.getTime() <= finSemana.getTime();
                        }
                        break;

                    case 'mensual':
                        if (!fecha) { mostrar = false; }
                        else { mostrar = fecha.getMonth() === hoyCero.getMonth() && fecha.getFullYear() === hoyCero.getFullYear(); }
                        break;

                    case 'personalizado':
                        if (fechaInicio && fechaFin) {
                            var inicio = parseDateYMD(fechaInicio);
                            var fin = parseDateYMD(fechaFin);
                            if (inicio && fin && fecha) {
                                // incluir límites
                                mostrar = fecha.getTime() >= inicio.getTime() && fecha.getTime() <= fin.getTime();
                            } else {
                                mostrar = false;
                            }
                        } else {
                            mostrar = false;
                        }
                        break;

                    default:
                        mostrar = true;
                }
            }

        if (mostrar) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
};

// ============================================
// FUNCIONES DRAG AND DROP PARA KANBAN
// ============================================

function drag(event) {
    // Guardar el ID de la oportunidad y el estado actual en dataTransfer
    const card = event.target.closest('.kanban-card');
    if (card) {
        const oportunidadId = card.id.replace('oportunidad-', '');
        const estadoActual = card.closest('.kanban-column').id;

        event.dataTransfer.setData('text/plain', JSON.stringify({
            id: oportunidadId,
            estadoActual: estadoActual
        }));

        // Agregar clase visual durante el drag
        card.classList.add('dragging');
    }
}

function allowDrop(event) {
    event.preventDefault();
    // Agregar clase visual para indicar que se puede soltar
    event.currentTarget.classList.add('drop-target');
}

function drop(event) {
    event.preventDefault();

    // Remover clase visual
    event.currentTarget.classList.remove('drop-target');

    try {
        const data = JSON.parse(event.dataTransfer.getData('text/plain'));
        const oportunidadId = data.id;
        const estadoActual = data.estadoActual;
        const nuevoEstado = event.currentTarget.id;

        // Solo cambiar estado si es diferente
        if (estadoActual !== nuevoEstado) {
            // Llamar a la función existente para cambiar estado
            cambiarEstado(oportunidadId, nuevoEstado);
        }

        // Remover clase visual del elemento que se estaba arrastrando
        const draggingElement = document.querySelector('.kanban-card.dragging');
        if (draggingElement) {
            draggingElement.classList.remove('dragging');
        }

    } catch (error) {
        // console.error("Error en drop event:", error);
        Swal.fire({
            title: "Error",
            text: "No se pudo procesar la acción de arrastrar y soltar.",
            icon: "error",
            confirmButtonText: "Cerrar"
        });
    }
}

// Remover clases visuales cuando se sale del área de drop
document.addEventListener('dragleave', function(event) {
    if (event.target.classList.contains('kanban-column')) {
        event.target.classList.remove('drop-target');
    }
});

// Remover clase dragging cuando termina el drag
document.addEventListener('dragend', function(event) {
    const draggingElement = document.querySelector('.kanban-card.dragging');
    if (draggingElement) {
        draggingElement.classList.remove('dragging');
    }
});

function mostrarDetalles(id) {
    // Verificar si el modal existe
    if ($('#modal-detalles-oportunidad').length === 0) {
        alert("Error: El modal de detalles no está disponible.");
        return;
    }

    // Verificar si el contenedor de contenido existe
    if ($('#detalles-oportunidad-contenido').length === 0) {
        alert("Error: El contenedor de detalles no está disponible.");
        return;
    }

    $.ajax({
        url: 'ajax/oportunidades.ajax.php',
        method: 'GET',
        data: { action: 'getOportunidad', id: id },
        dataType: 'json',
        success: function(data) {
            if (data && Array.isArray(data) && data.length > 0) {
                var oportunidad = data[0];

                // Determinar en qué columna está la tarjeta para pasar el nombre de columna al modal
                try {
                    var tarjeta = $('#oportunidad-' + oportunidad.id);
                    var parentId = tarjeta.parent().attr('id') || '';
                    var estadoMap = {
                        '1': 'seguimiento',
                        '2': 'calificado',
                        '3': 'propuesto',
                        '4': 'ganado',
                        '5': 'perdido',
                        '6': 'zona de espera'
                    };
                    var columnaNombre = estadoMap[parentId] || parentId || '';
                    // Guardar tanto data-attribute como propiedad jQuery para compatibilidad
                    if (columnaNombre) {
                        $('#modal-detalles-oportunidad').attr('data-column', columnaNombre).data('column', columnaNombre);
                        // También dejar en el contenedor de contenido por si otro flujo lo lee
                        $('#detalles-oportunidad-contenido').attr('data-column-name', columnaNombre).data('column-name', columnaNombre);
                    } else {
                        $('#modal-detalles-oportunidad').removeAttr('data-column').removeData('column');
                        $('#detalles-oportunidad-contenido').removeAttr('data-column-name').removeData('column-name');
                    }
                } catch (e) {
                    console.log('No se pudo determinar la columna de la tarjeta:', e);
                }

                    // Si la plantilla del modal contiene elementos estáticos (los añadidos en PHP),
                    // rellenarlos en lugar de sobrescribir el HTML completo.
                    if ($('#det-titulo').length && $('#det-cliente').length) {
                        $('#det-titulo').text(oportunidad.titulo || '-');
                        $('#det-cliente').text(oportunidad.nombre_cliente || '-');
                        $('#det-descripcion').text(oportunidad.descripcion || '-');
                        $('#det-fecha').text(oportunidad.fecha_cierre_estimada || '-');

                        // Intentar obtener teléfono/empresa desde la respuesta de oportunidad
                        var telefono = oportunidad.telefono || oportunidad.telefono_cliente || oportunidad.cliente_telefono || '';
                        var empresa = oportunidad.empresa || oportunidad.empresa_cliente || '';

                        // Inyectar inputs editables (deshabilitados) junto a los elementos estáticos
                        if ($('#modal-cliente-nombre').length === 0) {
                            // Añadir input para nombre del cliente
                            $('#det-cliente').after('<input type="text" class="form-control cliente-field" id="modal-cliente-nombre" name="cliente_nombre" value="' + (oportunidad.nombre_cliente || '') + '" disabled />' +
                                                     '<div class="form-check mt-1"><input class="form-check-input toggle-edit-cliente" type="checkbox" value="1" id="toggle-edit-nombre" data-field="nombre"><label class="form-check-label" for="toggle-edit-nombre">Editar cliente</label></div>');
                        } else {
                            $('#modal-cliente-nombre').val(oportunidad.nombre_cliente || '');
                        }

                        if ($('#modal-cliente-telefono').length === 0) {
                            $('#det-telefono').after('<input type="text" class="form-control cliente-field" id="modal-cliente-telefono" name="cliente_telefono" value="' + (telefono || '') + '" disabled />' +
                                                      '<div class="form-check mt-1"><input class="form-check-input toggle-edit-cliente" type="checkbox" value="1" id="toggle-edit-telefono" data-field="telefono"><label class="form-check-label" for="toggle-edit-telefono">Editar teléfono</label></div>');
                        } else {
                            $('#modal-cliente-telefono').val(telefono || '');
                        }

                        if ($('#modal-cliente-empresa').length === 0) {
                            $('#det-empresa').after('<input type="text" class="form-control cliente-field" id="modal-cliente-empresa" name="cliente_empresa" value="' + (empresa || '') + '" disabled />' +
                                                     '<div class="form-check mt-1"><input class="form-check-input toggle-edit-cliente" type="checkbox" value="1" id="toggle-edit-empresa" data-field="empresa"><label class="form-check-label" for="toggle-edit-empresa">Editar empresa</label></div>');
                        } else {
                            $('#modal-cliente-empresa').val(empresa || '');
                        }

                        // Si no hay teléfono o empresa en la respuesta, pedirlos al endpoint de clientes
                        if ((!telefono || !empresa) && oportunidad.cliente_id) {
                            $.ajax({
                                url: 'ajax/clientes.ajax.php',
                                method: 'POST',
                                data: { idCliente: oportunidad.cliente_id },
                                dataType: 'json',
                                success: function(clienteData) {
                                    if (clienteData) {
                                        var tel = clienteData.telefono || clienteData['telefono'] || telefono || '-';
                                        var emp = clienteData.empresa || clienteData['empresa'] || empresa || '-';
                                        var nombre = clienteData.nombre || clienteData['nombre'] || oportunidad.nombre_cliente || '-';
                                        $('#det-telefono').text(tel || '-');
                                        $('#det-empresa').text(emp || '-');
                                        $('#modal-cliente-telefono').val(tel || '');
                                        $('#modal-cliente-empresa').val(emp || '');
                                        $('#modal-cliente-nombre').val(nombre || '');
                                    } else {
                                        $('#det-telefono').text(telefono || '-');
                                        $('#det-empresa').text(empresa || '-');
                                    }
                                },
                                error: function() {
                                    $('#det-telefono').text(telefono || '-');
                                    $('#det-empresa').text(empresa || '-');
                                }
                            });
                        } else {
                            $('#det-telefono').text(telefono || '-');
                            $('#det-empresa').text(empresa || '-');
                            // Asegurar que los inputs también muestren los valores
                            $('#modal-cliente-telefono').val(telefono || '');
                            $('#modal-cliente-empresa').val(empresa || '');
                        }

                        // Registrar el handler de toggles (asegurar cobertura)
                        $(document).off('change', '.toggle-edit-cliente').on('change', '.toggle-edit-cliente', function() {
                            var field = $(this).data('field');
                            var $input = $('#modal-cliente-' + field);
                            if ($(this).is(':checked')) {
                                $input.prop('disabled', false).focus();
                                // añadir clase persistente para indicar estado editable
                                $input.addClass('cliente-field-active');
                                // añadir pulso temporal para llamar la atención
                                $input.addClass('cliente-field-pulse');
                                setTimeout(function() { $input.removeClass('cliente-field-pulse'); }, 1200);
                            } else {
                                $input.prop('disabled', true);
                                $input.removeClass('cliente-field-active cliente-field-pulse');
                            }
                        });

                        // Mostrar modal (sin formulario editable por defecto)
                        $('#modal-detalles-oportunidad').modal('show');

                        // Deshabilitar guardado desde este modal ya que ahora es de sólo lectura (si así lo deseas)
                        return;
                    }

                // Comportamiento previo: si no existen los elementos estáticos, inyectar el formulario editable
                var contenido =
                    '<form id="form-detalles-oportunidad">' +
                    '<div class="row">' +
                        '<div class="col-md-6">' +
                            '<h6>Título: <span style="color: red;">*</span></h6>' +
                            '<input type="text" class="form-control" id="modal-titulo" name="titulo" value="' + (oportunidad.titulo || '') + '" />' +
                        '</div>' +
                        '<div class="col-md-6">' +
                                '<h6>Cliente:</h6>' +
                                '<input type="text" spellcheck="false" autocomplete="off" class="form-control cliente-field" id="modal-cliente-nombre" name="cliente_nombre" value="' + (oportunidad.nombre_cliente || '') + '" disabled />' +
                                '<div class="form-check mt-1">' +
                                    '<input class="form-check-input toggle-edit-cliente" type="checkbox" value="1" id="toggle-edit-nombre" data-field="nombre">' +
                                    '<label class="form-check-label" for="toggle-edit-nombre">Editar cliente</label>' +
                                '</div>' +
                                '<input type="hidden" id="modal-cliente-id" value="' + (oportunidad.cliente_id || '') + '" />' +
                            '</div>' +
                    '</div>' +
                    '<div class="row">' +
                        '<div class="col-md-6">' +
                            '<h6>Información adicional:</h6>' +
                            '<textarea class="form-control" id="modal-descripcion" name="descripcion">' + (oportunidad.descripcion || '') + '</textarea>' +
                        '</div>' +
                        '<div class="col-md-6">' +
                                '<h6>Teléfono:</h6>' +
                                // Mostrar input deshabilitado con checkbox para habilitar edición
                                '<input type="text" spellcheck="false" autocomplete="off" class="form-control cliente-field" id="modal-cliente-telefono" name="cliente_telefono" value="' + (oportunidad.telefono || '') + '" disabled />' +
                                '<div class="form-check mt-1">' +
                                    '<input class="form-check-input toggle-edit-cliente" type="checkbox" value="1" id="toggle-edit-telefono" data-field="telefono">' +
                                    '<label class="form-check-label" for="toggle-edit-telefono">Editar teléfono</label>' +
                                '</div>' +
                                '<input type="hidden" id="modal-valor_estimado" name="valor_estimado" value="' + (oportunidad.valor_estimado || '') + '" />' +
                            '</div>' +
                    '</div>' +
                    '<div class="row">' +
                        '<div class="col-md-6">' +
                            '<h6>Empresa:</h6>' +
                            '<input type="text" spellcheck="false" autocomplete="off" class="form-control cliente-field" id="modal-cliente-empresa" name="cliente_empresa" value="' + (oportunidad.empresa || '') + '" disabled />' +
                            '<div class="form-check mt-1">' +
                                '<input class="form-check-input toggle-edit-cliente" type="checkbox" value="1" id="toggle-edit-empresa" data-field="empresa">' +
                                '<label class="form-check-label" for="toggle-edit-empresa">Editar empresa</label>' +
                            '</div>' +
                            '<input type="hidden" id="modal-probabilidad" name="probabilidad" value="' + (oportunidad.probabilidad || '') + '" />' +
                        '</div>' +
                        '<div class="col-md-6">' +
                            '<h6>Fecha de Cierre Estimada (Estimado): <span style="color: red;">*</span></h6>' +
                            '<input type="date" class="form-control" id="modal-fecha_cierre_estimada" name="fecha_cierre_estimada" value="' + (oportunidad.fecha_cierre_estimada || '') + '" />' +
                        '</div>' +
                    '</div>' +
                    '<div class="row">' +
                        '<div class="col-md-6">' +
                            '<h6>Actividad:</h6>' +
                            '<select class="form-control" id="modal-actividad" name="actividad">' +
                                '<option value="">Seleccionar actividad...</option>' +
                                '<option value="mensaje de whatsapp"' + ((oportunidad.actividad === 'mensaje de whatsapp') ? ' selected' : '') + '>Mensaje de WhatsApp</option>' +
                                '<option value="llamada"' + ((oportunidad.actividad === 'llamada') ? ' selected' : '') + '>Llamada</option>' +
                                '<option value="reunion"' + ((oportunidad.actividad === 'reunion') ? ' selected' : '') + '>Reunión</option>' +
                                '<option value="seguimiento de presupuesto"' + ((oportunidad.actividad === 'seguimiento de presupuesto') ? ' selected' : '') + '>Seguimiento de Presupuesto</option>' +
                                '<option value="ofertar"' + ((oportunidad.actividad === 'ofertar') ? ' selected' : '') + '>Ofertar</option>' +
                                '<option value="llamada para demostracion"' + ((oportunidad.actividad === 'llamada para demostracion') ? ' selected' : '') + '>Llamada para Demostración</option>' +
                                '<option value="otros"' + ((oportunidad.actividad === 'otros') ? ' selected' : '') + '>Otros</option>' +
                            '</select>' +
                        '</div>' +
                        '<div class="col-md-6">' +
                            '<h6>Fecha de Actividad:</h6>' +
                            '<input type="date" class="form-control" id="modal-fecha_actividad" name="fecha_actividad" value="' + (oportunidad.fecha_actividad || '') + '" />' +
                        '</div>' +
                    '</div>' +
                    '<input type="hidden" id="modal-id_oportunidad" name="id_oportunidad" value="' + oportunidad.id + '" />' +
                    '<div class="row mt-3">' +
                        '<div class="col-md-12 text-right">' +
                            '<button type="button" class="btn btn-primary" id="btn-guardar-cambios">Guardar Cambios</button>' +
                        '</div>' +
                    '</div>' +
                    '<div class="row mt-2">' +
                        '<div class="col-md-12">' +
                            '<h6>Acciones:</h6>' +
                            '<button type="button" class="btn btn-danger btn-cambiar-estado" data-id="' + oportunidad.id + '" data-estado="5">Perdido</button>' +
                            '<button type="button" class="btn btn-warning btn-zona-espera" data-id="' + oportunidad.id + '">Zona de Espera</button>' +
                            '<button type="button" class="btn btn-danger btn-eliminar-oportunidad" data-id="' + oportunidad.id + '">Eliminar</button>' +
                        '</div>' +
                    '</div>' +
                    '</form>';

                $('#detalles-oportunidad-contenido').html(contenido);

                // Handler: toggle para habilitar edición de campos de cliente
                $(document).off('change', '.toggle-edit-cliente').on('change', '.toggle-edit-cliente', function() {
                    var field = $(this).data('field');
                    var $input = $('#modal-cliente-' + field);
                    if ($(this).is(':checked')) {
                        $input.prop('disabled', false).focus();
                        // persistente + pulso temporal
                        $input.addClass('cliente-field-active cliente-field-pulse');
                        setTimeout(function() { $input.removeClass('cliente-field-pulse'); }, 1200);
                    } else {
                        $input.prop('disabled', true);
                        $input.removeClass('cliente-field-active cliente-field-pulse');
                    }
                });

                // Obtener datos de teléfono/empresa a partir de la respuesta de oportunidad
                var telefono = oportunidad.telefono || oportunidad.telefono_cliente || oportunidad.cliente_telefono || '';
                var empresa = oportunidad.empresa || oportunidad.empresa_cliente || '';

                // Si no hay teléfono/empresa en la respuesta, pedirlos al endpoint de clientes
                if ((!telefono || !empresa) && oportunidad.cliente_id) {
                    $.ajax({
                        url: 'ajax/clientes.ajax.php',
                        method: 'POST',
                        data: { idCliente: oportunidad.cliente_id },
                        dataType: 'json',
                        success: function(clienteData) {
                            var tel = clienteData && (clienteData.telefono || clienteData['telefono'] || clienteData.celular) ? (clienteData.telefono || clienteData['telefono'] || clienteData.celular) : telefono || '';
                            var emp = clienteData && (clienteData.empresa || clienteData['empresa'] || clienteData.empresa_nombre) ? (clienteData.empresa || clienteData['empresa'] || clienteData.empresa_nombre) : empresa || '';
                            // Poner en los <p> visibles y en los inputs editables (si existen)
                            $('#display-telefono').text(tel || '');
                            $('#display-empresa').text(emp || '');
                            $('#modal-cliente-telefono').val(tel || '');
                            $('#modal-cliente-empresa').val(emp || '');
                            $('#modal-cliente-nombre').val(clienteData.nombre || clienteData['nombre'] || oportunidad.nombre_cliente || '');
                        },
                        error: function() {
                            // Rellenar con lo que tengamos (posiblemente vacío)
                            $('#display-telefono').text(telefono || '');
                            $('#display-empresa').text(empresa || '');
                        }
                    });
                } else {
                    // Si ya tenemos los datos, colocarlos directamente y poblar los inputs editables
                    $('#display-telefono').text(telefono || '');
                    $('#modal-cliente-telefono').val(telefono || '');
                    $('#display-empresa').text(empresa || '');
                    $('#modal-cliente-empresa').val(empresa || '');
                }

                $('#modal-detalles-oportunidad').modal('show');

                $('#btn-guardar-cambios').off('click').on('click', function() {
                    actualizarOportunidad();
                });

            } else if (data && data.status === 'error') {
                alert("Error: " + data.message);
            } else {
                alert("No se pudieron cargar los detalles de la oportunidad.");
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert("Error al cargar los detalles de la oportunidad. Verifica la consola para más detalles.");
        }
    });
}

function actualizarOportunidad() {
    // Saltar la verificación de visibilidad del modal para evitar error falso
    setTimeout(function() {

        if ($('#modal-titulo').length === 0) {
            alert("Los campos del formulario no están disponibles. Por favor, abre los detalles de la oportunidad nuevamente.");
            return;
        }

        var tituloVal = $('#modal-titulo').val();

        var actividadVal = $('#modal-actividad').val();
        var fechaActividadVal = $('#modal-fecha_actividad').val();

        var datos = {
            action: 'actualizarOportunidad',
            id: $('#modal-id_oportunidad').val(),
            titulo: tituloVal,
            descripcion: $('#modal-descripcion').val(),
            valor_estimado: $('#modal-valor_estimado').val(),
            probabilidad: $('#modal-probabilidad').val(),
            fecha_cierre_estimada: $('#modal-fecha_cierre_estimada').val(),
            actividad: actividadVal,
            fecha_actividad: fechaActividadVal,
            cliente_id: $('#modal-cliente-id').val(),
            usuario_id: $('#modal-detalles-oportunidad').data('usuario-id')
        };

        // Recolectar cambios en campos de cliente (solo los campos cuyos checkbox estén activados)
        var idCliente = $('#modal-cliente-id').val();
        var clientUpdates = {};
        $('.toggle-edit-cliente:checked').each(function() {
            var field = $(this).data('field');
            var val = $('#modal-cliente-' + field).val();
            if (typeof val !== 'undefined') {
                clientUpdates[field] = val;
            }
        });

        // Función que realiza la actualización de oportunidad (se ejecuta después de actualizar cliente si aplica)
        function doUpdateOportunidad() {
            $.ajax({
                url: 'ajax/oportunidades.ajax.php',
                method: 'POST',
                data: datos,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        var mensaje = 'Oportunidad actualizada correctamente.';
                        var redirigirCalendario = false;

                        // Verificar si ambos campos están llenos para redirigir al calendario
                        if (actividadVal && fechaActividadVal) {
                            // Verificar si ya existe una reunión para este cliente, actividad y fecha
                            $.ajax({
                                url: 'ajax/oportunidades.ajax.php',
                                method: 'GET',
                                data: {
                                    action: 'verificarReunionExistente',
                                    cliente_id: datos.cliente_id,
                                    titulo: datos.actividad,
                                    fecha: datos.fecha_actividad
                                },
                                dataType: 'json',
                                success: function(response) {
                                    if (response.status === 'success') {
                                        if (response.existe) {
                                            mensaje += ' Ya existe una reunión programada para esta actividad y fecha.';
                                            redirigirCalendario = false;
                                        } else {
                                            mensaje += ' Redirigiendo al calendario para crear la reunión.';
                                            redirigirCalendario = true;
                                        }
                                    } else {
                                        mensaje += ' Error al verificar reuniones existentes.';
                                        redirigirCalendario = false;
                                    }

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Éxito',
                                        text: mensaje,
                                        confirmButtonText: 'Aceptar'
                                    }).then(() => {
                                        $('#modal-detalles-oportunidad').modal('hide');
                                        loadOportunidades();

                                        // Solo redirigir si ambos campos están llenos y no existe reunión
                                        if (redirigirCalendario) {
                                            var url = 'calendario?cliente_id=' + encodeURIComponent(datos.cliente_id) +
                                                      '&titulo=' + encodeURIComponent(datos.actividad) +
                                                      '&fecha=' + encodeURIComponent(datos.fecha_actividad);
                                            window.location.href = url;
                                        }
                                    });
                                },
                                error: function(jqXHR, textStatus, errorThrown) {
                                    mensaje += ' Error al verificar reuniones existentes.';
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Éxito',
                                        text: mensaje,
                                        confirmButtonText: 'Aceptar'
                                    }).then(() => {
                                        $('#modal-detalles-oportunidad').modal('hide');
                                        loadOportunidades();
                                    });
                                }
                            });
                        } else {
                            if (actividadVal && !fechaActividadVal) {
                                mensaje += ' Advertencia: Seleccionaste una actividad pero no especificaste la fecha.';
                            } else if (!actividadVal && fechaActividadVal) {
                                mensaje += ' Advertencia: Especificaste una fecha pero no seleccionaste actividad.';
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: mensaje,
                                confirmButtonText: 'Aceptar'
                            }).then(() => {
                                $('#modal-detalles-oportunidad').modal('hide');
                                loadOportunidades();
                            });
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al actualizar la oportunidad: ' + response.message,
                            confirmButtonText: 'Aceptar'
                        });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Error en la solicitud AJAX: ' + textStatus);
                }
            });
        }

        // Si hay cambios de cliente, enviarlos primero
        if (idCliente && Object.keys(clientUpdates).length > 0) {
            var payload = { action: 'actualizarCampos', idCliente: idCliente };
            // Adjuntar campos al payload
            for (var k in clientUpdates) { payload[k] = clientUpdates[k]; }

            $.ajax({
                url: 'ajax/clientes.ajax.php',
                method: 'POST',
                data: payload,
                dataType: 'json',
                success: function(resp) {
                    if (resp && resp.status === 'ok') {
                        // Continuar con la actualización de la oportunidad
                        doUpdateOportunidad();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al actualizar cliente',
                            text: (resp && resp.message) ? resp.message : 'No se pudieron guardar los cambios del cliente.',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error en la solicitud para actualizar cliente.',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        } else {
            // No hay cambios de cliente, proceder directamente
            doUpdateOportunidad();
        }

        // Validar campos obligatorios antes de enviar
        if (!tituloVal || tituloVal.trim() === "") {
            alert("El campo Título es obligatorio.");
            return;
        }
        // Validación numérica para 'probabilidad' (porcentaje 0-100)
        if (datos.probabilidad === '' || isNaN(datos.probabilidad) || Number(datos.probabilidad) < 0 || Number(datos.probabilidad) > 100) {
            alert("El campo Probabilidad debe ser un número entre 0 y 100.");
            return;
        }
        if (!datos.fecha_cierre_estimada) {
            alert("El campo Fecha de Cierre Estimada es obligatorio.");
            return;
        }

        $.ajax({
            url: 'ajax/oportunidades.ajax.php',
            method: 'POST',
            data: datos,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var mensaje = 'Oportunidad actualizada correctamente.';
                    var redirigirCalendario = false;

                    // Verificar si ambos campos están llenos para redirigir al calendario
                    if (actividadVal && fechaActividadVal) {
                        // Verificar si ya existe una reunión para este cliente, actividad y fecha
                        $.ajax({
                            url: 'ajax/oportunidades.ajax.php',
                            method: 'GET',
                            data: {
                                action: 'verificarReunionExistente',
                                cliente_id: datos.cliente_id,
                                titulo: datos.actividad,
                                fecha: datos.fecha_actividad
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.status === 'success') {
                                    if (response.existe) {
                                        mensaje += ' Ya existe una reunión programada para esta actividad y fecha.';
                                        redirigirCalendario = false;
                                    } else {
                                        mensaje += ' Redirigiendo al calendario para crear la reunión.';
                                        redirigirCalendario = true;
                                    }
                                } else {
                                    mensaje += ' Error al verificar reuniones existentes.';
                                    redirigirCalendario = false;
                                }

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Éxito',
                                    text: mensaje,
                                    confirmButtonText: 'Aceptar'
                                }).then(() => {
                                    $('#modal-detalles-oportunidad').modal('hide');
                                    loadOportunidades();

                                    // Solo redirigir si ambos campos están llenos y no existe reunión
                                    if (redirigirCalendario) {
                                        var url = 'calendario?cliente_id=' + encodeURIComponent(datos.cliente_id) +
                                                  '&titulo=' + encodeURIComponent(datos.actividad) +
                                                  '&fecha=' + encodeURIComponent(datos.fecha_actividad);
                                        window.location.href = url;
                                    }
                                });
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                mensaje += ' Error al verificar reuniones existentes.';
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Éxito',
                                    text: mensaje,
                                    confirmButtonText: 'Aceptar'
                                }).then(() => {
                                    $('#modal-detalles-oportunidad').modal('hide');
                                    loadOportunidades();
                                });
                            }
                        });
                    } else {
                        if (actividadVal && !fechaActividadVal) {
                            mensaje += ' Advertencia: Seleccionaste una actividad pero no especificaste la fecha.';
                        } else if (!actividadVal && fechaActividadVal) {
                            mensaje += ' Advertencia: Especificaste una fecha pero no seleccionaste actividad.';
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: mensaje,
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            $('#modal-detalles-oportunidad').modal('hide');
                            loadOportunidades();
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al actualizar la oportunidad: ' + response.message,
                        confirmButtonText: 'Aceptar'
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error en la solicitud AJAX: ' + textStatus);
            }
        });
    }, 100);
}

// ============================================
// FUNCIONES PARA EL BOTÓN CALENDARIO
// ============================================

// Función para verificar si el botón calendario debe estar activo
function verificarBotonCalendario() {
    var actividad = $('#modal-actividad').val();
    var fechaActividad = $('#modal-fecha_actividad').val();
    var btnCalendario = $('#btn-ir-calendario');

    if (actividad && fechaActividad) {
        btnCalendario.prop('disabled', false);
        btnCalendario.removeClass('btn-secondary').addClass('btn-info');
    } else {
        btnCalendario.prop('disabled', true);
        btnCalendario.removeClass('btn-info').addClass('btn-secondary');
    }
}

// Event listeners para los campos de actividad y fecha
$(document).on('change', '#modal-actividad, #modal-fecha_actividad', function() {
    verificarBotonCalendario();
});

// Event listener para el botón calendario
$(document).on('click', '#btn-ir-calendario', function() {
    var actividad = $('#modal-actividad').val();
    var fechaActividad = $('#modal-fecha_actividad').val();
    var clienteId = $('#modal-cliente-id').val();

    if (actividad && fechaActividad) {
        // Mapear la actividad al título de reunión
        var tituloMapeado = mapearActividadATitulo(actividad);

        // Construir URL para el calendario con parámetros
        var url = 'calendario?cliente_id=' + encodeURIComponent(clienteId) +
                  '&titulo=' + encodeURIComponent(tituloMapeado) +
                  '&fecha=' + encodeURIComponent(fechaActividad) +
                  '&actividad_origen=' + encodeURIComponent(actividad);

        // Cerrar el modal antes de redirigir
        $('#modal-detalles-oportunidad').modal('hide');

        // Redirigir al calendario
        window.location.href = url;
    } else {
        Swal.fire({
            icon: 'warning',
            title: 'Datos incompletos',
            text: 'Debe seleccionar una actividad y fecha para acceder al calendario.',
            confirmButtonText: 'Entendido'
        });
    }
});

// Función para mapear actividad a título de reunión
function mapearActividadATitulo(actividad) {
    var mapeo = {
        'mensaje de whatsapp': 'Mensaje WhatsApp',
        'llamada': 'Llamada',
        'reunion': 'Reunión',
        'seguimiento de presupuesto': 'Seguimiento de Presupuesto',
        'ofertar': 'Ofertar',
        'llamada para demostracion': 'Llamada para Demostración',
        'otros': 'Otros'
    };

    return mapeo[actividad] || actividad;
}

// Inicializar el botón calendario cuando se muestra el modal
$(document).on('shown.bs.modal', '#modal-detalles-oportunidad', function() {
    verificarBotonCalendario();
});
