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
    var tipoSeleccionado = $('#nuevoTipo').val();
    var documentoVal = $('#nuevoDocumento').val();
    var telefonoVal = $('#nuevoTelefono').val();

    var regexDNI = /^\d{8}$/;
    var regexRUC = /^\d{11}$/;
    var regexOtros = /^\d{8}$/;
    var regexTelefono = /^\d{1,11}$/; // Solo dígitos, máximo 11

    // Validar documento
    if (tipoSeleccionado === 'DNI') {
      if (!regexDNI.test(documentoVal)) {
        e.preventDefault();
        $('#nuevoDocumento').addClass('is-invalid');
        alert('El documento debe tener exactamente 8 dígitos numéricos para DNI.');
        return;
      } else {
        $('#nuevoDocumento').removeClass('is-invalid');
      }
    } else if (tipoSeleccionado === 'RUC') {
      if (!regexRUC.test(documentoVal)) {
        e.preventDefault();
        $('#nuevoDocumento').addClass('is-invalid');
        alert('El documento debe tener exactamente 11 dígitos numéricos para RUC.');
        return;
      } else {
        $('#nuevoDocumento').removeClass('is-invalid');
      }
    } else if (tipoSeleccionado === 'otros') {
      if (!regexOtros.test(documentoVal)) {
        e.preventDefault();
        $('#nuevoDocumento').addClass('is-invalid');
        alert('El documento debe tener exactamente 8 dígitos numéricos para otros.');
        return;
      } else {
        $('#nuevoDocumento').removeClass('is-invalid');
      }
    }

    // Validar teléfono: contar solo dígitos, ignorar espacios
    var telefonoDigits = telefonoVal.replace(/\s/g, '');
    if (!regexTelefono.test(telefonoDigits)) {
      e.preventDefault();
      $('#nuevoTelefono').addClass('is-invalid');
      alert('El teléfono debe contener solo dígitos y un máximo de 11 dígitos.');
      return;
    } else {
      $('#nuevoTelefono').removeClass('is-invalid');
    }
  });
});
