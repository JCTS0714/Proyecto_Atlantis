<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Tablero Kanban - CRM</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  
  <!-- Ya tienes Bootstrap 3.3.7 y jQuery en plantilla.php, por eso no se agregan aquí -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

  <style>
    .kanban-column {
      min-height: 300px;
      border: 1px solid #ddd;
      padding: 10px;
      background-color: #f9f9f9;
      overflow-y: auto;
    }
    .panel-kanban {
      cursor: move;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div class="content-wrapper">
    <section class="content-header">
      <h1>Tablero Kanban</h1>
    </section>

    <section class="content">
      <button class="btn btn-primary" id="btn-nueva-oportunidad" style="margin-bottom: 15px; width: 100%; max-width: 230px;">+ Nueva Oportunidad</button>
      <button class="btn btn-success" data-toggle="modal" data-target="#modalAgregarCliente" style="margin-bottom: 15px; width: 100%; max-width: 230px;">+ Nuevo Prospecto</button>
      <div class="row">
        <?php
        $estados = ["nuevo", "calificado", "propuesto", "ganado"];
        foreach ($estados as $estado) {
          echo '<div class="col-md-3">';
          echo '<div class="box box-primary">';
          echo '<div class="box-header with-border">';
          echo "<h3 class='box-title'>" . ucfirst($estado) . "</h3>";
          echo '</div>';
          echo "<div class='box-body kanban-column' id='$estado' ondrop='drop(event)' ondragover='allowDrop(event)'>";
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
              <label for="titulo">Título</label>
              <input type="text" class="form-control" id="titulo" name="nuevoTitulo" required>
            </div>
            <div class="form-group">
              <label for="descripcion">Descripción</label>
              <textarea class="form-control" id="descripcion" name="nuevaDescripcion" required></textarea>
            </div>
            <div class="form-group">
              <label for="valor_estimado">Valor Estimado</label>
              <input type="number" class="form-control" id="valor_estimado" name="nuevoValorEstimado" required>
            </div>
            <div class="form-group">
              <label for="probabilidad">Probabilidad (%)</label>
              <input type="number" class="form-control" id="probabilidad" name="nuevaProbabilidad" required>
            </div>
            <div class="form-group">
              <label for="fecha_cierre">Fecha de Cierre</label>
              <input type="date" class="form-control" id="fecha_cierre" name="nuevaFechaCierre" required>
            </div>
            <div class="form-group">
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

  <!-- Modal para agregar cliente -->
  <div id="modalAgregarCliente" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <form role="form" method="post" enctype="multipart/form-data">
          <div class="modal-header" style="background:#3c8dbc; color:white;">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Agregar Cliente</h4>
          </div>
          <div class="modal-body">
            <div class="box-body">
              <!-- Campos de formulario -->
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-user"></i></span>
                  <input type="text" class="form-control input-lg" name="nuevoNombre" placeholder="Ingresar nombre" required>
                </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-users"></i></span>
                  <select class="form-control input-lg" name="nuevoTipo" required>
                    <option value="">Seleccionar tipo</option>
                    <option value="DNI">DNI</option>
                    <option value="RUC">RUC</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-address-card"></i></span>
                  <input type="text" class="form-control input-lg" name="nuevoDocumento" placeholder="Ingresar documento" required>
                </div>
              </div>
              <div class="form-group">
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
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-building"></i></span>
                <input type="text" class="form-control input-lg" name="nuevoEmpresa" placeholder="Ingresar empresa" required>
              </div>
              </div>
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  <input type="date" class="form-control input-lg" name="nuevoFechaContacto" placeholder="Ingresar fecha de contacto" required>
                </div>
              </div>
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> 27fc4213f1497e196cdabdb3c71cbf402171bd57
              <!-- COSA QUE BORRO, ESTA IMPIDIENDO LA CREACION DE PROSPECTO -->
          <!--<div class="form-group">
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-building"></i></span>
                  <input type="text" class="form-control input-lg" id="cliente_id" name="idCliente" required style="width: 100%;">
            </div>
          </div>-->
<<<<<<< HEAD
=======
=======
          <div class="form-group">
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-building"></i></span>
              <input type="text" class="form-control input-lg" id="cliente_id" name="idCliente" required style="width: 100%;">
            </div>
          </div>
>>>>>>> b2e765b3318b27a44af7e57167922f29af51b6d3
>>>>>>> 27fc4213f1497e196cdabdb3c71cbf402171bd57
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
            <button type="submit" class="btn btn-primary">Guardar Cliente</button>
          </div>
          <input type="hidden" name="ruta" value="crm" />
          <?php
            $crearCliente = new ControladorCliente();
            $crearCliente->ctrCrearCliente();
          ?>
        </form>
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
      url: '/Proyecto_atlantis/Ventas/ajax/clientes_oportunidades.ajax.php',
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
      url: '/Proyecto_atlantis/Ventas/ajax/clientes_oportunidades.ajax.php',
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
  });

  $('#btn-nueva-oportunidad').on('click', function () {
    $('#modal-nueva-oportunidad').modal('show');
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
</body>
</html>
