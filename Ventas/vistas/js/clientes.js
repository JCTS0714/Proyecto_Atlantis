// ========================================
// ARCHIVO CLIENTES.JS
// ========================================

// console.log('Archivo clientes.js cargado correctamente');
// console.log('jQuery está disponible, versión:', $.fn.jquery);

$(document).off("click", ".btnEstadoCliente").on("click", ".btnEstadoCliente", function(event) {
  event.preventDefault();
  var $btn = $(this);
  var idCliente = $btn.attr("idCliente");
  var estadoCliente = parseInt($btn.attr("estadoCliente"));

  if (!idCliente || isNaN(estadoCliente)) {
    return;
  }

  // Calcula el nuevo estado
  // Prospecto (0) -> Cliente (2)
  // Cliente/Seguimiento (1 o 2) -> Prospecto (0)
  var nuevoEstado = estadoCliente === 0 ? 2 : 0;

  // Confirmación con SweetAlert
  Swal.fire({
    title: '¿Está seguro de cambiar el estado?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, cambiar',
    cancelButtonText: 'No, cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      // Envía el nuevo estado al backend
      var datos = new FormData();
      datos.append("activarId", idCliente);
      datos.append("activarEstado", nuevoEstado);

      $.ajax({
        url: "ajax/clientes.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function(respuesta) {
          if (respuesta == "ok" || respuesta == 1) {
            // Actualiza el botón y el atributo para reflejar el nuevo estado
            if (nuevoEstado === 2) {
              $btn.removeClass("btn-warning").addClass("btn-success").html("Cliente").attr("estadoCliente", 2);
            } else {
              $btn.removeClass("btn-success").addClass("btn-warning").html("Prospecto").attr("estadoCliente", 0);
            }
            // Recargar la página para reflejar cambios en la vista
            location.reload();
          } else {
            alert("Error al actualizar el estado. Intente nuevamente.");
          }
        },
        error: function() {
          alert("Error en la comunicación con el servidor.");
        }
      });
    }
  });
});

$(document).off("click", ".btnEditarCliente").on("click", ".btnEditarCliente", function() {
  var idCliente = $(this).attr("idCliente");
  var datos = new FormData();
  datos.append("idCliente", idCliente);

  $.ajax({
    url: "ajax/clientes.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function(respuesta) {
      if (!respuesta || !respuesta["id"]) {
        alert("Error: No se pudieron cargar los datos del cliente");
        return;
      }

      $("#idCliente").val(respuesta["id"]);
      $("#editarNombre").val(respuesta["nombre"]);
      $("#editarTipo").val(respuesta["tipo"] || "");
      $("#editarDocumento").val(respuesta["documento"]);
      $("#editarTelefono").val(respuesta["telefono"]);
      $("#editarCorreo").val(respuesta["correo"]);
      $("#editarCiudad").val(respuesta["ciudad"]);
      $("#editarMigracion").val(respuesta["migracion"]);
      $("#editarReferencia").val(respuesta["referencia"]);
      $("#editarFechaContacto").val(respuesta["fecha_contacto"]);
      $("#editarEmpresa").val(respuesta["empresa"]);
      $("#editarFechaCreacion").val(respuesta["fecha_creacion"]);

      // Rellenar placeholders con texto respectivo
      $("#editarNombre").attr("placeholder", "Ingresar nombre");
      $("#editarTipo").attr("placeholder", "Seleccionar tipo");
      $("#editarDocumento").attr("placeholder", "Ingresar documento");
      $("#editarTelefono").attr("placeholder", "Ingresar teléfono");
      $("#editarCorreo").attr("placeholder", "Ingresar Observacion");
      $("#editarCiudad").attr("placeholder", "Ingresar ciudad");
      $("#editarMigracion").attr("placeholder", "Ingresar migración");
      $("#editarReferencia").attr("placeholder", "Ingresar referencia");
      $("#editarFechaContacto").attr("placeholder", "Ingresar fecha de contacto");
      $("#editarEmpresa").attr("placeholder", "Ingresar empresa");

      console.log("Mostrando modal");
      $("#modalActualizarClientes").modal("show");
    },
    error: function(xhr, status, error) {
      console.error("Error en AJAX:", status, error);
      console.error("Respuesta:", xhr.responseText);
      alert("Error al cargar los datos del cliente");
    }
  });
});

// ========================================
// MANEJADOR PARA ELIMINAR CLIENTE
// ========================================
$(document).off("click", ".btnEliminarCliente").on("click", ".btnEliminarCliente", function(e) {
  e.preventDefault();
  console.log("Click en botón eliminar cliente detectado");
  var idCliente = $(this).attr("idCliente");
  var dataRuta = $(this).attr("data-ruta");
  
  if (!idCliente) {
    console.error("ID de cliente no encontrado");
    alert("Error: ID de cliente no encontrado");
    return;
  }

  console.log("ID Cliente:", idCliente, "Ruta:", dataRuta);

  // Confirmación con SweetAlert
  if (typeof Swal === 'undefined') {
    // Si Swal no está disponible, usar confirm tradicional
    if (!confirm('¿Está seguro de que desea eliminar este cliente?')) {
      return;
    }
    var datos = new FormData();
    datos.append("idCliente", idCliente);
    datos.append("ruta", dataRuta || "clientes");

    $.ajax({
      url: "ajax/clientes.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      success: function(respuesta) {
        console.log("Respuesta eliminar cliente:", respuesta);
        if (respuesta == "ok" || respuesta == 1) {
          alert('El cliente ha sido eliminado correctamente.');
          location.reload();
        } else {
          alert('Error al eliminar el cliente: ' + respuesta);
        }
      },
      error: function(xhr, status, error) {
        console.error("Error AJAX:", error);
        alert('Error en la comunicación con el servidor.');
      }
    });
  } else {
    // Usar SweetAlert si está disponible
    Swal.fire({
      title: '¿Está seguro?',
      text: "¡Esta acción no se puede deshacer!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        var datos = new FormData();
        datos.append("idCliente", idCliente);
        datos.append("ruta", dataRuta || "clientes");

        $.ajax({
          url: "ajax/clientes.ajax.php",
          method: "POST",
          data: datos,
          cache: false,
          contentType: false,
          processData: false,
          success: function(respuesta) {
            console.log("Respuesta eliminar cliente:", respuesta);
            if (respuesta == "ok" || respuesta == 1) {
              Swal.fire({
                title: 'Eliminado',
                text: 'El cliente ha sido eliminado correctamente.',
                icon: 'success',
                confirmButtonText: 'OK'
              }).then(() => {
                location.reload();
              });
            } else {
              Swal.fire({
                title: 'Error',
                text: 'Error al eliminar el cliente: ' + respuesta,
                icon: 'error'
              });
            }
          },
          error: function(xhr, status, error) {
            console.error("Error AJAX:", error);
            Swal.fire({
              title: 'Error',
              text: 'Error en la comunicación con el servidor.',
              icon: 'error'
            });
          }
        });
      }
    });
  }
});

// Manejador para botón Reactivar en zona de espera
$(document).off('click', '.btnReactivarCliente').on('click', '.btnReactivarCliente', function() {
  console.log('Click detectado en botón Reactivar');
  var idCliente = $(this).attr('idCliente');
  console.log('ID Cliente obtenido:', idCliente);

  if (!idCliente) {
    console.error('ID de cliente no encontrado en el botón');
    alert('ID de cliente no encontrado');
    return;
  }

  console.log('Mostrando confirmación al usuario');
  if (!confirm('¿Está seguro que desea reactivar este cliente y pasarlo a seguimiento?')) {
    console.log('Usuario canceló la confirmación');
    return;
  }

  console.log('Buscando oportunidad para cliente ID:', idCliente);

  // Primero buscar la oportunidad del cliente
  $.ajax({
    url: 'ajax/oportunidades.ajax.php?action=getOportunidadByCliente',
    method: 'GET',
    data: {
      clienteId: idCliente
    },
    dataType: 'json',
    success: function(oportunidadResponse) {
      console.log('Respuesta búsqueda oportunidad:', oportunidadResponse);

      if (oportunidadResponse && oportunidadResponse.length > 0) {
        var idOportunidad = oportunidadResponse[0].id;
        console.log('ID Oportunidad encontrado:', idOportunidad);

        console.log('Enviando petición AJAX para cambiar estado con datos:', {
          idOportunidad: idOportunidad,
          nuevoEstado: 1
        });

        // Ahora cambiar el estado de la oportunidad
        $.ajax({
          url: 'ajax/oportunidades.ajax.php?action=cambiarEstado',
          method: 'POST',
          data: {
            idOportunidad: idOportunidad,
            nuevoEstado: 1 // Estado seguimiento
          },
          dataType: 'json',
          success: function(response) {
            console.log('Respuesta AJAX cambio estado exitosa:', response);
            if (response.status === 'success') {
              console.log('Estado actualizado correctamente, recargando página');
              alert('Cliente reactivado correctamente');
              // Recargar la página o tabla para reflejar cambios
              location.reload();
            } else {
              console.error('Error en respuesta:', response.message);
              alert('Error al reactivar cliente: ' + response.message);
            }
          },
          error: function(xhr, status, error) {
            console.error('Error AJAX cambio estado:', {
              xhr: xhr,
              status: status,
              error: error,
              responseText: xhr.responseText
            });
            alert('Error en la petición AJAX: ' + error);
          }
        });
      } else {
        console.error('No se encontró oportunidad para el cliente');
        alert('No se encontró la oportunidad asociada al cliente');
      }
    },
    error: function(xhr, status, error) {
      console.error('Error AJAX búsqueda oportunidad:', {
        xhr: xhr,
        status: status,
        error: error,
        responseText: xhr.responseText
      });
      alert('Error al buscar la oportunidad del cliente: ' + error);
    }
  });
});

// Validar formulario agregar cliente/prospecto, excluyendo el formulario de nueva oportunidad y el formulario de login
$("#modalActualizarClientes form").not("#form-nueva-oportunidad, .login-box-body form").on("submit", function(e) {
  var tipoSelector = $(this).find("select[name='nuevoTipo'], select[name='editarTipo']");
  var documentoSelector = $(this).find("input[name='nuevoDocumento'], input[name='editarDocumento']");
  var telefonoSelector = $(this).find("input[name='nuevoTelefono'], input[name='editarTelefono']");
  var correoSelector = $(this).find("input[name='nuevoCorreo'], input[name='editarCorreo']");

  // Validar teléfono solo si el campo existe y no está vacío
  if (telefonoSelector.length > 0 && $(telefonoSelector).val() !== "") {
    // Validar teléfono para clientes (modal editar clientes)
    if ($(this).closest("#modalActualizarClientes").length) {
      // Limitar a máximo 9 caracteres sin alerta
      if ($(telefonoSelector).val().length > 9) {
        $(telefonoSelector).val($(telefonoSelector).val().substring(0, 9));
      }

      // Limitar a máximo 9 caracteres en el input editarTelefono en tiempo real
      $("#editarTelefono").on("input", function() {
        var maxLength = 9;
        var valor = $(this).val();
        if (valor.length > maxLength) {
          $(this).val(valor.substring(0, maxLength));
        }
      });
    }
    // Validar teléfono para prospectos
    else if ($(this).hasClass("form-prospecto")) {
      if (!validarTelefonoProspecto(telefonoSelector)) {
        e.preventDefault();
        return;
      }
    }
  }

  if (!validarDocumento(tipoSelector, documentoSelector) || !validarCorreo(correoSelector)) {
    e.preventDefault();
  }
});

// Validar teléfono para prospectos: solo 9 dígitos numéricos
function validarTelefonoProspecto(telefonoSelector) {
  var telefono = $(telefonoSelector).val();
  var soloNumeros = /^[0-9]{9}$/; // Exige exactamente 9 dígitos
  if (telefono !== "" && !soloNumeros.test(telefono)) {
    alert("El teléfono debe tener exactamente 9 dígitos numéricos.");
    return false;
  }
  return true;
}

// Validar Observacion: permitir cualquier texto (no se valida formato de email)
function validarCorreo(correoSelector) {
  // Aceptar vacío o cualquier tipo de texto — validación superficial por diseño
  return true;
}

// Validar teléfono: solo 9 dígitos numéricos
function validarTelefono(telefonoSelector) {
  var telefono = $(telefonoSelector).val();
  var soloNumeros = /^[0-9]{0,9}$/; // Permite vacío o 9 dígitos
  if (telefono !== "" && !soloNumeros.test(telefono)) {
    alert("El teléfono debe tener exactamente 9 dígitos numéricos.");
    return false;
  }
  return true;
}

// Bloquear entrada de no números en campos de teléfono
$("input[name='nuevoTelefono'], input[name='editarTelefono']").on("keypress", function(e) {
  var charCode = e.which ? e.which : e.keyCode;
  if (charCode < 48 || charCode > 57) {
    e.preventDefault();
  }
});

// Función para ajustar maxlength y permitir solo números en campo documento según tipo seleccionado
function configurarValidacionDocumento(tipoSelector, documentoSelector) {
  var $tipo = $(tipoSelector);
  var $documento = $(documentoSelector);

  function ajustar() {
    var tipo = $tipo.val();
    if (tipo === "DNI") {
      $documento.attr("maxlength", 8);
    } else if (tipo === "RUC") {
      $documento.attr("maxlength", 11);
    } else {
      $documento.removeAttr("maxlength");
    }
  }

  function permitirSoloNumeros(e) {
    var charCode = e.which ? e.which : e.keyCode;
    if (charCode < 48 || charCode > 57) {
      e.preventDefault();
    }
  }

  // Ajustar maxlength al cargar
  ajustar();

  // Cambiar maxlength al cambiar tipo
  $tipo.on("change", function() {
    ajustar();
    $documento.val(""); // Limpiar campo documento al cambiar tipo
  });

  // Permitir solo números en documento
  $documento.on("keypress", permitirSoloNumeros);
}

// Configurar validación para modal agregar prospecto
configurarValidacionDocumento("select[name='nuevoTipo']", "input[name='nuevoDocumento']");

// Configurar validación para modal editar prospecto y cliente
configurarValidacionDocumento("select[name='editarTipo']", "input[name='editarDocumento']");

// Ejecutar ajuste inicial al abrir modal editar para que la validación funcione con el valor actual
$('#modalActualizarClientes').on('shown.bs.modal', function () {
  var tipoSelector = "select[name='editarTipo']";
  var documentoSelector = "input[name='editarDocumento']";
  var $tipo = $(tipoSelector);
  var $documento = $(documentoSelector);

  function ajustar() {
    var tipo = $tipo.val();
    if (tipo === "DNI") {
      $documento.attr("maxlength", 8);
    } else if (tipo === "RUC") {
      $documento.attr("maxlength", 11);
    } else {
      $documento.removeAttr("maxlength");
    }
  }

  ajustar();
});

// Manejador para botón Registrar Incidencia
$(document).off("click", ".btnRegistrarIncidencia").on("click", ".btnRegistrarIncidencia", function() {
  var idCliente = $(this).attr("idCliente");
  var nombreCliente = $(this).attr("nombreCliente");

  // Redirigir a la página de incidencias con parámetros (usar BASE_URL + ruta limpia)
  if (typeof window.BASE_URL !== 'undefined') {
    window.location.href = window.BASE_URL + '/incidencias?idCliente=' + encodeURIComponent(idCliente) + '&nombreCliente=' + encodeURIComponent(nombreCliente);
  } else {
    // Fallback relativo
    window.location.href = 'incidencias?idCliente=' + encodeURIComponent(idCliente) + '&nombreCliente=' + encodeURIComponent(nombreCliente);
  }
});

// DataTable initialization moved to plantilla.js (centralized)

// Manejador para selects de cambio de estado en la tabla de clientes (similar a prospectos)
$(document).off('change', '.select-estado-cliente').on('change', '.select-estado-cliente', function() {
  var idCliente = $(this).data('id');
  var nuevoEstado = $(this).val();

  console.log('select-estado-cliente changed', { idCliente: idCliente, nuevoEstado: nuevoEstado });

  if (!idCliente || nuevoEstado === undefined) {
    console.error('select-estado-cliente: parámetros inválidos', {idCliente: idCliente, nuevoEstado: nuevoEstado});
    return;
  }

  // Usar $.post para enviar datos URL-encoded y evitar problemas con FormData en algunos entornos
  $.post('ajax/clientes.ajax.php', { activarId: idCliente, activarEstado: nuevoEstado }, function(respuesta, textStatus, xhr) {
    console.log('Respuesta POST cambiar estado:', { respuesta: respuesta, textStatus: textStatus });

    var res = respuesta;
    if (typeof respuesta === 'string') {
      var trimmed = respuesta.trim();
      if (trimmed === 'ok') {
        res = { status: 'ok' };
      } else {
        try { res = JSON.parse(respuesta); } catch (e) { res = { status: 'error', message: 'Respuesta no válida' }; }
      }
    }

    if (res && res.status && res.status === 'ok') {
      if (typeof Swal !== 'undefined') {
        Swal.fire('Actualizado', 'Estado actualizado correctamente', 'success').then(function() { location.reload(); });
      } else {
        alert('Estado actualizado correctamente');
        location.reload();
      }
    } else {
      var msg = (res && res.message) ? res.message : 'Error al actualizar estado';
      console.warn('Fallo al actualizar estado:', msg, respuesta);
      if (typeof Swal !== 'undefined') {
        Swal.fire('Error', msg, 'error');
      } else {
        alert(msg);
      }
    }
  }).fail(function(xhr, status, err) {
    console.error('POST falló cambiar estado', { status: status, err: err, response: xhr.responseText });
    if (typeof Swal !== 'undefined') {
      Swal.fire('Error', 'No se pudo actualizar el estado (request failed)', 'error');
    } else {
      alert('No se pudo actualizar el estado (request failed)');
    }
  });
});
