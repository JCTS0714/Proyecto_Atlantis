$(document).ready(function() {
    // Función para actualizar el dropdown de notificaciones
    function actualizarDropdownNotificaciones(notificaciones) {
        var ahora = new Date();
        notificaciones = notificaciones.filter(function(notificacion) {
            var fechaHoraFin = new Date(notificacion.fecha + ' ' + notificacion.hora_fin);
            // Ajuste para considerar hora_fin con o sin segundos
            if (isNaN(fechaHoraFin.getTime())) {
                // Intentar parsear sin segundos
                var fechaHoraFinStr = notificacion.fecha + ' ' + notificacion.hora_fin + ':00';
                fechaHoraFin = new Date(fechaHoraFinStr);
            }
            return fechaHoraFin >= ahora;
        });

        var lista = $('#lista-notificaciones');
        // Limpiar todas las notificaciones previas para evitar duplicados por cambio de fecha
        lista.empty();

        // Filtrar notificaciones que no deben desaparecer si hora_fin no ha pasado
        notificaciones = notificaciones.filter(function(notificacion) {
            var ahora = new Date();
            var fechaHoraFin = new Date(notificacion.fecha + ' ' + notificacion.hora_fin);
            var fechaNotificacion = new Date(notificacion.fecha);
            // Calcular diffDays sin mutar 'ahora' ni 'fechaNotificacion'
            var inicioDiaAhora = new Date(ahora.getFullYear(), ahora.getMonth(), ahora.getDate());
            var inicioDiaNotificacion = new Date(fechaNotificacion.getFullYear(), fechaNotificacion.getMonth(), fechaNotificacion.getDate());
            var diffDias = Math.floor((inicioDiaNotificacion - inicioDiaAhora) / (1000 * 60 * 60 * 24));
            var mostrar = fechaHoraFin >= ahora && diffDias <= 3;
            return mostrar;
        });

        // Después de aplicar el filtrado final, actualizar contador y cabecera
        var contador = notificaciones.length;
        $('#contador-notificaciones').text(contador);
        $('#header-notificaciones').text(contador > 0 ? 'Tienes ' + contador + ' notificación' + (contador > 1 ? 'es' : '') : 'No tienes notificaciones');

        if (contador === 0) {
            lista.append('<li><a href="#">No hay notificaciones</a></li>');
            return;
        }

        notificaciones.forEach(function(notificacion) {
            var contenidoCompleto = '';
            if (notificacion.descripcion && notificacion.descripcion.trim() !== '') {
                contenidoCompleto += '<strong>Descripción:</strong> ' + notificacion.descripcion + '<br>';
            }
            if (notificacion.observaciones && notificacion.observaciones.trim() !== '') {
                contenidoCompleto += '<strong>Observaciones:</strong> ' + notificacion.observaciones + '<br>';
            }
            if (contenidoCompleto === '') {
                contenidoCompleto = 'Reunión con cliente: ' + (notificacion.nombre_cliente || notificacion.cliente_id) + ' el día ' + notificacion.fecha;
            }

            var fechaHoraFin = new Date(notificacion.fecha + ' ' + notificacion.hora_fin);
            var diffDays = Math.floor((fechaHoraFin - ahora) / (1000 * 60 * 60 * 24));
            var colorCampana = 'text-blue';
            if (diffDays === 0) {
                colorCampana = 'text-red';
            } else if (diffDays === 1) {
                colorCampana = 'text-orange';
            } else if (diffDays === 2) {
                colorCampana = 'text-yellow';
            }

            var item = '<li><a href="#" class="notificacion-item" data-html="' + encodeURIComponent(contenidoCompleto) + '">' +
                '<i class="fa fa-bell ' + colorCampana + '"></i><br>' + contenidoCompleto + '</a></li>';
            // console.log("Notificación generada para dropdown:", item);
            lista.append(item);
        });

        $('.notificacion-item').off('click').on('click', function(e) {
            e.preventDefault();
            var contenidoHtml = decodeURIComponent($(this).data('html'));
            if ($('#mensaje-notificacion').length === 0) {
                $('body').append('<div id="mensaje-notificacion" style="position: fixed; top: 20px; right: 20px; background-color: #007bff; color: white; padding: 15px 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.3); z-index: 1050; max-width: 300px; cursor: pointer;">' + contenidoHtml + '</div>');
                $('#mensaje-notificacion').on('click', function() { $(this).fadeOut(300, function() { $(this).remove(); }); });
            } else {
                $('#mensaje-notificacion').stop(true, true).css('opacity', 1).html(contenidoHtml);
                clearTimeout($('#mensaje-notificacion').data('timeoutId'));
                var timeoutId = setTimeout(function() { $('#mensaje-notificacion').fadeOut(300, function() { $(this).remove(); }); }, 5000);
                $('#mensaje-notificacion').data('timeoutId', timeoutId);
            }
        });
    }

    // Si existe el módulo de notificaciones, registrar renderer y arrancarlo
    if (window.NotificationsModule && typeof window.NotificationsModule.registerRenderer === 'function') {
        try {
            // Registrar la función que actualiza el dropdown
            window.NotificationsModule.registerRenderer(actualizarDropdownNotificaciones);
            // Opcional: ajustar intervalo (en ms)
            if (window.NotificationsModule.init) {
                // Pasar también el parámetro de cooldown para evitar notificaciones repetidas
                window.NotificationsModule.init({ pollInterval: 30 * 1000, cooldownMinutes: 30 });
            }
            // Arrancar
            window.NotificationsModule.start();
        } catch (e) {
            console.error('Error inicializando NotificationsModule', e);
        }
    } else {
        // Fallback: iniciar polling local (si no está el módulo)
        var POLL_INTERVAL = 30 * 1000; // 30 segundos
        var pollTimer = null;
        function consultarReunionesParaNotificarFallback() {
            var usuarioId = $('#usuario_id').val();
            $.ajax({
                url: 'ajax/calendario.ajax.php',
                type: 'POST',
                data: { accion: 'obtener_para_notificar', usuario_id: usuarioId },
                dataType: 'json',
                success: function(response) {
                    if (response && Array.isArray(response.eventos)) {
                        actualizarDropdownNotificaciones(response.eventos);
                    }
                }
            });
        }
        function startPollingFallback() {
            if (pollTimer) return;
            consultarReunionesParaNotificarFallback();
            pollTimer = setInterval(consultarReunionesParaNotificarFallback, POLL_INTERVAL);
        }
        function stopPollingFallback() {
            if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
        }
        document.addEventListener('visibilitychange', function() { if (document.hidden) stopPollingFallback(); else startPollingFallback(); });
        startPollingFallback();
    }
});

    // Función para consultar reuniones próximas a notificar
    /*
    function consultarReunionesParaNotificar() {
        var usuarioId = $('#usuario_id').val();
        $.ajax({
            url: 'ajax/calendario.ajax.php',  // Ajusta la ruta
            type: 'POST',
            data: { accion: 'obtener_para_notificar', usuario_id: usuarioId },
            dataType: 'json',
            success: function(response) {
                if (response && Array.isArray(response.eventos)) {
                    actualizarDropdownNotificaciones(response.eventos);
                    var hoy = new Date().toISOString().split('T')[0];
                    response.eventos.forEach(function(reunion) {
                        $.ajax({
                            url: 'ajax/calendario.ajax.php',
                            type: 'POST',
                            data: { accion: 'actualizar_ultima_notificacion', id: reunion.id, fecha: hoy }
                        });
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Error al consultar reuniones para notificar.");
            }
        });
    }

    // Ejecutar consulta al cargar y cada minuto
    consultarReunionesParaNotificar();
    setInterval(consultarReunionesParaNotificar, 5000);

    // Log para diagnosticar actualizaciones periódicas
    // console.log("Iniciado el polling de notificaciones cada 5 segundos");
    */
