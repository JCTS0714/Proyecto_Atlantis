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
  var nuevoEstado = estadoCliente === 1 ? 0 : 1;

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
            if (nuevoEstado === 1) {
              $btn.removeClass("btn-warning").addClass("btn-success").html("Cliente").attr("estadoCliente", 1);
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

// Manejador para botón editar cliente
$(document).on("click", ".btnEditarCliente", function() {
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
      $("#editarTipo").val(respuesta["tipo"]);
      $("#editarDocumento").val(respuesta["documento"]);
      $("#editarTelefono").val(respuesta["telefono"]);
      $("#editarCorreo").val(respuesta["correo"]);
      $("#editarCiudad").val(respuesta["ciudad"]);
      $("#editarMigracion").val(respuesta["migracion"]);
      $("#editarReferencia").val(respuesta["referencia"]);
      $("#editarFechaContacto").val(respuesta["fecha_contacto"]);
      $("#editarEmpresa").val(respuesta["empresa"]);
      $("#editarFechaCreacion").val(respuesta["fecha_creacion"]);
      $("#modalActualizarClientes").modal("show");
    }
  });
});

// Manejador para botón eliminar cliente o prospecto
$(document).off("click", ".btnEliminarCliente").on("click", ".btnEliminarCliente", function() {
  var idCliente = $(this).attr("idCliente");
  var ruta = $(this).data("ruta") || "clientes";

  Swal.fire({
    title: '¿Está seguro de eliminar el cliente?',
    text: "Esta acción no se puede deshacer.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'No, cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location = "index.php?ruta=" + ruta + "&idClienteEliminar=" + idCliente;
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
  if (correoSelector.length === 0) {
    // No hay campo correo, no validar
    return true;
  }
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
  var soloNumeros = /^[0-9]{9}$/; // Exige exactamente 9 dígitos
  if (telefono !== "" && !soloNumeros.test(telefono)) {
    alert("El teléfono debe tener exactamente 9 dígitos numéricos.");
    return false;
  }
  return true;
}

// Bloquear entrada de no números en campos de teléfono
$("input[name='nuevoTelefono'], input[name='editarTelefono']").on("input", function(e) {
  // Reemplazar cualquier caracter no numérico con vacío
  $(this).val($(this).val().replace(/[^0-9]/g, ""));
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

// Validar solo números en campos teléfono nuevo y editar
$("input[name='nuevoTelefono'], input[name='editarTelefono']").on("keypress", function(e) {
  var charCode = e.which ? e.which : e.keyCode;
  if (charCode < 48 || charCode > 57) {
    e.preventDefault();
  }
});

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
