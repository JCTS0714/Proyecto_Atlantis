$(function () {

  // Variables para modales y formularios
  var modalCrear = $('#modalCrearReunion');
  var modalDetalle = $('#modalDetalleReunion');
  var formCrear = $('#formCrearReunion');
  var formEditar = $('#formEditarReunion');

  // Función reutilizable para recargar la lista lateral de clientes con actividades
  function refreshClientesActividades() {
    $.ajax({
      url: 'ajax/clientes_actividades.ajax.php',
      type: 'POST',
      data: { accion: 'obtener_lista_clientes' },
      success: function(data) {
        $('.box-solid .list-group').html(data);
        // No es necesario re-enlazar los handlers aquí: existe un listener global para '.actividad-btn'
        // (ver abajo) que llama a `destacarActividad`. Simplemente actualizar el HTML.
      },
      error: function(xhr, status, error) {
        console.error('Error al actualizar lista de clientes:', status, error);
      }
    });
  }

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
            var events = [];
          // Función para formatear hora con "a.m." y "p.m." sin etiquetas HTML
          function formatAmPm(time) {
            var hour = moment(time).format('h:mm');
            var ampm = moment(time).format('a');
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
              cliente_nombre: item.nombre_cliente || item.cliente_id,
              usuario_id: item.usuario_id,
              ubicacion: item.ubicacion,
              observaciones: item.observaciones
            });
          });
            // Guardar cache global de eventos para que otros módulos (ej. alarmas) lo consuman
            window._calEvents = events;
            // Notificar a cualquier listener que los eventos han sido actualizados (para checks inmediatos)
            try { document.dispatchEvent(new CustomEvent('calEventsUpdated')); } catch(e) { console.warn('Event dispatch failed', e); }
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
        // En el flujo normal de edición desde el calendario, mantener la fecha como solo lectura
        $('#editar_fecha').prop('readonly', true).attr('type', 'text');
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
                // Deshabilitar edición del cliente en el modal de edición
                clienteSelect.prop('disabled', true).trigger('change.select2');
            } else {
              // Si no se obtiene nombre, mostrar solo ID
              var newOption = new Option(clienteId, clienteId, true, true);
                clienteSelect.empty().append(newOption).trigger('change');
                clienteSelect.prop('disabled', true).trigger('change.select2');
            }
          },
          error: function() {
            // En caso de error, mostrar solo ID
            var newOption = new Option(clienteId, clienteId, true, true);
            clienteSelect.empty().append(newOption).trigger('change');
            clienteSelect.prop('disabled', true).trigger('change.select2');
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
          try { refreshClientesActividades(); } catch(e){ console.warn('refreshClientesActividades falló', e); }

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

  // Al cerrar el modal de detalles/edición, re-habilitar el selector de cliente
  modalDetalle.on('hide.bs.modal', function () {
    // Rehabilitar select de cliente en caso de que estuviera deshabilitado
    try {
      $('#editar_cliente_id').prop('disabled', false).trigger('change.select2');
    } catch(e) { console.warn('No se pudo re-habilitar #editar_cliente_id', e); }
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

    // Si el select de cliente está deshabilitado (modo edición), asegurarse de incluir cliente_id
    if ($('#editar_cliente_id').is(':disabled')) {
      var clienteVal = $('#editar_cliente_id').val();
      if ($('#editar_cliente_id_hidden').length === 0) {
        formEditar.append('<input type="hidden" id="editar_cliente_id_hidden" name="cliente_id">');
      }
      $('#editar_cliente_id_hidden').val(clienteVal);
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
    try { refreshClientesActividades(); } catch(e) { console.warn('refreshClientesActividades error', e); }
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
    // Asegurar que el título esté editable cuando se abre desde el calendario
    $('#titulo').prop('readonly', false);
    // Asegurar que el selector de cliente para creación esté habilitado
    $('#cliente_id').prop('disabled', false).trigger('change.select2');
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
    var actividadOrigen = getUrlParameter('actividad_origen');

   // console.log("Verificando parámetros de URL:", {clienteId, titulo, fecha});

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
            // Asegurar que el select de creación esté habilitado
            $('#cliente_id').prop('disabled', false).trigger('change.select2');
            console.log("Cliente prellenado correctamente:", response.nombre);
          } else {
            // Si no se obtiene nombre, mostrar solo ID
            var newOption = new Option(clienteId, clienteId, true, true);
            $('#cliente_id').empty().append(newOption).trigger('change');
            // Asegurar que el select de creación esté habilitado
            $('#cliente_id').prop('disabled', false).trigger('change.select2');
            console.log("Cliente prellenado con ID:", clienteId);
          }

          // Mostrar el modal después de prellenar los datos
          setTimeout(function() {
            // Si el modal viene desde una actividad (oportunidad), desactivar edición del título
            if (actividadOrigen && actividadOrigen.toLowerCase() === 'oportunidad') {
              $('#titulo').prop('readonly', true);
            } else {
              $('#titulo').prop('readonly', false);
            }

            $('#modalCrearReunion').modal('show');
            console.log("Modal mostrado");
          }, 500);
        },
        error: function(xhr, status, error) {
          console.error("Error en AJAX cliente:", status, error);
          // En caso de error, mostrar solo ID
          var newOption = new Option(clienteId, clienteId, true, true);
          $('#cliente_id').empty().append(newOption).trigger('change');
          // Asegurar que el select de creación esté habilitado
          $('#cliente_id').prop('disabled', false).trigger('change.select2');

          // Mostrar el modal después de prellenar los datos
          setTimeout(function() {
            if (actividadOrigen && actividadOrigen.toLowerCase() === 'oportunidad') {
              $('#titulo').prop('readonly', true);
            } else {
              $('#titulo').prop('readonly', false);
            }
            $('#modalCrearReunion').modal('show');
            console.log("Modal mostrado (con error en cliente)");
          }, 500);
        }
      });
    } else {
      //console.log("No hay parámetros suficientes en la URL para abrir modal");
    }
  }

  // Ejecutar la función después de un breve delay para asegurar que todo esté inicializado
  setTimeout(function() {
    openModalFromUrl();
  }, 1000);

  // ========== Reuniones pasadas: check y modal ==========
  function renderReunionesPasadas(rows) {
    var tbody = document.querySelector('#tablaReunionesPasadas tbody');
    if (!tbody) return;
    tbody.innerHTML = '';
    rows.forEach(function(r){
      var tr = document.createElement('tr');
      var fecha = r.fecha || '';
      var hora = (r.hora_inicio || '') + ' - ' + (r.hora_fin || '');
      var titulo = r.titulo || '';
      var cliente = r.nombre_cliente || r.cliente_id || '';
      tr.innerHTML = '<td>'+fecha+'</td><td>'+hora+'</td><td>'+titulo+'</td><td>'+cliente+'</td>' +
        '<td>'+
          '<button class="btn btn-xs btn-success btn-concretado" data-id="'+r.id+'" title="Concretado"><i class="fa fa-check"></i></button> '
        + '<button class="btn btn-xs btn-warning btn-editar" data-id="'+r.id+'" title="Editar"><i class="fa fa-pencil"></i></button> '
        + '<button class="btn btn-xs btn-default btn-archivar" data-id="'+r.id+'" title="No concretado"><i class="fa fa-times"></i></button>'
        + '</td>';
      tbody.appendChild(tr);
    });

    // attach handlers
    document.querySelectorAll('.btn-concretado').forEach(function(btn){
      btn.addEventListener('click', function(){
        var id = this.getAttribute('data-id');
        if (!confirm('Marcar reunión como concretada y archivar?')) return;
        $.post('ajax/calendario.ajax.php', { accion: 'marcar_concretado', id: id, usuario_id: $('#usuario_id').val() }, function(resp){
          try {
            var j = typeof resp === 'string' ? JSON.parse(resp) : resp;
            if (j.success) {
              $('#tablaReunionesPasadas tbody tr').filter(function(){ return $(this).find('.btn-concretado').data('id') == id; }).remove();
              $('#calendarReuniones').fullCalendar('refetchEvents');
              try { refreshClientesActividades(); } catch(e){ console.warn('refreshClientesActividades error', e); }
            }
          } catch(e){ console.error(e); }
        });
      });
    });

    document.querySelectorAll('.btn-archivar').forEach(function(btn){
      btn.addEventListener('click', function(){
        var id = this.getAttribute('data-id');
        if (!confirm('Marcar reunión como no concretada?')) return;
        $.post('ajax/calendario.ajax.php', { accion: 'marcar_no_concretado', id: id, usuario_id: $('#usuario_id').val() }, function(resp){
          try {
            var j = typeof resp === 'string' ? JSON.parse(resp) : resp;
            if (j.success) {
              $('#tablaReunionesPasadas tbody tr').filter(function(){ return $(this).find('.btn-archivar').data('id') == id; }).remove();
              $('#calendarReuniones').fullCalendar('refetchEvents');
              try { refreshClientesActividades(); } catch(e){ console.warn('refreshClientesActividades error', e); }
            }
          } catch(e){ console.error(e); }
        });
      });
    });

    document.querySelectorAll('.btn-editar').forEach(function(btn){
      btn.addEventListener('click', function(){
        var id = this.getAttribute('data-id');
        // Abrir modal de edición reutilizando la existente
        // Cargar datos por AJAX y abrir modalDetalle
        $.post('ajax/calendario.ajax.php', { accion: 'mostrar', id: id }, function(resp){
          // la acción 'mostrar' devuelve todos los eventos; mejor usar controlador para obtener individual (si no existe, reusar data)
          // Para simplicidad, se recargará el calendario y buscará el evento en window._calEvents
          $('#calendarReuniones').fullCalendar('refetchEvents');
          setTimeout(function(){
            var evs = window._calEvents || [];
            var ev = evs.find(function(e){ return String(e.id) === String(id); });
            if (ev) {
              $('#editar_id').val(ev.id);
              $('#editar_titulo').val($('<div>').html(ev.title).text());
              $('#editar_fecha').val(moment(ev.start).format('YYYY-MM-DD'));
              // En el caso de edición provista desde el modal de reuniones pasadas, permitir cambiar la fecha
              $('#editar_fecha').prop('readonly', false).attr('type', 'date');
              $('#editar_hora_inicio').val(moment(ev.start).format('HH:mm'));
              $('#editar_hora_fin').val(ev.end ? moment(ev.end).format('HH:mm') : '');
              $('#editar_descripcion').val(ev.descripcion || '');
              $('#editar_ubicacion').val(ev.ubicacion || '');
              $('#editar_observaciones').val(ev.observaciones || '');
              // Prellenar usuario_id en el formulario de edición (necesario para la validación en el modelo)
              if ($('#editar_usuario_id').length === 0) {
                formEditar.append('<input type="hidden" id="editar_usuario_id" name="usuario_id">');
              }
              $('#editar_usuario_id').val(ev.usuario_id || '');
              // Preseleccionar cliente
              var clienteSelect = $('#editar_cliente_id');
              var clienteId = ev.cliente_id;
              if (clienteId) {
                // Asegurarse de no llamar .trim() sobre valores que no son strings
                var clienteNombreSafe = (ev.cliente_nombre !== undefined && ev.cliente_nombre !== null) ? String(ev.cliente_nombre) : '';
                // Si el nombre es vacío, o es igual al id (p.ej. '80'), forzar fallback AJAX
                var looksLikeId = /^[0-9]+$/.test(clienteNombreSafe) && clienteNombreSafe === String(clienteId);
                if (clienteNombreSafe.trim() !== '' && !looksLikeId) {
                  var newOption = new Option(clienteNombreSafe, clienteId, true, true);
                  clienteSelect.empty().append(newOption).trigger('change');
                  clienteSelect.prop('disabled', true).trigger('change.select2');
                } else {
                  // Fallback: solicitar nombre del cliente vía AJAX si no viene en el evento
                  $.ajax({
                    url: 'ajax/clientes.ajax.php',
                    type: 'POST',
                    data: { idCliente: clienteId },
                    dataType: 'json',
                    success: function(resp) {
                      var nombre = (resp && resp.nombre) ? resp.nombre : clienteId;
                      var newOption = new Option(nombre, clienteId, true, true);
                      clienteSelect.empty().append(newOption).trigger('change');
                      clienteSelect.prop('disabled', true).trigger('change.select2');
                    },
                    error: function() {
                      var newOption = new Option(clienteId, clienteId, true, true);
                      clienteSelect.empty().append(newOption).trigger('change');
                      clienteSelect.prop('disabled', true).trigger('change.select2');
                    }
                  });
                }
              }
              $('#modalDetalleReunion').modal('show');
            } else {
              alert('No se pudo cargar datos de la reunión para editar.');
            }
          }, 600);
        });
      });
    });
  }

  function checkAndOpenPastModal() {
    $.post('ajax/calendario.ajax.php', { accion: 'listar_pasadas' }, function(resp){
      try {
        var j = typeof resp === 'string' ? JSON.parse(resp) : resp;
        var rows = j.eventos || [];
        console.log('checkAndOpenPastModal: filas recibidas =', rows.length, rows);
        // Mostrar modal automáticamente sólo una vez por sesión
        try {
          if (rows.length > 0) {
            renderReunionesPasadas(rows);
            $('#modalReunionesPasadas').modal('show');
          }
        } catch(se){ console.warn('Error mostrando modal pasadas', se); }
      } catch(e){ console.error('Error parse listar_pasadas', e); }
    });
  }

  // Abrir interfaz completa del Historial de reuniones desde el botón
  $('#btnReunionesArchivadas').off('click').on('click', function(){
    window.location = window.BASE_URL + '/reuniones-archivadas';
  });

  // Ejecutar check en carga (se ejecuta después de cargar eventos por fullCalendar cuando se dispara calEventsUpdated)
  document.addEventListener('calEventsUpdated', function(){ try { checkAndOpenPastModal(); } catch(e){ console.error(e); } });
  // También ejecutarlo inmediatamente una vez
  setTimeout(function(){ checkAndOpenPastModal(); }, 1500);

  // ========== Alarmas 10/5 minutos (scheduler) ==========
  (function setupAlarms(){
    // Preferencia por defecto: sonido activado
    if (localStorage.getItem('notif_sound') === null) {
      localStorage.setItem('notif_sound', '1');
    }
    var SOUND_ENABLED = localStorage.getItem('notif_sound') === '1';

    // Rutas locales por defecto (puedes reemplazarlas por ficheros en /vistas/audio/)
    var audioUrl10 = window.BASE_URL + '/vistas/audio/alert-10.mp3';
    var audioUrl5  = window.BASE_URL + '/vistas/audio/alert-5.mp3';

    // Intentar usar objetos Audio (si los archivos existen en el servidor)
    var audio10 = new Audio(audioUrl10);
    var audio5  = new Audio(audioUrl5);
    // Volumen por defecto más alto (mensaje del usuario)
    audio10.volume = 1.0; audio5.volume = 1.0;

    // Fallback WebAudio beep generator
    var AudioContext = window.AudioContext || window.webkitAudioContext;
    var audioCtx = AudioContext ? new AudioContext() : null;
    function playBeep(freq, duration, gainVal){
      if (!audioCtx) return;
      var osc = audioCtx.createOscillator();
      var gain = audioCtx.createGain();
      osc.type = 'sine';
      osc.frequency.value = freq;
      gain.gain.value = typeof gainVal === 'number' ? gainVal : 0.15;
      osc.connect(gain);
      gain.connect(audioCtx.destination);
      // start/stop precise scheduling
      var now = audioCtx.currentTime;
      osc.start(now);
      osc.stop(now + (duration / 1000));
    }

    function requestNotificationPermission(){
      if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission().then(function(p){ console.log('Notification permission', p); });
      }
      // Resume audio context on user gesture (some browsers require this)
      try { if (audioCtx && audioCtx.state === 'suspended') audioCtx.resume(); } catch(e){}
    }

    function showSystemNotification(title, body){
      if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(title, { body: body, icon: window.BASE_URL + '/vistas/img/notification_icon.png' });
      } else if (typeof Swal !== 'undefined') {
        Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: title, text: body, timer: 5000, showConfirmButton: false });
      } else {
        console.log('Notif:', title, body);
      }
    }

    var _audioErrorLogged = false;

    // Reproducir un objeto Audio varias veces (repeats) con pequeño espacio (gapMs)
    function playAudioRepeated(audioObj, repeats, gapMs){
      if (!audioObj || !audioObj.play) return;
      try {
        try { audioObj.volume = 1.0; } catch(e){}
      } catch(e){}
      var count = 0;
      var playOnce = function(){
        // Leer preferencia en tiempo real
        if (localStorage.getItem('notif_sound') !== '1') return;
        var p = audioObj.play();
        count++;
        if (p && p.then) {
          p.then(function(){
            // Programar siguiente reproducción cuando termine o con gap
            if (count < repeats) {
              // intentar usar evento 'ended' if available
              var onEnded = function(){
                audioObj.removeEventListener('ended', onEnded);
                setTimeout(playOnce, gapMs);
              };
              audioObj.addEventListener('ended', onEnded);
              // Fallback: si no se dispara 'ended' en X ms, forzar siguiente
              setTimeout(function(){
                try { audioObj.removeEventListener('ended', onEnded); } catch(e){}
                if (count < repeats) setTimeout(playOnce, gapMs);
              }, 3000);
            }
          }).catch(function(err){
            if (!_audioErrorLogged) console.warn('Audio play blocked, fallback to WebAudio', err);
            _audioErrorLogged = true;
            // Fallback to beeps repeated
            playBeepSequenceRepeated(repeats, gapMs);
          });
        } else {
          // Older browsers may not return a promise
          if (count < repeats) setTimeout(playOnce, gapMs + 800);
        }
      };
      // Start first play
      playOnce();
    }

    // Repetir la secuencia de beeps fallback (dual-tone) N veces
    function playBeepSequenceRepeated(repeats, gapMs){
      if (!audioCtx) return;
      var played = 0;
      function once(){
        if (localStorage.getItem('notif_sound') !== '1') return;
        // Dual-tone
        playBeep(880, 500, 0.2);
        setTimeout(function(){ playBeep(1320, 300, 0.18); }, 150);
        played++;
        if (played < repeats) setTimeout(once, 500 + gapMs);
      }
      once();
    }

    function playSound(audioObj){
      // Leer preferencia en tiempo real desde localStorage
      var enabled = localStorage.getItem('notif_sound') === '1';
      if (!enabled) return;
      try {
        var hasSource = audioObj && (audioObj.currentSrc || audioObj.src);
        if (hasSource && audioObj.play) {
          // Reproducir 3 veces con 400ms de separación
          playAudioRepeated(audioObj, 3, 400);
        } else {
          if (!_audioErrorLogged) console.warn('Audio element has no supported sources, using WebAudio fallback');
          _audioErrorLogged = true;
          playBeepSequenceRepeated(3, 400);
        }
      } catch(e) {
        if (!_audioErrorLogged) console.warn('Error en playSound, usando fallback', e);
        _audioErrorLogged = true;
        playBeepSequenceRepeated(3, 400);
      }
    }

    // Scheduler: revisar events cache cada 30s
    var ALARM_INTERVAL = 30 * 1000;

    function runAlarmCheck(){
      var events = window._calEvents || [];
      if (!events || events.length === 0) return;
      var now = moment();
      events.forEach(function(ev){
        try {
          // normalizar start como moment
          var evStart = moment(ev.start);
          var diffMin = evStart.diff(now, 'minutes', true);

          // Preferir la notificación de 5 minutos si aplica (evita que se muestre también la de 10)
          if (diffMin > 0) {
            // 5-minute notification: trigger when <=5 minutes left (and still in future)
            if (diffMin <= 5 && !ev._notified5) {
              ev._notified5 = true;
              // Marcar también la notificación de 10 como ya enviada para evitar duplicados
              ev._notified10 = true;
              showSystemNotification('Reunión en menos de 5 minutos', ev.title || 'Tienes una reunión próxima');
              playSound(audio5);
            }
            // 10-minute notification: trigger only cuando está entre 10 y 5 minutos
            else if (diffMin <= 10 && diffMin > 5 && !ev._notified10) {
              ev._notified10 = true;
              showSystemNotification('Reunión en menos de 10 minutos', ev.title || 'Tienes una reunión próxima');
              playSound(audio10);
            }
          }
        } catch(e) { console.error('Alarm check error', e); }
      });
    }

    // Ejecutar check periódicamente
    setInterval(runAlarmCheck, ALARM_INTERVAL);
    // Ejecutar check cuando los eventos son actualizados (para notificar inmediatamente si hay reuniones cercanas al abrir la página)
    document.addEventListener('calEventsUpdated', function(){ try { runAlarmCheck(); } catch(e){ console.error(e); } });
    // Ejecutar un chequeo inmediato al inicializar las alarmas (por si hay reuniones próximas al abrir la página)
    try { runAlarmCheck(); } catch(e){ console.error('Initial alarm check failed', e); }

    // Añadir sólo el toggle de sonido dentro del desplegable del perfil (sin botón de prueba)
    function addSoundToggleInProfile(){
      // Buscar el menú desplegable del usuario
      var profileDropdownMenu = document.querySelector('.dropdown.user.user-menu .dropdown-menu');
      if (!profileDropdownMenu) return;

      // Evitar insertar el toggle más de una vez
      if (profileDropdownMenu.querySelector('.sound-toggle-row')) return;

      // Eliminar otras filas previas en caso de duplicados por ejecuciones anteriores
      var prev = document.querySelectorAll('.sound-toggle-row');
      prev.forEach(function(n){ if (n && n.parentNode) n.parentNode.removeChild(n); });

      // Crear fila contenedora
      var row = document.createElement('div');
      row.className = 'sound-toggle-row';
      row.style.padding = '8px 12px';
      row.style.display = 'flex';
      row.style.alignItems = 'center';
      row.style.justifyContent = 'space-between';

      var label = document.createElement('span');
      label.textContent = 'Sonido:';
      label.style.marginRight = '8px';

      var btnToggle = document.createElement('button');
      btnToggle.className = 'btn btn-xs';
      function updateToggle(){
        // Si no existe la preferencia, establecer ON por defecto
        if (localStorage.getItem('notif_sound') === null) {
          localStorage.setItem('notif_sound', '1');
        }
        var on = localStorage.getItem('notif_sound') === '1';
        btnToggle.textContent = on ? 'ON' : 'OFF';
        btnToggle.className = on ? 'btn btn-xs btn-success' : 'btn btn-xs btn-danger';
      }
      btnToggle.onclick = function(){
        var on = localStorage.getItem('notif_sound') === '1';
        localStorage.setItem('notif_sound', on ? '0' : '1');
        updateToggle();
      };

      // Añadir elementos al row y al dropdown
      row.appendChild(label);
      row.appendChild(btnToggle);
      // Insertar al principio del dropdown para visibilidad
      profileDropdownMenu.insertBefore(row, profileDropdownMenu.firstChild);
      updateToggle();
    }
    addSoundToggleInProfile();

  })();

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

      // Normalizar cadenas: trim, colapsar espacios y lowercase para comparación tolerante
      function normalize(s) {
        if (!s) return '';
        return s.toString().replace(/^\s+|\s+$/g, '').replace(/\s+/g, ' ').toLowerCase();
      }

      var tituloNorm = normalize(tituloSinHora);
      var actividadNorm = normalize(actividad);

      // Comparar de forma tolerante: igualdad o inclusión (por si hay diferencias menores)
      if (eventClienteId == clienteId && (tituloNorm === actividadNorm || tituloNorm.indexOf(actividadNorm) !== -1 || actividadNorm.indexOf(tituloNorm) !== -1)) {
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
