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
  </style>
</head>
<body>
  <div class="content-wrapper">
    <section class="content-header">
      <h1>Tablero Kanban</h1>
    </section>

    <section class="content">
      <div style="margin-bottom: 15px;">
          <button class="btn btn-success" data-toggle="modal" data-target="#modalAgregarProspecto" style="margin-right: 10px; width: 100%; max-width: 230px;">+ Nuevo Prospecto</button>
        <button class="btn btn-info" data-toggle="modal" data-target="#modal-filtros" style="width: 100%; max-width: 230px;">
          <i class="fa fa-filter mr-2"></i>Filtros
        </button>
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
          <div class="form-group">
            <label for="titulo">Título <span style="color:red">*</span></label>
            <input type="text" class="form-control" id="titulo" name="nuevoTitulo" required>
          </div>
          <div class="form-group">
            <label for="descripcion">Información adicional</label>
            <textarea class="form-control" id="descripcion" name="descripcion"></textarea>
          </div>
          <div class="form-group">
            <label for="valor_estimado">Valor Estimado <span style="color:red">*</span></label>
            <input type="number" class="form-control" id="valor_estimado" name="nuevoValorEstimado" required>
          </div>
          <div class="form-group">
            <label for="probabilidad">Probabilidad (%) <span style="color:red">*</span></label>
            <input type="number" class="form-control" id="probabilidad" name="nuevaProbabilidad" required>
          </div>
          <div class="form-group">
            <label for="fecha_cierre">Fecha de Cierre Estimado (Estimado) <span style="color:red">*</span></label>
            <input type="date" class="form-control" id="fecha_cierre" name="nuevaFechaCierre" required>
          </div>
          <div class="form-group">
            <label for="cliente_id">Cliente <span style="color:red">*</span></label>
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-tags"></i></span>
              <select class="form-control input-lg" id="cliente_id" name="idCliente" required style="width: 100%;">
                <option value="">Seleccionar cliente</option>
              </select>
            </div>
          </div>
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
                  <input type="email" class="form-control input-lg" name="nuevoCorreo" placeholder="Ingresar correo">
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
  $('#cliente_id').select2({
    placeholder: 'Buscar cliente',
    minimumInputLength: 1,
    dropdownParent: $('#modal-nueva-oportunidad'),
    ajax: {
      url: 'ajax/clientes_oportunidades.ajax.php',
      dataType: 'json',
      delay: 250,
      data: function (params) {
        console.log("Search term:", params.term);
        return {
          q: params.term // término de búsqueda
        };
      },
      processResults: function (data) {
        return {
          results: data.map(function(cliente) {
            return { id: cliente.id, text: cliente.nombre };
          })
        };
      },
      cache: true
    }
  });
  // Ya no se usa loadClientes porque select2 carga dinámicamente
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
});
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
  
  <!-- Incluir el modal de detalles de oportunidad -->
  <?php include 'modal_detalles.php'; ?>
  
  <!-- Incluir el modal de filtros -->
  <?php include 'modal_filtros.php'; ?>
</body>
</html>
