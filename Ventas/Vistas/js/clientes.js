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

// Manejador para botón eliminar cliente
$(document).off("click", ".btnEliminarCliente").on("click", ".btnEliminarCliente", function() {
  var idCliente = $(this).attr("idCliente");

  Swal.fire({
    title: '¿Está seguro de eliminar el cliente?',
    text: "Esta acción no se puede deshacer.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'No, cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location = "index.php?ruta=clientes&idClienteEliminar=" + idCliente;
    }
  });
});

// Validar formulario agregar cliente/prospecto, excluyendo el formulario de nueva oportunidad
$("form").not("#form-nueva-oportunidad").on("submit", function(e) {
  // Solo aplicar validaciones en formularios específicos
  var formAction = $(this).attr('action') || window.location.href;
  
  // Verificar si el formulario es de clientes o prospectos
  var isClientesForm = formAction.indexOf('clientes') !== -1;
  var isProspectosForm = formAction.indexOf('prospectos') !== -1;
  var isModalForm = $(this).closest('.modal').length > 0;
  
  // Solo aplicar validaciones en formularios de clientes/prospectos
  if ((isClientesForm || isProspectosForm || isModalForm) && !$(this).hasClass('login-form')) {
    var tipoSelector = $(this).find("select[name='nuevoTipo'], select[name='editarTipo']");
    var documentoSelector = $(this).find("input[name='nuevoDocumento'], input[name='editarDocumento']");
    var telefonoSelector = $(this).find("input[name='nuevoTelefono'], input[name='editarTelefono']");
    var correoSelector = $(this).find("input[name='nuevoCorreo'], input[name='editarCorreo']");

    if (!validarDocumento(tipoSelector, documentoSelector) || !validarTelefono(telefonoSelector) || !validarCorreo(correoSelector)) {
      e.preventDefault();
    }
  }
});

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

// Validación para campos DNI y RUC en formularios agregar y editar
function validarDocumento(tipoSelector, documentoSelector) {
  var tipo = $(tipoSelector).val();
  var documento = $(documentoSelector).val();

  var soloNumeros = /^[0-9]+$/;

  if (tipo === "DNI") {
    if (!soloNumeros.test(documento) || documento.length !== 8) {
      alert("El DNI debe tener 8 dígitos numéricos.");
      return false;
    }
  } else if (tipo === "RUC") {
    if (!soloNumeros.test(documento) || documento.length !== 11) {
      alert("El RUC debe tener 11 dígitos numéricos.");
      return false;
    }
  }
  return true;
}
