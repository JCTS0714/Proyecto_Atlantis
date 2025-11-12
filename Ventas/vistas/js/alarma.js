$(document).ready(function() {
    var alarmasActivadas = {};
    var alarmasDetenidas = JSON.parse(localStorage.getItem('alarmasDetenidas') || '{}');

    function verificarAlarmas(eventos) {
        var ahora = new Date();
        eventos.forEach(function(evento) {
            if (!evento.fecha_hora) return;
            var fechaEvento = new Date(evento.fecha_hora);
            if (isNaN(fechaEvento.getTime())) return;
            var diffMinutos = (fechaEvento - ahora) / 60000;
            var tiempoAlarmaMostrar = null;
            if (diffMinutos <= 30 && diffMinutos > 10) tiempoAlarmaMostrar = 30;
            else if (diffMinutos <= 10 && diffMinutos > 5) tiempoAlarmaMostrar = 10;
            else if (diffMinutos <= 5 && diffMinutos > 0) tiempoAlarmaMostrar = 5;
            if (tiempoAlarmaMostrar && !alarmasActivadas[evento.id + '_' + tiempoAlarmaMostrar] && !alarmasDetenidas[evento.id + '_' + tiempoAlarmaMostrar]) {
                alarmasActivadas[evento.id + '_' + tiempoAlarmaMostrar] = true;
                if ($('#mensaje-alarma').length === 0) {
                    $('body').append('<div id="mensaje-alarma" style="position: fixed; top: 80px; right: 20px; background-color: #dc3545; color: white; padding: 15px 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.3); z-index: 1060; max-width: 300px; cursor: pointer;">Alarma: Reunión con cliente: ' + (evento.nombre_cliente || evento.cliente_id) + ' en menos de ' + tiempoAlarmaMostrar + ' minutos.<br><button id="btn-detener-alarma" style="margin-top: 10px; padding: 5px 10px; background-color: white; color: #dc3545; border: none; border-radius: 4px; cursor: pointer;">Detener alarma</button></div>');
                    $('#btn-detener-alarma').on('click', function() {
                        $('#mensaje-alarma').fadeOut(300, function() { $(this).remove(); });
                        alarmasDetenidas[evento.id + '_' + tiempoAlarmaMostrar] = true;
                        localStorage.setItem('alarmasDetenidas', JSON.stringify(alarmasDetenidas));
                    });
                    $('#mensaje-alarma').on('click', function() {
                        $(this).fadeOut(300, function() { $(this).remove(); });
                        alarmasDetenidas[evento.id + '_' + tiempoAlarmaMostrar] = true;
                        localStorage.setItem('alarmasDetenidas', JSON.stringify(alarmasDetenidas));
                    });
                }
            }
        });
    }

    function consultarReunionesParaNotificarConAlarma() {
        var usuarioId = $('#usuario_id').val();
        $.ajax({
            url: 'ajax/calendario.ajax.php',
            type: 'POST',
            data: { accion: 'obtener_para_notificar', usuario_id: usuarioId },
            dataType: 'json',
            success: function(response) {
                if (response && Array.isArray(response.eventos)) {
                    // Asumiendo que tienes esta función en notificaciones.js
                    if (typeof actualizarDropdownNotificaciones === 'function') {
                        actualizarDropdownNotificaciones(response.eventos);
                    }
                    verificarAlarmas(response.eventos);
                }
            }
        });
    }

    consultarReunionesParaNotificarConAlarma();
    setInterval(consultarReunionesParaNotificarConAlarma, 60000);

    // Mantener visible la alarma hasta que el usuario la cierre
    $(document).on('mouseenter', '#mensaje-alarma', function() {
        $(this).stop(true, true);
    });

    // Eliminar el fadeOut automático al salir del mouse para evitar desaparición no deseada
    // $(document).on('mouseleave', '#mensaje-alarma', function() {
    //     $(this).fadeOut(300, function() { $(this).remove(); });
    // });
});
