$(function () {

  // Variables para modales y formularios
  var modalCrear = $('#modalCrearReunion');
  var modalDetalle = $('#modalDetalleReunion');
  var formCrear = $('#formCrearReunion');
  var formEditar = $('#formEditarReunion');

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
  if ($('#calendarReuniones').length > 0) {
    $('#calendarReuniones').fullCalendar({
      locale: 'es',
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
      },
      buttonText: {
        today: 'Hoy',
        month: 'Mes',
        week: 'Semana',
        day: 'Día'
      },
      monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
      monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
      dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
      dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
      editable: true,
      eventLimit: true,
      defaultTimedEventDuration: '00:30:00',
      height: 600,
      slotDuration: '00:15:00', // Intervalos de 15 minutos en vista semana y día
      minTime: '06:00:00', // Hora mínima visible en vista semana y día
      maxTime: '22:00:00', // Hora máxima visible en vista semana y día
      displayEventTime: false, // Agregado para ocultar hora automática en todas las vistas
      eventRender: function(event, element) {
        element.attr('data-cliente_id', event.cliente_id);
        element.find('.fc-time').remove(); // Descomentar para eliminar la hora automática y evitar duplicados
        element.css({
          'font-size': '14px',
          'padding': '4px 6px',
          'min-height': '24px',
          'white-space': 'normal'
        });
      },
      dayClick: function(date, jsEvent, view) {
        // Mostrar advertencia sobre actividades duplicadas
        var clienteId = getUrlParameter('cliente_id');
        if (clienteId) {
          // Si hay un cliente pre-seleccionado, verificar duplicados
          $.ajax({
            url: 'ajax/calendario.ajax.php',
            type: 'POST',
            data: {
              accion: 'verificar_duplicados',
              cliente_id: clienteId
            },
            dataType: 'json',
            success: function(response) {
              if (response.duplicados && response.duplicados.length > 0) {
                var actividades = response.duplicados.join(', ');
                alert('⚠️ Advertencia: El cliente ya tiene las siguientes actividades: ' + actividades + '\n\nRecuerda que no se permiten actividades duplicadas para el mismo cliente.');
              }
              // Abrir modal de crear reunión con la fecha seleccionada
              abrirModalCrearReunion(date);
            },
            error: function() {
              // En caso de error, abrir modal de todas formas
              abrirModalCrearReunion(date);
            }
          });
        } else {
          // Mostrar advertencia general
          alert('⚠️ Recordatorio: No se permiten actividades duplicadas para el mismo cliente.\n\nAsegúrate de seleccionar un cliente que no tenga ya esa actividad registrada.');
          abrirModalCrearReunion(date);
        }
      },
      events: function (start, end, timezone, callback) {
        $.ajax({
          url: 'ajax/calendario.ajax.php',
          type: 'POST',
          data: { accion: 'mostrar' },
          dataType: 'json',
          success: function (response) {
            console.log("Reuniones recibidas del servidor:", response);
            var events = [];
          // Función para formatear hora con "a.m." y "p.m." sin etiquetas HTML
          function formatAmPm(time) {
            console.log("formatAmPm - time:", time);
            var hour = moment(time).format('h:mm');
            var ampm = moment(time).format('a');
            console.log("formatAmPm - ampm:", ampm);
            if (ampm === 'am') return hour + ' a.m.';
            if (ampm === 'pm') return hour + ' p.m.';
            return hour;
          }
          $.each(response.eventos, function (i, item) {
            if (!item.id) {
              console.error("Error: El ID de la reunión está vacío para el evento con título:", item.title);
            } else {
              console.log("Procesando reunión con ID:", item.id);
            }
            var startTime = formatAmPm(item.start);
            var endTime = item.end ? formatAmPm(item.end) : formatAmPm(moment(item.start).add(30, 'minutes'));
            // Limpiar título original de posibles horas previas (formato hh:mm a.m. - )
            var cleanTitle = item.title.replace(/^\d{1,2}:\d{2}\s[ap]\.m\.\s-\s/, '');
            var titleWithTime = startTime + ' - ' + cleanTitle; // Incluir hora formateada en el título limpio
            // Asegurar que end tenga valor válido para mostrar duración
            var endDate = item.end ? item.end : moment(item.start).add(30, 'minutes').format();
            events.push({
              id: item.id,
              title: titleWithTime,
              start: item.start,
              end: endDate,
              backgroundColor: item.color || '#3c8dbc',
              borderColor: item.color || '#3c8dbc',
              allDay: false,
              displayEventTime: false,
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
        // Cargar datos en modal detalle
        $('#editar_id').val(calEvent.id);
        // Limpiar etiquetas HTML del título para mostrar solo texto plano en el modal
        var plainTitle = $('<div>').html(calEvent.title).text();
        $('#editar_titulo').val(plainTitle);
        $('#editar_fecha').val(calEvent.start.format('YYYY-MM-DD'));
        $('#editar_hora_inicio').val(calEvent.start.format('HH:mm'));
        $('#editar_hora_fin').val(calEvent.end ? calEvent.end.format('HH:mm') : '');
        $('#editar_descripcion').val(calEvent.descripcion);
        $('#editar_ubicacion').val(calEvent.ubicacion);
        $('#editar_observaciones').val(calEvent.observaciones);

        // Prellenar select cliente con nombre obtenido vía AJAX
        var clienteId = calEvent.cliente_id;
        var clienteSelect = $('#editar_cliente_id');

        // Prellenar usuario_id en el formulario de edición
        if ($('#editar_usuario_id').length === 0) {
          formEditar.append('<input type="hidden" id="editar_usuario_id" name="usuario_id">');
        }
        $('#editar_usuario_id').val(calEvent.usuario_id);

        $.ajax({
          url: 'ajax/clientes.ajax.php',
          type: 'POST',
          data: { idCliente: clienteId },
          dataType: 'json',
          success: function(response) {
            if (response && response.nombre) {
              // Crear opción con nombre del cliente
              var newOption = new Option(response.nombre, clienteId, true, true);
              clienteSelect.empty().append(newOption).trigger('change');
            } else {
              // Si no se obtiene nombre, mostrar solo ID
              var newOption = new Option(clienteId, clienteId, true, true);
              clienteSelect.empty().append(newOption).trigger('change');
            }
          },
          error: function() {
            // En caso de error, mostrar solo ID
            var newOption = new Option(clienteId, clienteId, true, true);
            clienteSelect.empty().append(newOption).trigger('change');
          }
        });

        modalDetalle.modal('show');
      },
      eventRender: function(event, element) {
        element.attr('data-cliente_id', event.cliente_id);
      },
      eventAfterRender: function(event, element) {
        // Permitir que el título con HTML se renderice correctamente
        console.log("eventAfterRender - event.title:", event.title);
        console.log("eventAfterRender - element.fc-title HTML:", element.find('.fc-title').html());
        element.find('.fc-title').html(event.title);
      }
    });
  }

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

          // Actualizar lista de clientes recientes con actividades sin recargar la página
          $.ajax({
            url: 'ajax/clientes_actividades.ajax.php',
            type: 'POST',
            data: { accion: 'obtener_lista_clientes' },
            success: function(data) {
              $('.box-solid .list-group').html(data);
              // Reactivar eventos para botones de actividad después de actualizar la lista
              $(document).off('click', '.actividad-btn').on('click', '.actividad-btn', function() {
                var actividad = $(this).data('actividad');
                var clienteId = $(this).data('cliente-id');
                // Función para destacar actividad en el calendario
                $('.fc-event').removeClass('actividad-destacada');
                $('.fc-event').each(function() {
                  var eventTitle = $(this).find('.fc-title').text();
                  var eventClienteId = $(this).attr('data-cliente_id');
                  if (eventTitle === actividad && eventClienteId == clienteId) {
                    $(this).addClass('actividad-destacada');
                  }
                });
              });
            },
            error: function(xhr, status, error) {
              console.error('Error al actualizar lista de clientes:', status, error);
            }
          });

          // Limpiar parámetros de la URL para evitar que el modal se reabra con datos previos
          if (window.history.replaceState) {
            var url = new URL(window.location);
            url.searchParams.delete('cliente_id');
            url.searchParams.delete('titulo');
            url.searchParams.delete('fecha');
            url.searchParams.delete('actividad_origen');
            window.history.replaceState({}, document.title, url.toString());
          }
        } else if (response.error) {
          alert('Error al crear la reunión: ' + response.error);
        } else {
          alert('Error al crear la reunión: ' + JSON.stringify(response));
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

  // Limpiar parámetros de la URL también cuando se cierra el modal al hacer click fuera o cerrar modal
  modalCrear.on('hide.bs.modal', function () {
    if (window.history.replaceState) {
      var url = new URL(window.location);
      url.searchParams.delete('cliente_id');
      url.searchParams.delete('titulo');
      url.searchParams.delete('fecha');
      url.searchParams.delete('actividad_origen');
      window.history.replaceState({}, document.title, url.toString());
    }
  });

formEditar.off('submit').on('submit', function (e) {
  e.preventDefault();
  
  // Validación de campos incluyendo cliente_id
  if ($('#editar_titulo').val().trim() === '' || 
      $('#editar_fecha').val().trim() === '' || 
      $('#editar_hora_inicio').val().trim() === '' || 
      $('#editar_hora_fin').val().trim() === '' ||
      $('#editar_cliente_id').val().trim() === '') {
    alert('Por favor, complete todos los campos obligatorios incluyendo el cliente.');
    return;
  }

    var datos = formEditar.serializeArray();
    console.log("Datos enviados para actualizar reunión:", datos);
  var dataObj = {};
  $.each(datos, function (i, field) {
    dataObj[field.name] = field.value;
  });
  dataObj.accion = 'actualizar';

  // Deshabilitar botón para evitar envíos múltiples
  var $btn = formEditar.find('button[type="submit"]');
  $btn.prop('disabled', true);

  $.ajax({
    url: 'ajax/calendario.ajax.php',
    type: 'POST',
    data: dataObj,
    dataType: 'json',
    success: function (response) {
    if (response == 'ok' || (response.success && response.success === true)) {
    Swal.fire({
      icon: "success",
      title: "¡La reunión ha sido editada correctamente!",
      showConfirmButton: true,
      confirmButtonText: "Cerrar",
      showCloseButton: true
    }).then((result) => {
      if (result.isConfirmed) {
        window.location = "calendario";
      }
    });
    modalDetalle.modal('hide');
    $('#calendarReuniones').fullCalendar('refetchEvents');
    } else {
        var errorMsg = response.error ? response.error : JSON.stringify(response);
        mostrarMensaje('Error al actualizar la reunión: ' + errorMsg, 'danger');
      }

      // Función para mostrar notificación visual
      function mostrarMensaje(mensaje, tipo) {
        var $msg = $('<div class="alert alert-' + tipo + ' alert-dismissible fade show" role="alert">'
          + mensaje +
          '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'
          + '<span aria-hidden="true">&times;</span>'
          + '</button>'
          + '</div>');
        $("body").append($msg);
        setTimeout(function() { $msg.alert('close'); }, 3000);
      }
    },
    error: function(xhr, status, error) {
      alert('Error en la comunicación con el servidor: ' + error + '\\nEstado: ' + status + '\\nRespuesta: ' + xhr.responseText);
    },
    complete: function() {
      // Rehabilitar botón después de la petición
      $btn.prop('disabled', false);
    }
  });
  });

  // Botón eliminar reunión
  $('#btnEliminarReunion').off('click').on('click', function () {
    Swal.fire({
      title: '¿Está seguro de eliminar esta reunión?',
      text: "Esta acción no se puede deshacer",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: 'ajax/calendario.ajax.php',
          type: 'POST',
          data: { accion: 'eliminar', id: $('#editar_id').val() },
          dataType: 'json',
          success: function (response) {
            if (response == 'ok') {
              Swal.fire(
                'Eliminado!',
                'La reunión ha sido eliminada.',
                'success'
              ).then(() => {
                modalDetalle.modal('hide');
                $('#calendarReuniones').fullCalendar('refetchEvents');
              });
            } else {
              Swal.fire(
                'Error',
                'Error al eliminar la reunión.',
                'error'
              );
            }
          },
          error: function(xhr, status, error) {
            Swal.fire(
              'Error',
              'Error en la comunicación con el servidor.',
              'error'
            );
          }
        });
      }
    });
  });

  // Función para abrir modal de crear reunión
  function abrirModalCrearReunion(date) {
    formCrear[0].reset();
    $('#fecha').val(date.format('YYYY-MM-DD'));
    $('#titulo').focus();
    modalCrear.modal('show');
  }

  // Función para obtener parámetros de la URL
  function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
  }

  // Función para abrir modal desde parámetros de URL
  function openModalFromUrl() {
    var clienteId = getUrlParameter('cliente_id');
    var titulo = getUrlParameter('titulo');
    var fecha = getUrlParameter('fecha');

    console.log("Verificando parámetros de URL:", {clienteId, titulo, fecha});

    if (clienteId && titulo && fecha) {
      console.log("Abriendo modal con parámetros de URL:", {clienteId, titulo, fecha});

      // Prellenar el formulario
      $('#titulo').val(titulo);
      $('#fecha').val(fecha);

      // Prellenar cliente usando AJAX
      $.ajax({
        url: 'ajax/clientes.ajax.php',
        type: 'POST',
        data: { idCliente: clienteId },
        dataType: 'json',
        success: function(response) {
          console.log("Respuesta AJAX cliente:", response);
          if (response && response.nombre) {
            // Crear opción con nombre del cliente
            var newOption = new Option(response.nombre, clienteId, true, true);
            $('#cliente_id').empty().append(newOption).trigger('change');
            console.log("Cliente prellenado correctamente:", response.nombre);
          } else {
            // Si no se obtiene nombre, mostrar solo ID
            var newOption = new Option(clienteId, clienteId, true, true);
            $('#cliente_id').empty().append(newOption).trigger('change');
            console.log("Cliente prellenado con ID:", clienteId);
          }

          // Mostrar el modal después de prellenar los datos
          setTimeout(function() {
            $('#modalCrearReunion').modal('show');
            console.log("Modal mostrado");
          }, 500);
        },
        error: function(xhr, status, error) {
          console.error("Error en AJAX cliente:", status, error);
          // En caso de error, mostrar solo ID
          var newOption = new Option(clienteId, clienteId, true, true);
          $('#cliente_id').empty().append(newOption).trigger('change');

          // Mostrar el modal después de prellenar los datos
          setTimeout(function() {
            $('#modalCrearReunion').modal('show');
            console.log("Modal mostrado (con error en cliente)");
          }, 500);
        }
      });
    } else {
      console.log("No hay parámetros suficientes en la URL para abrir modal");
    }
  }

  // Ejecutar la función después de un breve delay para asegurar que todo esté inicializado
  setTimeout(function() {
    openModalFromUrl();
  }, 1000);

  // Función para destacar actividad en el calendario
  function destacarActividad(actividad, clienteId) {
    // Quitar resaltado previo
    $('.fc-event').removeClass('actividad-destacada');

    // Buscar eventos que coincidan con la actividad y cliente
    $('.fc-event').each(function() {
      var eventTitle = $(this).find('.fc-title').text();
      var eventClienteId = $(this).attr('data-cliente_id');

      // Extraer solo la parte del título después de la hora y guion (formato "hh:mm a.m. - actividad")
      var tituloSinHora = eventTitle.replace(/^\d{1,2}:\d{2}\s[ap]\.m\.\s-\s/, '');

      if (tituloSinHora === actividad && eventClienteId == clienteId) {
        $(this).addClass('actividad-destacada');
      }
    });
  }

  // Event listener para botones de actividad
  $(document).on('click', '.actividad-btn', function() {
    var actividad = $(this).data('actividad');
    var clienteId = $(this).data('cliente-id');

    destacarActividad(actividad, clienteId);
  });

  // Event listener para quitar resaltado al hacer clic fuera
  $(document).on('click', function(e) {
    if (!$(e.target).closest('.fc-event').length && !$(e.target).closest('.actividad-btn').length) {
      $('.fc-event').removeClass('actividad-destacada');
    }
  });

});
