function loadOportunidades() {
    $.ajax({
        url: 'ajax/oportunidades.ajax.php',
        method: 'GET',
        data: { action: 'getOportunidades', filtrarUltimaSemana: true },
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

            // Aplicar filtros si están activos
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
        '<div class="kanban-card panel-kanban" id="oportunidad-' + oportunidad.id + '" data-fecha-cierre="' + oportunidad.fecha_cierre_estimada + '" onclick="mostrarDetalles(' + oportunidad.id + ')" draggable="true" ondragstart="drag(event)">' +
            '<h5>' + oportunidad.titulo + '</h5>' + // Título de la oportunidad
            '<p>' + oportunidad.nombre_cliente + '</p>' + // Nombre del prospecto
        '</div>';
    $('#' + oportunidad.estado).append(tarjeta);
}

function crearOportunidad() {
    return new Promise(function(resolve, reject) {
        var nuevaOportunidad = {
            nuevoTitulo: $('#titulo').val(),
            nuevaDescripcion: $('#descripcion').val(),
            nuevoValorEstimado: $('#valor_estimado').val(),
            nuevaProbabilidad: $('#probabilidad').val(),
            idCliente: $('#cliente_id').val(),
            idUsuario: $('#usuario_id').val(),
            nuevoEstado: '1', // Estado 1: seguimiento
            nuevaFechaCierre: $('#fecha_cierre').val()
        };

        $.ajax({
            url: 'ajax/oportunidades.ajax.php',
            method: 'POST',
            data: Object.assign({ action: 'crearOportunidad' }, nuevaOportunidad),
            dataType: 'json',
            success: function(response) {
                // console.log("AJAX success response:", response);
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
    console.log("cambiarEstado llamado - ID:", id, "Nuevo Estado:", nuevoEstado, "Tipo:", typeof nuevoEstado);

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
            console.log("cambiarEstado success - Respuesta:", respuesta);
            if (respuesta && respuesta.status === "success") {
                // Si el nuevo estado es 5 (Perdido) o 6 (Zona de Espera), remover la tarjeta del kanban
                if (nuevoEstado == "5" || nuevoEstado == "6") {
                    console.log("Removiendo tarjeta para estado", nuevoEstado);
                    $('#oportunidad-' + id).remove();

                    // Si estamos en la página de No Clientes, recargar la página completa
                    if (window.location.pathname.includes('NoClientes')) {
                        console.log("Recargando página de No Clientes...");
                        location.reload();
                    }
                } else {
                    console.log("Moviendo tarjeta a estado", nuevoEstado);
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
            console.log("Error AJAX en cambiarEstado:", textStatus, errorThrown);

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

    console.log("Botón cambiar estado clickeado - ID:", id, "Estado:", estado, "Tipo:", typeof estado);

    // Confirmación especial para estado "Perdido"
    if (estado == "5") {
        console.log("Mostrando confirmación para Perdido");
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
                console.log("Confirmado Perdido, cambiando estado");
                cambiarEstado(id, estado);
                // Cerrar el modal después de confirmar
                $('#modal-detalles-oportunidad').modal('hide');
            } else {
                console.log("Cancelado Perdido");
            }
        });
    } else {
        console.log("Cambiando estado sin confirmación especial");
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
            cambiarEstado(id, "6"); // Estado 6: KANBAN_ZONA_ESPERA -> Lista Zona de Espera (4)

            // Cerrar el modal de detalles
            $('#modal-detalles-oportunidad').modal('hide');

            // Mostrar modal con últimos prospectos en zona de espera después de un breve delay
            setTimeout(function() {
                mostrarUltimosEnZonaEspera();
            }, 500);
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
            if (!fechaCierre) {
                mostrar = false;
            } else {
                var fecha = new Date(fechaCierre);
                var hoy = new Date();

                switch (filtroPeriodo) {
                    case 'diario':
                        mostrar = fecha.toDateString() === hoy.toDateString();
                        break;
                    case 'semanal':
                        var primerDiaSemana = new Date(hoy);
                        primerDiaSemana.setDate(hoy.getDate() - hoy.getDay());
                        var ultimoDiaSemana = new Date(primerDiaSemana);
                        ultimoDiaSemana.setDate(primerDiaSemana.getDate() + 6);
                        mostrar = fecha >= primerDiaSemana && fecha <= ultimoDiaSemana;
                        break;
                    case 'mensual':
                        mostrar = fecha.getMonth() === hoy.getMonth() && fecha.getFullYear() === hoy.getFullYear();
                        break;
                    case 'personalizado':
                        if (fechaInicio && fechaFin) {
                            var inicio = new Date(fechaInicio);
                            var fin = new Date(fechaFin);
                            mostrar = fecha >= inicio && fecha <= fin;
                        }
                        break;
                    default:
                        mostrar = true;
                }
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

                var contenido =
                    '<form id="form-detalles-oportunidad">' +
                    '<div class="row">' +
                        '<div class="col-md-6">' +
                            '<h6>Título: <span style="color: red;">*</span></h6>' +
                            '<input type="text" class="form-control" id="modal-titulo" name="titulo" value="' + (oportunidad.titulo || '') + '" />' +
                        '</div>' +
                        '<div class="col-md-6">' +
                            '<h6>Cliente:</h6>' +
                            '<p>' + (oportunidad.nombre_cliente || 'N/A') + '</p>' +
                            '<input type="hidden" id="modal-cliente-id" value="' + (oportunidad.cliente_id || '') + '" />' +
                        '</div>' +
                    '</div>' +
                    '<div class="row">' +
                        '<div class="col-md-6">' +
                            '<h6>Información adicional:</h6>' +
                            '<textarea class="form-control" id="modal-descripcion" name="descripcion">' + (oportunidad.descripcion || '') + '</textarea>' +
                        '</div>' +
                        '<div class="col-md-6">' +
                            '<h6>Valor Estimado:</h6>' +
                            '<input type="number" step="0.01" class="form-control" id="modal-valor_estimado" name="valor_estimado" value="' + (oportunidad.valor_estimado || '') + '" />' +
                        '</div>' +
                    '</div>' +
                    '<div class="row">' +
                        '<div class="col-md-6">' +
                            '<h6>Probabilidad (%): <span style="color: red;">*</span></h6>' +
                            '<input type="number" min="0" max="100" class="form-control" id="modal-probabilidad" name="probabilidad" value="' + (oportunidad.probabilidad || '') + '" />' +
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

        // console.log("Datos a enviar en actualizarOportunidad:", datos);
        // console.log("Actividad seleccionada:", actividadVal, "Tipo:", typeof actividadVal);
        // console.log("Fecha actividad:", fechaActividadVal, "Tipo:", typeof fechaActividadVal);

        // Validar campos obligatorios antes de enviar
        if (!tituloVal || tituloVal.trim() === "") {
            alert("El campo Título es obligatorio.");
            return;
        }
        if (!datos.probabilidad || isNaN(datos.probabilidad) || datos.probabilidad < 0 || datos.probabilidad > 100) {
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
                                        console.log("Redirigiendo a URL:", url);
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

    console.log("Verificando botón calendario - Actividad:", actividad, "Fecha:", fechaActividad);

    if (actividad && fechaActividad) {
        btnCalendario.prop('disabled', false);
        btnCalendario.removeClass('btn-secondary').addClass('btn-info');
        console.log("Botón calendario ACTIVADO");
    } else {
        btnCalendario.prop('disabled', true);
        btnCalendario.removeClass('btn-info').addClass('btn-secondary');
        console.log("Botón calendario DESACTIVADO");
    }
}

// Event listeners para los campos de actividad y fecha
$(document).on('change', '#modal-actividad, #modal-fecha_actividad', function() {
    console.log("Cambio detectado en campo:", $(this).attr('id'), "Valor:", $(this).val());
    verificarBotonCalendario();
});

// Event listener para el botón calendario
$(document).on('click', '#btn-ir-calendario', function() {
    var actividad = $('#modal-actividad').val();
    var fechaActividad = $('#modal-fecha_actividad').val();
    var clienteId = $('#modal-cliente-id').val();

    console.log("Botón calendario clickeado - Actividad:", actividad, "Fecha:", fechaActividad, "Cliente ID:", clienteId);

    if (actividad && fechaActividad) {
        // Mapear la actividad al título de reunión
        var tituloMapeado = mapearActividadATitulo(actividad);
        console.log("Título mapeado:", tituloMapeado);

        // Construir URL para el calendario con parámetros
        var url = 'calendario?cliente_id=' + encodeURIComponent(clienteId) +
                  '&titulo=' + encodeURIComponent(tituloMapeado) +
                  '&fecha=' + encodeURIComponent(fechaActividad) +
                  '&actividad_origen=' + encodeURIComponent(actividad);

        console.log("Redirigiendo a calendario con URL:", url);

        // Cerrar el modal antes de redirigir
        $('#modal-detalles-oportunidad').modal('hide');

        // Redirigir al calendario
        window.location.href = url;
    } else {
        console.warn("Intento de redirección sin actividad o fecha completa");
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
    console.log("Modal de detalles mostrado - inicializando botón calendario");
    verificarBotonCalendario();
});
