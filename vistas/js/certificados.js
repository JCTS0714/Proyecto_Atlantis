$(document).ready(function(){

  // Inicializar DataTable (una sola vez) para la tabla de certificados
  var tablaCertificados = null;
  function initDataTable() {
    if (!$.fn.DataTable || !$('#tablaCertificados').length) return;
    if ($.fn.dataTable.isDataTable('#tablaCertificados')) {
      tablaCertificados = $('#tablaCertificados').DataTable();
      return;
    }
    tablaCertificados = $('#tablaCertificados').DataTable({
      responsive: true,
      paging: true,
      searching: true,
      ordering: true,
      pageLength: 25,
      columnDefs: [{ orderable: false, targets: -1 }],
      data: [],
      // Prevent automatic width adjustments that sometimes hide rows on dynamic updates
      autoWidth: false
    });
  }
  initDataTable();

  // Helper: recargar datos desde el endpoint y reconstruir la tabla
  function reloadTable() {
    console.log('certificados: reloadTable() called, fetching data from server');
    var ajaxUrl = (window.BASE_URL || '') + '/ajax/certificados.ajax.php';
    $.ajax({ url: ajaxUrl, method: 'POST', data: { accion: 'mostrar' }, dataType: 'json' })
    .done(function(resp){
      console.log('certificados: reloadTable response', resp);
      var eventos = (resp && resp.eventos) ? resp.eventos : (resp && resp.data ? resp.data : []);
      var $tbody = $('#tablaCertificados tbody');
      // Build array of rows compatible with DataTables API
      var rows = eventos.map(function(c, idx){
        return [
          (idx+1),
          (c.nombre ? htmlspecialchars(c.nombre) : ''),
          (c.ruc ? htmlspecialchars(c.ruc) : ''),
          (c.usuario ? htmlspecialchars(c.usuario) : ''),
          (c.clave ? htmlspecialchars(c.clave) : ''),
          (c.fecha_creacion ? formatDate(c.fecha_creacion) : '-'),
          (c.fecha_vencimiento ? formatDate(c.fecha_vencimiento) : '-'),
          (c.estado ? htmlspecialchars(c.estado) : ''),
          (c.observacion ? htmlspecialchars(c.observacion).replace(/\n/g, '<br>') : ''),
          (c.tipo ? htmlspecialchars(c.tipo) : '-'),
          '<button class="btn btn-warning btn-sm btnEditarCertificado" data-id="' + c.id + '"><i class="fa fa-pencil"></i></button> ' +
          '<button class="btn btn-danger btn-sm btnEliminarCertificado" data-id="' + c.id + '"><i class="fa fa-trash"></i></button>'
        ];
      });

      // Use DataTables API to update rows (no reinitialisation)
      try{
        initDataTable();
        if (tablaCertificados && typeof tablaCertificados.clear === 'function'){
          tablaCertificados.clear();
          if (rows.length) tablaCertificados.rows.add(rows);
          tablaCertificados.draw(false);
        } else {
          // Fallback: directly replace tbody
          $tbody.empty();
          rows.forEach(function(r){
            var tr = '<tr>' + r.map(function(cell){ return '<td>' + cell + '</td>'; }).join('') + '</tr>';
            $tbody.append(tr);
          });
        }
      }catch(e){ console.error('certificados: error updating DataTable rows', e); }
    }).fail(function(jqxhr, textStatus, errorThrown){
      console.error('certificados: reloadTable AJAX failed', textStatus, errorThrown);
      try{ console.error('certificados: responseText', jqxhr.responseText); }catch(e){}
    });
  }
  // Expose reloadTable globally so other inline scripts can refresh without full reload
  window.certificados_reloadTable = reloadTable;

  // Load initial data into the table
  try{ reloadTable(); }catch(e){ console.warn('certificados: initial reloadTable failed', e); }

  // Small helpers
  function htmlspecialchars(str){ if(!str) return ''; return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
  function formatDate(d){ if(!d) return '-'; return d.split(' ')[0]; }

  // Crear certificado
  // Asegurar que el botón abrirá el modal incluso si data-toggle no funciona
  $(document).on('click', '#btnAgregarCertificado', function(e){
    e.preventDefault();
    console.log('btnAgregarCertificado clicked, attempting to show modal');
    console.log('modal exists? ', $('#modalAgregarCertificado').length);
    if (typeof $.fn.modal !== 'function') {
      console.error('Bootstrap modal plugin not available ($.fn.modal is undefined)');
    } else {
      $('#modalAgregarCertificado').modal('show');
    }
  });

  $(document).on('submit', '#formAgregarCertificado', function(e){
    e.preventDefault();
    var form = $(this)[0];
    var fd = new FormData(form);
    fd.append('accion','crear');
    $.ajax({
      url: 'ajax/certificados.ajax.php',
      method: 'POST',
      data: fd,
      processData: false,
      contentType: false,
      dataType: 'json'
    }).done(function(resp){
      if(resp == 'ok' || (resp && resp.success !== false)){
        $('#modalAgregarCertificado').modal('hide');
        reloadTable();
      } else {
        var msg = (resp && resp.error) ? resp.error : 'Error al crear certificado';
        Swal.fire('Error', msg, 'error');
      }
    }).fail(function(jqxhr){
      var msg = (jqxhr && jqxhr.responseText) ? jqxhr.responseText : 'No se pudo conectar al servidor';
      Swal.fire('Error',msg,'error');
    });
  });

  // Mark that the main certificados submit handler is registered so inline fallbacks don't add duplicate handlers
  try{ window._certificados_has_main_handler = true; }catch(e){ /* ignore */ }

  // Abrir modal editar y cargar datos
  $(document).on('click', '.btnEditarCertificado', function(){
    var id = $(this).data('id');
    if(!id) return;
    $.ajax({
      url: 'ajax/certificados.ajax.php',
      method: 'POST',
      data: { accion: 'mostrar' },
      dataType: 'json'
    }).done(function(resp){
      var eventos = resp.eventos || [];
      var encontrado = eventos.find(function(e){ return parseInt(e.id) === parseInt(id); });
      if(!encontrado){ Swal.fire('Error','Certificado no encontrado','error'); return; }
      // rellenar campos
      $('#editar_id').val(encontrado.id);
      $('#editar_nombre').val(encontrado.nombre);
      $('#editar_ruc').val(encontrado.ruc);
      $('#editar_usuario').val(encontrado.usuario);
      $('#editar_clave').val(encontrado.clave);
      $('#editar_fecha_creacion').val(encontrado.fecha_creacion);
      $('#editar_fecha_vencimiento').val(encontrado.fecha_vencimiento);
      $('#editar_estado').val(encontrado.estado);
      $('#editar_observacion').val(encontrado.observacion);
      $('#editar_tipo').val(encontrado.tipo || 'OSE');
      $('#modalEditarCertificado').modal('show');
    }).fail(function(){ Swal.fire('Error','No se pudo obtener datos','error'); });
  });

  // Enviar edición
  $(document).on('submit', '#formEditarCertificado', function(e){
    e.preventDefault();
    var form = $(this)[0];
    var fd = new FormData(form);
    fd.append('accion','actualizar');
    $.ajax({ url: 'ajax/certificados.ajax.php', method: 'POST', data: fd, processData: false, contentType: false, dataType: 'json' })
    .done(function(resp){
      if(resp && resp.success === true){
        $('#modalEditarCertificado').modal('hide');
        reloadTable();
      } else if(typeof resp === 'string' && resp === 'ok'){
        $('#modalEditarCertificado').modal('hide');
        reloadTable();
      } else { var msg = (resp && resp.error) ? resp.error : 'Error al actualizar'; Swal.fire('Error', msg, 'error'); }
    }).fail(function(jqxhr){ var msg = (jqxhr && jqxhr.responseText) ? jqxhr.responseText : 'No se pudo conectar al servidor'; Swal.fire('Error',msg,'error'); });
  });

  // Eliminar (confirmación)
  $(document).on('click', '.btnEliminarCertificado', function(e){
    // Use the event object to properly stop propagation
    try{ e.preventDefault(); e.stopImmediatePropagation(); } catch(err){ console.warn('certificados: stopImmediatePropagation failed', err); }
    var id = $(this).data('id');
    if(!id) return;

    console.log('certificados: solicitar confirmacion eliminar id=', id);

    // Defer the Swal open slightly to avoid race conditions with other handlers
    setTimeout(function(){
      Swal.fire({
        title: '¿Eliminar certificado?',
        text: 'Esta acción eliminará el registro permanentemente.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        allowOutsideClick: false,
        allowEscapeKey: false,
        focusCancel: true,
        didOpen: function(){ console.log('certificados: Swal didOpen'); },
        willClose: function(){ console.log('certificados: Swal willClose'); }
      }).then(function(result){
        console.log('certificados: Swal result', result);
        if(result.isConfirmed){
          $.ajax({ url: (window.BASE_URL || '') + '/ajax/certificados.ajax.php', method: 'POST', data: { accion: 'eliminar', id: id }, dataType: 'json' })
          .done(function(resp){ console.log('certificados: eliminar response', resp); if(resp == 'ok' || (resp && resp.success !== false)){ reloadTable(); } else { Swal.fire('Error','No se pudo eliminar','error'); } })
          .fail(function(jqxhr,textStatus,errorThrown){ console.error('certificados: eliminar ajax fail', textStatus, errorThrown, jqxhr && jqxhr.responseText); Swal.fire('Error','No se pudo conectar al servidor','error'); });
        }
      });
    }, 50);
  });

});
