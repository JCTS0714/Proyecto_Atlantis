$(function () {

  // Inicializar eventos externos arrastrables
  function init_events(ele) {
    ele.each(function () {
      var eventObject = {
        id: $(this).data('id'),
        title: $.trim($(this).text()),
        color: $(this).css('background-color')
      };
      $(this).data('eventObject', eventObject);
      $(this).draggable({
        zIndex: 1070,
        revert: true,
        revertDuration: 0
      });
    });
  }

  init_events($('#external-events div.external-event'));

  // Variable para color actual
  var currColor = '#3c8dbc';

  // Selector de color para eventos
  $('#color-chooser > li > a').click(function (e) {
    e.preventDefault();
    currColor = $(this).css('color');
    $('#add-new-event').css({ 'background-color': currColor, 'border-color': currColor });
    // Evitar que el # se agregue a la URL
    return false;
  });

  // Envío creación evento directo sin modal
  $('#add-new-event').on('click', function (e) {
    e.preventDefault();
    var titulo = $('#new-event').val().trim();
    if (titulo === '') {
      alert('Por favor, ingrese un título para el evento.');
      return;
    }
    var datos = {
      titulo: titulo,
      color: currColor,
      accion: 'crear_evento'
    };
    $.ajax({
      url: 'ajax/evento.ajax.php',
      type: 'POST',
      data: datos,
      dataType: 'json',
      success: function (response) {
        if (response == 'ok') {
          $('#new-event').val('');
          $('#calendarEventos').fullCalendar('refetchEvents');
          // Recargar eventos arrastrables
          cargarEventosExternos();
        } else {
          alert('Error al crear el evento.');
        }
      }
    });
  });

  // Función para cargar eventos (plantillas) en el calendario
  function cargarEventos(callback) {
    $.ajax({
      url: 'ajax/evento.ajax.php',
      type: 'POST',
      data: { accion: 'mostrar_eventos' },
      dataType: 'json',
      success: function (response) {
        var events = [];
        $.each(response, function (i, item) {
          events.push({
            id: item.id,
            title: item.titulo,
            backgroundColor: item.color || currColor,
            borderColor: item.color || currColor,
            allDay: false
          });
        });
        callback(events);
      }
    });
  }

  // Inicializar calendario para eventos usando el mismo ID 'calendarEventos'
  $('#calendarEventos').fullCalendar({
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
    editable: false,
    droppable: true,
    eventLimit: true,
    events: function (start, end, timezone, callback) {
      cargarEventos(callback);
    }
  });

  // Función para cargar eventos arrastrables en el panel lateral
  function cargarEventosExternos() {
    $('#external-events').empty();
    $.ajax({
      url: 'ajax/evento.ajax.php',
      type: 'POST',
      data: { accion: 'mostrar_eventos' },
      dataType: 'json',
      success: function (response) {
        $.each(response, function (i, item) {
          var eventDiv = $('<div class="external-event"></div>');
          eventDiv.text(item.titulo);
          eventDiv.css('background-color', item.color);
          eventDiv.data('id', item.id);
          eventDiv.data('eventObject', { id: item.id, title: item.titulo, color: item.color });
          $('#external-events').append(eventDiv);
          eventDiv.draggable({
            zIndex: 1070,
            revert: true,
            revertDuration: 0
          });
        });
      }
    });
  }

  // Inicializar eventos arrastrables al cargar la página
  cargarEventosExternos();

});
