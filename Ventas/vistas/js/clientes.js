console.log('Archivo clientes.js cargado correctamente');

// Verificar que jQuery esté disponible
if (typeof $ === 'undefined') {
    console.error('jQuery no está disponible');
} else {
    console.log('jQuery está disponible, versión:', $.fn.jquery);
}

$(document).off("click", ".btnEstadoCliente").on("click", ".btnEstadoCliente", function(event) {
  event.preventDefault();
  console.log("Click en botón estado cliente/prospecto");
  var $btn = $(this);
  var idCliente = $btn.attr("idCliente");
  var estadoCliente = parseInt($btn.attr("estadoCliente"));

  if (!idCliente || isNaN(estadoCliente)) {
    console.error("Atributos idCliente o estadoCliente inválidos en el botón.");
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
    console.log("Resultado SweetAlert:", result);
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
          console.log("Respuesta ajax:", respuesta);
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
  console.log("Click en botón editar cliente detectado"); // Log para verificar evento
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
      console.log("Respuesta ajax editar cliente:", respuesta); // Log para diagnóstico
      $("#idCliente").val(respuesta["id"]);
      $("#editarNombre").val(respuesta["nombre"]);

      // Asignar valor al select tipo, permitiendo cualquier valor que venga
      if (respuesta["tipo"]) {
        $("#editarTipo").val(respuesta["tipo"]);
      } else {
        $("#editarTipo").val("");
      }

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
      $("#editarCorreo").attr("placeholder", "Ingresar correo");
      $("#editarCiudad").attr("placeholder", "Ingresar ciudad");
      $("#editarMigracion").attr("placeholder", "Ingresar migración");
      $("#editarReferencia").attr("placeholder", "Ingresar referencia");
      $("#editarFechaContacto").attr("placeholder", "Ingresar fecha de contacto");
      $("#editarEmpresa").attr("placeholder", "Ingresar empresa");

      $("#modalActualizarClientes").modal("show");
    }
  });
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

// Validar correo: puede estar vacío o ser un email válido
function validarCorreo(correoSelector) {
  var correo = $(correoSelector).val();
  if (correo === "") {
    return true;
  }
  var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(correo)) {
    alert("El correo electrónico no es válido.");
    return false;
  }
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

  // Redirigir a la página de incidencias con parámetros
  window.location.href = "index.php?ruta=incidencias&idCliente=" + idCliente + "&nombreCliente=" + encodeURIComponent(nombreCliente);
});

// Inicializar DataTable para la tabla de clientes
$('#tablaClientes').DataTable({
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
