$(document).ready(function() {
  $('#nuevoTipo').on('change', function() {
    var tipoSeleccionado = $(this).val();
    var documentoInput = $('#nuevoDocumento');

    // Ajustar maxlength y placeholder según tipo seleccionado
    if (tipoSeleccionado === 'DNI') {
      documentoInput.attr('placeholder', 'Ingrese 8 dígitos');
      documentoInput.attr('maxlength', '8');
      documentoInput.removeClass('is-invalid');
    } else if (tipoSeleccionado === 'RUC') {
      documentoInput.attr('placeholder', 'Ingrese 11 dígitos');
      documentoInput.attr('maxlength', '11');
      documentoInput.removeClass('is-invalid');
    } else if (tipoSeleccionado === 'otros') {
      documentoInput.attr('placeholder', 'Ingrese 8 dígitos');
      documentoInput.attr('maxlength', '8');
      documentoInput.removeClass('is-invalid');
    } else {
      documentoInput.attr('placeholder', 'Ingresar documento');
      documentoInput.removeAttr('maxlength');
      documentoInput.removeClass('is-invalid');
    }
  });

  // Validación al enviar el formulario
  $('form[role="form"]').on('submit', function(e) {
    var $tipo = $('#nuevoTipo');
    var $documento = $('#nuevoDocumento');
    var $telefono = $('#nuevoTelefono');

    var tipoSeleccionado = $tipo.length ? $tipo.val() : null;
    var documentoVal = $documento.length ? ($documento.val() || '') : '';
    var telefonoVal = $telefono.length ? ($telefono.val() || '') : '';

    var regexDNI = /^\d{8}$/;
    var regexRUC = /^\d{11}$/;
    var regexOtros = /^\d{8}$/;
    var regexTelefono = /^\d{1,11}$/; // Solo dígitos, máximo 11

    // Validar documento (solo si el campo existe en el formulario)
    if ($documento.length && tipoSeleccionado) {
      if (tipoSeleccionado === 'DNI') {
        if (!regexDNI.test(documentoVal)) {
          e.preventDefault();
          $documento.addClass('is-invalid');
          alert('El documento debe tener exactamente 8 dígitos numéricos para DNI.');
          return;
        } else {
          $documento.removeClass('is-invalid');
        }
      } else if (tipoSeleccionado === 'RUC') {
        if (!regexRUC.test(documentoVal)) {
          e.preventDefault();
          $documento.addClass('is-invalid');
          alert('El documento debe tener exactamente 11 dígitos numéricos para RUC.');
          return;
        } else {
          $documento.removeClass('is-invalid');
        }
      } else if (tipoSeleccionado === 'otros') {
        if (!regexOtros.test(documentoVal)) {
          e.preventDefault();
          $documento.addClass('is-invalid');
          alert('El documento debe tener exactamente 8 dígitos numéricos para otros.');
          return;
        } else {
          $documento.removeClass('is-invalid');
        }
      }
    }

    // Validar teléfono: contar solo dígitos, ignorar espacios (solo si existe)
    if ($telefono.length) {
      var telefonoDigits = telefonoVal.replace(/\s/g, '');
      if (!regexTelefono.test(telefonoDigits)) {
        e.preventDefault();
        $telefono.addClass('is-invalid');
        alert('El teléfono debe contener solo dígitos y un máximo de 11 dígitos.');
        return;
      } else {
        $telefono.removeClass('is-invalid');
      }
    }
  });
});
