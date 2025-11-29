// JS para manejar abrir modal de edición y cargar datos via AJAX
$(document).off('click', '.btnEditarContador').on('click', '.btnEditarContador', function() {
  var id = $(this).data('id');
  if (!id) return;
  $.ajax({
    url: 'ajax/contadores.ajax.php',
    method: 'POST',
    data: { idContador: id },
    dataType: 'json'
  }).done(function(data) {
    if (!data || !data.id) return;
    $('#idContador').val(data.id);
    $('#editarNro').val(data.nro);
    $('#editarComercio').val(data.comercio);
    $('#editarNomContador').val(data.nom_contador);
    $('#editarTitularTlf').val(data.titular_tlf);
    $('#editarTelefono').val(data.telefono);
    $('#editarTelefonoActu').val(data.telefono_actu);
    $('#editarLink').val(data.link);
    $('#editarUsuario').val(data.usuario);
    $('#editarContrasena').val(data.contrasena);
  }).fail(function() {
    console.warn('No se pudo cargar contador');
  });
});

// Inicializar DataTable si existe
$(document).ready(function() {
  if ($('#tablaContadores').length) {
    try { $('#tablaContadores').DataTable(); } catch(e) { /* ignore */ }
  }
  // Solicitar el próximo N° y mostrarlo en el modal crear
  try {
    $.ajax({
      url: 'ajax/contadores.ajax.php',
      method: 'GET',
      data: { next_nro: 1 },
      dataType: 'json'
    }).done(function(resp) {
      if (resp && resp.next_nro) {
        $('#nuevoNro').val(resp.next_nro);
      }
    }).fail(function(){ /* ignore */ });
  } catch(e) {}
});
