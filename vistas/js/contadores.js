// JS para manejar contadores con Select2 para comercios

$(document).ready(function() {
  
  // ========================================
  // INICIALIZAR SELECT2 PARA COMERCIOS
  // ========================================
  function initSelect2Comercio($select, modalId) {
    // Determinar el modal padre
    var $dropdownParent = modalId ? $(modalId) : $select.closest('.modal');
    
    $select.select2({
      placeholder: 'Buscar comercio por empresa...',
      allowClear: true,
      minimumInputLength: 1,
      dropdownParent: $dropdownParent, // FIX: Evita problemas de z-index en modales
      ajax: {
        url: 'ajax/contadores.ajax.php',
        dataType: 'json',
        delay: 300,
        data: function(params) {
          return {
            action: 'buscarEmpresas',
            term: params.term
          };
        },
        processResults: function(data) {
          return data;
        },
        cache: true
      },
      width: 'calc(100% - 45px)'
    });
  }
  
  // Inicializar el primer select2 del modal crear (al abrir modal)
  $('#modalAgregarContador').on('shown.bs.modal', function() {
    var $firstSelect = $('#comerciosContainer .select2-comercio').first();
    if (!$firstSelect.hasClass('select2-hidden-accessible')) {
      initSelect2Comercio($firstSelect, '#modalAgregarContador');
    }
  });
  
  // ========================================
  // AGREGAR MÁS COMERCIOS (MODAL CREAR)
  // ========================================
  $('#btnAgregarComercio').on('click', function() {
    var $container = $('#comerciosContainer');
    var $nuevoItem = $(`
      <div class="comercio-item" style="margin-bottom: 8px;">
        <select class="form-control select2-comercio" name="nuevosComercios[]" style="width: calc(100% - 45px); display: inline-block;">
        </select>
        <button type="button" class="btn btn-danger btn-sm btn-quitar-comercio" title="Quitar">
          <i class="fa fa-minus"></i>
        </button>
      </div>
    `);
    $container.append($nuevoItem);
    initSelect2Comercio($nuevoItem.find('.select2-comercio'), '#modalAgregarContador');
    
    // Mostrar botón quitar en todos los items si hay más de 1
    actualizarBotonesQuitar($container);
  });
  
  // Quitar comercio
  $(document).on('click', '.btn-quitar-comercio', function() {
    var $container = $('#comerciosContainer');
    if ($container.find('.comercio-item').length > 1) {
      $(this).closest('.comercio-item').remove();
      actualizarBotonesQuitar($container);
    }
  });
  
  function actualizarBotonesQuitar($container) {
    var items = $container.find('.comercio-item');
    if (items.length > 1) {
      items.find('.btn-quitar-comercio').show();
    } else {
      items.find('.btn-quitar-comercio').hide();
    }
  }
  
  // ========================================
  // AGREGAR MÁS COMERCIOS (MODAL EDITAR)
  // ========================================
  $('#btnAgregarComercioEditar').on('click', function() {
    var $container = $('#comerciosContainerEditar');
    var $nuevoItem = $(`
      <div class="comercio-item" style="margin-bottom: 8px;">
        <select class="form-control select2-comercio-editar" name="editarComercios[]" style="width: calc(100% - 45px); display: inline-block;">
        </select>
        <button type="button" class="btn btn-danger btn-sm btn-quitar-comercio-editar" title="Quitar">
          <i class="fa fa-minus"></i>
        </button>
      </div>
    `);
    $container.append($nuevoItem);
    initSelect2Comercio($nuevoItem.find('.select2-comercio-editar'), '#modalEditarContador');
    
    actualizarBotonesQuitarEditar($container);
  });
  
  $(document).on('click', '.btn-quitar-comercio-editar', function() {
    var $container = $('#comerciosContainerEditar');
    if ($container.find('.comercio-item').length > 1) {
      $(this).closest('.comercio-item').remove();
      actualizarBotonesQuitarEditar($container);
    }
  });
  
  function actualizarBotonesQuitarEditar($container) {
    var items = $container.find('.comercio-item');
    if (items.length > 1) {
      items.find('.btn-quitar-comercio-editar').show();
    } else {
      items.find('.btn-quitar-comercio-editar').hide();
    }
  }
  
  // ========================================
  // CARGAR DATOS AL ABRIR MODAL EDITAR
  // ========================================
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
      $('#editarNomContador').val(data.nom_contador);
      $('#editarTitularTlf').val(data.titular_tlf);
      $('#editarTelefono').val(data.telefono);
      $('#editarTelefonoActu').val(data.telefono_actu);
      $('#editarLink').val(data.link);
      $('#editarUsuario').val(data.usuario);
      $('#editarContrasena').val(data.contrasena);
      
      // Limpiar comercios anteriores
      var $container = $('#comerciosContainerEditar');
      $container.find('.comercio-item:not(:first)').remove();
      var $firstSelect = $container.find('.select2-comercio-editar').first();
      
      // Destruir Select2 existente
      if ($firstSelect.hasClass('select2-hidden-accessible')) {
        $firstSelect.select2('destroy');
      }
      $firstSelect.empty();
      
      // Cargar comercios asignados
      if (data.clientes_asignados && data.clientes_asignados.length > 0) {
        // Formatear texto para primer cliente
        var primerCliente = data.clientes_asignados[0];
        var text1 = primerCliente.empresa + (primerCliente.nombre ? ' (' + primerCliente.nombre + ')' : '');
        $firstSelect.append(new Option(text1, primerCliente.id, true, true));
        initSelect2Comercio($firstSelect, '#modalEditarContador');
        
        // Agregar más si hay
        for (var i = 1; i < data.clientes_asignados.length; i++) {
          var cliente = data.clientes_asignados[i];
          var textN = cliente.empresa + (cliente.nombre ? ' (' + cliente.nombre + ')' : '');
          var $nuevoItem = $(`
            <div class="comercio-item" style="margin-bottom: 8px;">
              <select class="form-control select2-comercio-editar" name="editarComercios[]" style="width: calc(100% - 45px); display: inline-block;">
              </select>
              <button type="button" class="btn btn-danger btn-sm btn-quitar-comercio-editar" title="Quitar">
                <i class="fa fa-minus"></i>
              </button>
            </div>
          `);
          $container.append($nuevoItem);
          var $newSelect = $nuevoItem.find('.select2-comercio-editar');
          $newSelect.append(new Option(textN, cliente.id, true, true));
          initSelect2Comercio($newSelect, '#modalEditarContador');
        }
        
        actualizarBotonesQuitarEditar($container);
      } else {
        // Sin comercios asignados, solo inicializar vacío
        initSelect2Comercio($firstSelect, '#modalEditarContador');
      }
      
    }).fail(function() {
      console.warn('No se pudo cargar contador');
    });
  });
  
  // ========================================
  // SUBMIT FORMULARIO CREAR - Recoger IDs
  // ========================================
  $('#formAgregarContador').on('submit', function(e) {
    var ids = [];
    $('#comerciosContainer .select2-comercio').each(function() {
      var val = $(this).val();
      if (val) ids.push(val);
    });
    $('#comercioIds').val(ids.join(','));
  });
  
  // ========================================
  // SUBMIT FORMULARIO EDITAR - Recoger IDs
  // ========================================
  $('#formEditarContador').on('submit', function(e) {
    var ids = [];
    $('#comerciosContainerEditar .select2-comercio-editar').each(function() {
      var val = $(this).val();
      if (val) ids.push(val);
    });
    $('#comercioIdsEditar').val(ids.join(','));
  });
  
  // ========================================
  // RESET MODAL CREAR AL CERRAR
  // ========================================
  $('#modalAgregarContador').on('hidden.bs.modal', function() {
    var $container = $('#comerciosContainer');
    $container.find('.comercio-item:not(:first)').remove();
    var $firstSelect = $container.find('.select2-comercio').first();
    if ($firstSelect.hasClass('select2-hidden-accessible')) {
      $firstSelect.val(null).trigger('change');
    }
    $container.find('.btn-quitar-comercio').hide();
  });
  
  // ========================================
  // INICIALIZAR DATATABLE
  // ========================================
  if ($('#tablaContadores').length) {
    try { 
      $('#tablaContadores').DataTable({
        "language": {
          "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
      }); 
    } catch(e) { /* ignore */ }
  }
  
  // ========================================
  // CARGAR PRÓXIMO N° AL ABRIR MODAL CREAR
  // ========================================
  $('#modalAgregarContador').on('show.bs.modal', function() {
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
  });
  
});
