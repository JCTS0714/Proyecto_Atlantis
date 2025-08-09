$(function () {

  // Variables para modales y formularios
  var modalCrear = $('#modalCrearReunion');
  var modalDetalle = $('#modalDetalleReunion');
  var formCrear = $('#formCrearReunion');
  var formEditar = $('#formEditarReunion');
  var eventoSeleccionado = null;

  // Función para inicializar select2 buscador de clientes
  function initSelect2Cliente(selector, modalParent) {
    $(selector).select2({
      placeholder: 'Buscar cliente',
      minimumInputLength: 1,
      dropdownParent: $(modalParent),
      allowClear: true,
      ajax: {
        url: '/Proyecto_atlantis/Ventas/ajax/clientes_oportunidades.ajax.php',
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            q: params.term // término de búsqueda
          };
        },
        processResults: function (data) {
          return {
            results: data.map(function(cliente) {
              return { id: cliente.id, text: cliente.nombre };
            })
          };
        },
        cache: true
      }
    });
  }

  // Inicializar select2 para buscador de cliente en modales con función reutilizable
  initSelect2Cliente('#cliente_id', '#modalCrearReunion');
  initSelect2Cliente('#editar_cliente_id', '#modalDetalleReunion');

  // Inicializar calendario para reuniones usando el ID 'calendarReuniones'
  $('#calendarReuniones').fullCalendar({
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'month,agendaWeek,agendaDay'
    },
    buttonText: {
      today: 'hoy',
      month: 'mes',
      week: 'semana',
      day: 'día'
    },
    editable: true,
    droppable: true,
    eventLimit: true,
          events: function (start, end, timezone, callback) {
            $.ajax({
              url: 'ajax/calendario.ajax.php',
              type: 'POST',
              data: { accion: 'mostrar' },
              dataType: 'json',
              success: function (response) {
                var events = [];
                $.each(response, function (i, item) {
                  events.push({
                    id: item.id,
                    title: item.titulo,
                    start: item.fecha + 'T' + item.hora_inicio,
                    end: item.fecha + 'T' + item.hora_fin,
                    backgroundColor: item.color || '#3c8dbc',
                    borderColor: item.color || '#3c8dbc',
                    allDay: false,
                    descripcion: item.descripcion,
                    cliente_id: item.cliente_id,
                    usuario_id: item.usuario_id,
                    ubicacion: item.ubicacion,
                    observaciones: item.observaciones
                  });
                });
                callback(events);
              }
            });
          },
      drop: function (date, jsEvent, ui, resourceId) {
        // Abrir modal creación con datos del evento arrastrado
        var originalEventObject = $(this).data('eventObject');
        if (!originalEventObject) return;

        formCrear[0].reset();
        $('#titulo').val(originalEventObject.title);
        $('#fecha').val(date.format('YYYY-MM-DD'));
        // Asignar color de la tarjeta arrastrada al campo oculto o variable para usar en creación
        $('#color').val(originalEventObject.color || '#3c8dbc');
        modalCrear.modal('show');
        eventoSeleccionado = null;
      },
    eventDrop: function(event, delta, revertFunc) {
      // Actualizar solo fecha de reunión al arrastrar
      var datos = {
        id: event.id,
        fecha: event.start.format('YYYY-MM-DD'),
        accion: 'actualizar_fecha'
      };
      $.ajax({
        url: 'ajax/calendario.ajax.php',
        type: 'POST',
        data: datos,
        dataType: 'json',
        success: function(response) {
          if (response != 'ok') {
            alert('Error al actualizar la fecha de la reunión.');
            revertFunc();
          }
        },
        error: function(xhr, status, error) {
          alert('Error en la comunicación con el servidor: ' + error + '\\nEstado: ' + status + '\\nRespuesta: ' + xhr.responseText);
          console.error('Error AJAX actualizar fecha:', status, error, xhr.responseText);
          revertFunc();
        }
      });
    },
    eventClick: function (calEvent, jsEvent, view) {
      eventoSeleccionado = calEvent;
      // Cargar datos en modal detalle
      $('#editar_id').val(calEvent.id);
      $('#editar_titulo').val(calEvent.title);
      $('#editar_fecha').val(calEvent.start.format('YYYY-MM-DD'));
      $('#editar_hora_inicio').val(calEvent.start.format('HH:mm'));
      $('#editar_hora_fin').val(calEvent.end ? calEvent.end.format('HH:mm') : '');
      $('#editar_descripcion').val(calEvent.descripcion);
      $('#editar_ubicacion').val(calEvent.ubicacion);
      $('#editar_observaciones').val(calEvent.observaciones);
      modalDetalle.modal('show');
    }
  });

  // Envío formulario creación reunión
  formCrear.off('submit').on('submit', function (e) {
    e.preventDefault();
    var datos = formCrear.serializeArray();
    var dataObj = {};
    $.each(datos, function (i, field) {
      dataObj[field.name] = field.value;
    });
    dataObj.accion = 'crear';

    // Deshabilitar botón para evitar envíos múltiples
    var $btn = formCrear.find('button[type="submit"]');
    $btn.prop('disabled', true);

    $.ajax({
      url: 'ajax/calendario.ajax.php',
      type: 'POST',
      data: dataObj,
      dataType: 'json',
      success: function (response) {
        if (response == 'ok') {
          modalCrear.modal('hide');
          $('#calendarReuniones').fullCalendar('refetchEvents');
        } else {
          alert('Error al crear la reunión: ' + response);
        }
      },
      error: function(xhr, status, error) {
        alert('Error en la comunicación con el servidor al crear la reunión: ' + error + '\\nEstado: ' + status + '\\nRespuesta: ' + xhr.responseText);
        console.error('Error AJAX crear reunión:', status, error, xhr.responseText);
      },
      complete: function() {
        // Rehabilitar botón después de la petición
        $btn.prop('disabled', false);
      }
    });
  });

  // Envío formulario edición reunión
  formEditar.off('submit').on('submit', function (e) {
    e.preventDefault();
    var datos = formEditar.serializeArray();
    var dataObj = {};
    $.each(datos, function (i, field) {
      dataObj[field.name] = field.value;
    });
    dataObj.accion = 'actualizar';
    $.ajax({
      url: 'ajax/calendario.ajax.php',
      type: 'POST',
      data: dataObj,
      dataType: 'json',
      success: function (response) {
        if (response == 'ok') {
          modalDetalle.modal('hide');
          $('#calendarReuniones').fullCalendar('refetchEvents');
        } else {
          alert('Error al actualizar la reunión.');
        }
      }
    });
  });

  // Botón eliminar reunión
  $('#btnEliminarReunion').off('click').on('click', function () {
    if (confirm('¿Está seguro de eliminar esta reunión?')) {
      $.ajax({
        url: 'ajax/calendario.ajax.php',
        type: 'POST',
        data: { accion: 'eliminar', id: $('#editar_id').val() },
        dataType: 'json',
        success: function (response) {
          if (response == 'ok') {
            modalDetalle.modal('hide');
            $('#calendarReuniones').fullCalendar('refetchEvents');
          } else {
            alert('Error al eliminar la reunión.');
          }
        }
      });
    }
  });

});
