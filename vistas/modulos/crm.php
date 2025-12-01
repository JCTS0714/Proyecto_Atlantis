<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Tablero Kanban - CRM</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  
  <!-- Ya tienes Bootstrap 3.3.7 y jQuery en plantilla.php, por eso no se agregan aquí -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
  <script src="../js/oportunidades.js"></script>

  <style>
    .kanban-column {
      min-height: 300px;
      border: 1px solid #ddd;
      padding: 10px;
      background-color: #f9f9f9;
      overflow-y: auto;
      transition: all 0.3s ease;
    }
    .panel-kanban {
      cursor: move;
      margin-bottom: 10px;
    }
    
    /* Estilos para drag and drop */
    .kanban-column.drop-target {
      background-color: #e8f4fd;
      border: 2px dashed #3c8dbc;
      transform: scale(1.02);
    }
    
    .card.dragging {
      opacity: 0.6;
      transform: rotate(5deg);
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .card {
      transition: all 0.2s ease;
      cursor: grab;
    }
    
    .card:active {
      cursor: grabbing;
    }
    
    /* Colores de fondo para cada columna */
    #1 {
      background-color: #f8f9fa;
      border-left: 4px solid #6c757d;
    }
    
    #2 {
      background-color: #fff3cd;
      border-left: 4px solid #ffc107;
    }
    
    #3 {
      background-color: #d1ecf1;
      border-left: 4px solid #17a2b8;
    }
    
    #4 {
      background-color: #d4edda;
      border-left: 4px solid #28a745;
    }
    
    .box-header h3 {
      font-weight: bold;
      text-transform: uppercase;
    }
    /* Botón derecho del toolbar: empuja a la derecha con un pequeño margen */
    #filtros-inline { flex: 1 1 auto; min-width: 0; }
    .toolbar-right-button {
      margin-left: auto;
      margin-right: 8px; /* pequeño espacio desde el borde derecho */
    }

    /* Ajuste responsive: en pantallas pequeñas los elementos se apilan y el botón ocupa su propio renglón */
    @media (max-width: 768px) {
      #filtros-inline { flex: 1 1 100%; }
      .toolbar-right-button { margin-left: 0; margin-right: 0; }
    }
  </style>
</head>
<body>
  <div class="content-wrapper">
    <section class="content-header">
      <h1>Tablero Kanban</h1>
    </section>

    <section class="content">
      <div style="margin-bottom: 15px; display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
          <button class="btn btn-success" data-toggle="modal" data-target="#modalAgregarProspecto" style="margin-right: 10px; width: 100%; max-width: 230px;">+ Nuevo Prospecto</button>

          <!-- Filtros inline encima del kanban (posición restaurada) -->
          <div id="filtros-inline" style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <div class="form-group" style="margin:0;">
              <label for="filtro-estado" style="display:block; font-weight:600; margin-bottom:4px;">Estado</label>
              <select class="form-control" id="filtro-estado" name="filtroEstado" style="min-width:160px;">
                  <option value="">Todos los estados</option>
                  <option value="1">Seguimiento</option>
                  <option value="2">Calificado</option>
                  <option value="3">Propuesto</option>
                  <option value="4">Ganado</option>
              </select>
            </div>

            <div class="form-group" style="margin:0;">
              <label for="filtro-periodo" style="display:block; font-weight:600; margin-bottom:4px;">Periodo</label>
              <select class="form-control" id="filtro-periodo" name="filtroPeriodo" style="min-width:140px;">
                  <option value="diario">Diario</option>
                  <option value="semanal">Semanal</option>
                  <option value="mensual">Mensual</option>
                  <option value="personalizado">Personalizado</option>
              </select>
            </div>

            <div id="filtro-fechas-personalizado" style="display: none; margin:0;">
                <div class="form-group" style="margin:0 8px 0 0;">
                    <label for="fecha-inicio" style="display:block; font-weight:600; margin-bottom:4px;">Fecha Inicio</label>
                    <input type="date" class="form-control" id="fecha-inicio" name="fechaInicio">
                </div>
                <div class="form-group" style="margin:0 8px 0 0;">
                    <label for="fecha-fin" style="display:block; font-weight:600; margin-bottom:4px;">Fecha Fin</label>
                    <input type="date" class="form-control" id="fecha-fin" name="fechaFin">
                </div>
              </div>

            <div style="display:flex; gap:6px; align-items:flex-end;">
              <button type="button" class="btn btn-default" id="btn-limpiar-filtros-inline">Limpiar</button>
              <button type="button" class="btn btn-primary" id="btn-aplicar-filtros-inline">Aplicar</button>
            </div>
          </div>
        <!-- Botón para crear oportunidad desde prospecto (ubicado a la derecha del toolbar) -->
        <button id="btn-nueva-oportunidad-prospecto" class="btn btn-info toolbar-right-button" style="width: 100%; max-width: 230px;">+ Nueva Oportunidad (Prospecto)</button>
        </div>
        <div class="row">
        <?php
    $estados = [
        "1" => "Seguimiento",
        "2" => "Calificado",
        "3" => "Propuesto", 
        "4" => "Ganado"
    ];
        foreach ($estados as $id => $titulo) {
          echo '<div class="col-md-3">';
          echo '<div class="box box-primary">';
          echo '<div class="box-header with-border">';
          echo "<h3 class='box-title'>$titulo</h3>";
          echo '</div>';
          echo "<div class='box-body kanban-column' id='$id' ondrop='drop(event)' ondragover='allowDrop(event)'>";
          echo "<!-- Tarjetas se cargarán aquí -->";
          echo '</div>';
          echo '</div></div>';
        }
        ?>
      </div>
    </section>
  </div>

      <style>
        /* Estilos específicos para los filtros inline */
        #filtros-inline .form-group { display:inline-block; }
        @media (max-width: 768px) {
        #filtros-inline { flex-direction: column; align-items: stretch; }
        #filtros-inline .form-group { width: 100%; }
        #filtro-fechas-personalizado { display:flex; flex-direction:column; }
        }
      </style>

      <script>
      document.addEventListener('DOMContentLoaded', function() {
        // Inicializar periodo por defecto a vacío (sin filtro)
          var filtroPeriodo = document.getElementById('filtro-periodo');
          if (filtroPeriodo) {
            filtroPeriodo.value = '';
          }

        function showHidePersonalizado() {
          var el = document.getElementById('filtro-fechas-personalizado');
          if (!el) return;
          if (filtroPeriodo.value === 'personalizado') {
            el.style.display = 'flex';
          } else {
            el.style.display = 'none';
          }
        }

        if (filtroPeriodo) {
          filtroPeriodo.addEventListener('change', function() {
            showHidePersonalizado();
          });
        }

        // Limpiar filtros
        document.getElementById('btn-limpiar-filtros-inline').addEventListener('click', function() {
          document.getElementById('filtro-estado').value = '';
          document.getElementById('filtro-periodo').value = '';
          document.getElementById('fecha-inicio').value = '';
          document.getElementById('fecha-fin').value = '';
          showHidePersonalizado();
          if (typeof filtrarOportunidades === 'function') {
            filtrarOportunidades('', '', '', '');
          }
        });

        // Aplicar filtros con validación para personalizado
        document.getElementById('btn-aplicar-filtros-inline').addEventListener('click', function() {
          var estado = document.getElementById('filtro-estado').value;
          var periodo = document.getElementById('filtro-periodo').value;
          var inicio = document.getElementById('fecha-inicio').value;
          var fin = document.getElementById('fecha-fin').value;

          if (periodo === 'personalizado') {
            if (!inicio || !fin) {
              alert('Para el periodo personalizado debe seleccionar Fecha Inicio y Fecha Fin.');
              return;
            }
            var dInicio = new Date(inicio);
            var dFin = new Date(fin);
            if (isNaN(dInicio.getTime()) || isNaN(dFin.getTime())) {
              alert('Fechas inválidas.');
              return;
            }
            if (dInicio > dFin) {
              alert('Fecha Inicio no puede ser mayor que Fecha Fin.');
              return;
            }
          }

          if (typeof filtrarOportunidades === 'function') {
            filtrarOportunidades(estado || '', periodo || '', inicio || '', fin || '');
          }
        });

        // Mostrar/ocultar personalizado según valor inicial
        showHidePersonalizado();
      });
      </script>


<!-- Modal para crear nueva oportunidad -->
<div class="modal fade" id="modal-nueva-oportunidad" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="form-nueva-oportunidad">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title" id="modalLabel">Nueva Oportunidad</h4>
        </div>
        <div class="modal-body">
          <!-- Usuario logueado -->
          <input type="hidden" id="usuario_id" name="idUsuario" value="<?php echo $_SESSION['id']; ?>">

          <!-- 1: Título (único campo editable) -->
          <div class="form-group">
            <label for="titulo">Título <span style="color:red">*</span></label>
            <input type="text" class="form-control" id="titulo" name="nuevoTitulo" required>
          </div>

          <!-- Información adicional (editable) -->
          <div class="form-group">
            <label for="descripcion">Información adicional</label>
            <textarea class="form-control" id="descripcion" name="nuevaDescripcion" placeholder="Información adicional"></textarea>
          </div>

          <!-- 2: Cliente (select2) -->
          <div class="form-group">
            <label for="cliente_id">Cliente <span style="color:red">*</span></label>
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-tags"></i></span>
              <select class="form-control input-lg" id="cliente_id" name="idCliente" required style="width: 100%;">
                <option value="">Seleccionar cliente</option>
              </select>
            </div>
          </div>

          <!-- 3: Empresa (solo lectura, autocompletado desde prospectos) -->
          <div class="form-group">
            <label for="empresa">Empresa</label>
            <input type="text" class="form-control" id="empresa" name="empresa" readonly>
          </div>

          <!-- 4: Teléfono (solo lectura, autocompletado desde prospectos) -->
          <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input type="text" class="form-control" id="telefono" name="telefono" readonly>
          </div>
          <!-- Campos antiguos ocultos para compatibilidad con CRUD existente -->
          <input type="hidden" id="valor_estimado" name="nuevoValorEstimado" value="50">
          <input type="hidden" id="probabilidad" name="nuevaProbabilidad" value="50">
          <!-- Indicador si el modal fue abierto para crear desde prospecto (solo prospectos) -->
          <input type="hidden" id="prospect_only" name="prospect_only" value="">
          <!-- Fecha de cierre por defecto: hoy (formato YYYY-MM-DD) -->
          <?php $hoy = date('Y-m-d'); ?>
          <input type="hidden" id="fecha_cierre" name="nuevaFechaCierre" value="<?php echo $hoy; ?>">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Crear Oportunidad</button>
        </div>
      </div>
    </form>
  </div>
</div>
  <div id="modalAgregarProspecto" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <form role="form" method="post" enctype="multipart/form-data">
          <div class="modal-header" style="background:#3c8dbc; color:white;">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Agregar Prospecto</h4>
          </div>
          <div class="modal-body">
            <div class="box-body">
              <!-- Campos para agregar prospecto -->
              <div class="form-group">
                <label for="nuevoNombre">Nombre <span style="color:red">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-user"></i></span>
                  <input type="text" class="form-control input-lg" name="nuevoNombre" placeholder="Ingresar nombre" required>
                </div>
              </div>
              <div class="form-group">
                <label for="nuevoTipo">Tipo <span style="color:red">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-users"></i></span>
                  <select class="form-control input-lg" name="nuevoTipo" id="nuevoTipo" required>
                    <option value="">Seleccionar tipo</option>
                    <option value="DNI">DNI</option>
                    <option value="RUC">RUC</option>
                    <option value="otros">otros</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label for="nuevoDocumento">Documento <span style="color:red">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-address-card"></i></span>
                  <input type="text" class="form-control input-lg" name="nuevoDocumento" id="nuevoDocumento" placeholder="Ingresar documento" required>
                </div>
              </div>
              <div class="form-group">
                <label for="nuevoTelefono">Teléfono <span style="color:red">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-mobile"></i></span>
                  <input type="text" class="form-control input-lg" name="nuevoTelefono" placeholder="Ingresar teléfono" maxlength="9" required>
                </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                  <input type="text" class="form-control input-lg" name="nuevoCorreo" placeholder="Ingresar Observacion">
                </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-home"></i></span>
                  <input type="text" class="form-control input-lg" name="nuevoCiudad" placeholder="Ingresar ciudad">
                </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-globe"></i></span>
                  <input type="text" class="form-control input-lg" name="nuevoMigracion" placeholder="Ingresar migración">
                </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-link"></i></span>
                  <input type="text" class="form-control input-lg" name="nuevoReferencia" placeholder="Ingresar referencia">
                </div>
              </div>
              <div class="form-group">
                <label for="nuevoEmpresa">Empresa <span style="color:red">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-building"></i></span>
                  <input type="text" class="form-control input-lg" name="nuevoEmpresa" placeholder="Ingresar empresa" required>
                </div>
              </div>
              <div class="form-group">
                <label for="nuevoFechaContacto">Fecha de Contacto <span style="color:red">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  <input type="date" class="form-control input-lg" name="nuevoFechaContacto" placeholder="Ingresar fecha de contacto" required>
                </div>
              </div>
              <div class="form-group" style="display:none;">
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  <input type="date" class="form-control input-lg" name="nuevoFechaCreacion" placeholder="Ingresar fecha de creación">
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
            <button type="submit" class="btn btn-primary">Guardar Prospecto</button>
          </div>
          <?php
            $_POST['nuevoEstado'] = 0; // Estado 0 para prospectos
            $crearProspecto = new ControladorProspectos();
            $crearProspecto->ctrCrearProspecto();
          ?>
          <input type="hidden" name="nuevoEstado" value="0" />
          <input type="hidden" name="ruta" value="crm" />
        </form>
      </div>
    </div>
  </div>

<!-- Modal para mostrar los últimos prospectos en zona de espera -->
<div class="modal fade" id="modal-zona-espera" tabindex="-1" role="dialog" aria-labelledby="modalZonaEsperaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalZonaEsperaLabel">Últimos Prospectos en Zona de Espera</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="modal-zona-espera-lista" class="list-group">
                    <!-- Aquí se llenarán los prospectos -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

  <script>
$(document).ready(function () {
  function initClienteSelect2() {
    var $el = $('#cliente_id');
    // Si ya existe una instancia, destrúyela antes de re-inicializar
    if ($el.data('select2')) {
      try { $el.select2('destroy'); } catch(e) { /* ignore */ }
    }

    $el.select2({
      placeholder: 'Buscar cliente',
      minimumInputLength: 1,
      dropdownParent: $('#modal-nueva-oportunidad'),
      ajax: {
        url: 'ajax/clientes_oportunidades.ajax.php',
        dataType: 'json',
        delay: 250,
        data: function (params) {
          // Pasar posible filtro de estado (e.g., prospectos)
          return {
            q: params.term,
            estado: window.selectClientesEstado || ''
          };
        },
        processResults: function (data) {
          if (!Array.isArray(data)) return { results: [] };
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

  // Inicializar al cargar el documento y también cuando el modal se muestre (por si hay conflictos)
  initClienteSelect2();
  $('#modal-nueva-oportunidad').on('shown.bs.modal', function() {
    initClienteSelect2();
  });
  // Ya no se usa loadClientes porque select2 carga dinámicamente
  // No aplicar periodo por defecto al cargar
  window.filtrosActivos = { estado: '', periodo: '', fechaInicio: '', fechaFin: '' };
  loadOportunidades();

  // Nueva funcionalidad: abrir modal con cliente preseleccionado y bloqueado si cliente_id está en URL
  function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? null : decodeURIComponent(results[1].replace(/\+/g, ' '));
  }

  var clienteId = getUrlParameter('cliente_id');
  if (clienteId) {
    // Abrir modal
    $('#modal-nueva-oportunidad').modal('show');

    // Cargar el cliente preseleccionado en select2
    var option = new Option('Cargando...', clienteId, true, true);
    $('#cliente_id').append(option).trigger('change');

    // Hacer una llamada AJAX para obtener el nombre del cliente y actualizar la opción
    $.ajax({
      type: 'GET',
      url: 'ajax/clientes_oportunidades.ajax.php',
      data: { id: clienteId },
      dataType: 'json'
    }).then(function (data) {
      if (data && data.length > 0) {
        var clienteNombre = data[0].nombre;
        var newOption = new Option(clienteNombre, clienteId, true, true);
        $('#cliente_id').empty().append(newOption).trigger('change');
        // Deshabilitar el select2 para que no se pueda modificar
        $('#cliente_id').prop('disabled', true);
      }
    });

    // Limpiar cliente_id de la URL para evitar reapertura del modal al recargar
    if (window.history.replaceState) {
      var url = new URL(window.location);
      url.searchParams.delete('cliente_id');
      window.history.replaceState({}, document.title, url.toString());
    }
  }

  // Limpiar y habilitar el select2 y resetear formulario cuando se cierra el modal
  $('#modal-nueva-oportunidad').on('hidden.bs.modal', function () {
    $('#cliente_id').val(null).trigger('change');
    $('#cliente_id').prop('disabled', false);
    // Resetear todos los campos del formulario
    $('#form-nueva-oportunidad')[0].reset();
    // reset prospect_only flag and global filter
    window.selectClientesEstado = null;
    $('#prospect_only').val('');

    // Limpiar parámetros de la URL para evitar reapertura del modal con datos previos
    if (window.history.replaceState) {
      var url = new URL(window.location);
      url.searchParams.delete('cliente_id');
      url.searchParams.delete('titulo');
      url.searchParams.delete('fecha');
      url.searchParams.delete('actividad_origen');
      window.history.replaceState({}, document.title, url.toString());
    }
  });


  $('#form-nueva-oportunidad').on('submit', function (e) {
    e.preventDefault();
    crearOportunidad().then(function() {
      // Actualizar la URL para eliminar cliente_id después de crear la oportunidad
      if (window.history.replaceState) {
        var url = new URL(window.location);
        url.searchParams.delete('cliente_id');
        window.history.replaceState({}, document.title, url.toString());
      }
    });
  });

  // Handler para botón "Nueva Oportunidad (Prospecto)"
  $('#btn-nueva-oportunidad-prospecto').on('click', function() {
    // Forzar que select2 devuelva solo prospectos (estado = 0)
    window.selectClientesEstado = '0';
    // Marcar el modal como creado desde prospectos
    $('#prospect_only').val('1');
    // Limpiar select y abrir modal
    $('#cliente_id').val(null).trigger('change');
    $('#modal-nueva-oportunidad').modal('show');
  });
});
  </script>

  <script>
  // Autocompletar empresa y teléfono al seleccionar cliente en el modal
  (function() {
    // Cuando select2 cambia, solicitar datos del cliente
    $(document).on('change', '#cliente_id', function() {
      var clienteId = $(this).val();
      if (!clienteId) {
        $('#empresa').val('');
        $('#telefono').val('');
        return;
      }

      $.ajax({
        type: 'GET',
        url: 'ajax/clientes_oportunidades.ajax.php',
        data: { id: clienteId },
        dataType: 'json'
      }).then(function(data) {
        if (data && data.length > 0) {
          var c = data[0];
          $('#empresa').val(c.empresa || '');
          $('#telefono').val(c.telefono || '');
        } else if (data && data.empresa) {
          // en caso de que el endpoint devuelva un objeto en vez de array
          $('#empresa').val(data.empresa || '');
          $('#telefono').val(data.telefono || '');
        } else {
          $('#empresa').val('');
          $('#telefono').val('');
        }
      }).fail(function() {
        $('#empresa').val('');
        $('#telefono').val('');
      });
    });

    // Si el modal se abre con cliente_id en la URL, ya existe código que coloca la opción
    // Extendemos para rellenar empresa/telefono y bloquearlos
    // (El código que detecta cliente_id y hace la petición mantiene su lógica arriba)
  })();
  </script>

  <script>
    $(document).ready(function() {
      $('#nuevoTipo').on('change', function() {
        var tipoSeleccionado = $(this).val();
        var documentoInput = $('#nuevoDocumento');

        if (tipoSeleccionado === 'otros') {
          documentoInput.attr('placeholder', 'ingrese 8 dígitos');
          documentoInput.attr('maxlength', '8');
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

        if (tipoSeleccionado === 'otros') {
          var regex8Digitos = /^\d{8}$/;
          if (!regex8Digitos.test(documentoVal)) {
            e.preventDefault();
            $('#nuevoDocumento').addClass('is-invalid');
            alert('El documento debe tener exactamente 8 dígitos numéricos.');
          } else {
            $('#nuevoDocumento').removeClass('is-invalid');
          }
        }
      });
    });
  </script>
  
  <style>
    /* Highlight styles for reactivated opportunity */
    .oportunidad-highlight { 
      border: 2px solid #ffc107;
      box-shadow: 0 0 0 6px rgba(255,193,7,0.18);
      transition: box-shadow 0.3s, transform 0.15s;
      border-radius: 6px;
    }
    .oportunidad-highlight.pulse {
      animation: oportunidadPulse 1s ease-in-out 0s 3;
    }
    @keyframes oportunidadPulse {
      0% { box-shadow: 0 0 0 0 rgba(255,193,7,0.35); }
      50% { box-shadow: 0 0 18px 8px rgba(255,193,7,0.22); }
      100% { box-shadow: 0 0 0 0 rgba(255,193,7,0); }
    }
  </style>

  <script>
    // If the URL includes highlight_oportunidad_id, attempt to find and mark the card
    (function() {
      function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? null : decodeURIComponent(results[1].replace(/\+/g, ' '));
      }

      var highlightId = getUrlParameter('highlight_oportunidad_id');
      if (!highlightId) return;

      // Remove param from URL so it doesn't re-trigger on reload
      try {
        if (window.history.replaceState) {
          var url = new URL(window.location);
          url.searchParams.delete('highlight_oportunidad_id');
          window.history.replaceState({}, document.title, url.toString());
        }
      } catch (e) {
        // ignore
      }

      // Try to highlight the card several times (waiting for AJAX render)
      var attempts = 0;
      function tryHighlight() {
        attempts++;
        var el = document.getElementById('oportunidad-' + highlightId);
        if (el) {
          el.classList.add('oportunidad-highlight');
          // Scroll into view centered
          try { el.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch(e) { el.scrollIntoView(); }
          // Add pulse animation
          setTimeout(function() { el.classList.add('pulse'); }, 50);
          // Remove highlight after a while
          setTimeout(function() { el.classList.remove('oportunidad-highlight'); el.classList.remove('pulse'); }, 6000);
          return;
        }
        if (attempts < 12) {
          setTimeout(tryHighlight, 350);
        }
      }

      // Start trying after a small delay to let initial loadOportunidades() run
      setTimeout(tryHighlight, 300);
    })();
  </script>

  <!-- Incluir el modal de detalles de oportunidad -->
  <?php include 'modal_detalles.php'; ?>
  
  <!-- Incluir el modal de filtros -->
  <?php include 'modal_filtros.php'; ?>
</body>
</html>
