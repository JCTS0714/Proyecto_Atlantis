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
          if ($estado === 'nuevo') {
            echo '<button class="btn btn-primary btn-block" id="btn-nueva-oportunidad">+ Nueva Oportunidad</button>';
          }
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
                <select class="form-control input-lg" id="cliente_id" name="idCliente" required>
                  <option value="">Seleccionar cliente</option>
                  <?php
                  $item = null;
                  $valor = null;
                  $cliente = ControladorCliente::ctrMostrarCliente($item, $valor);
                  
                  if(!empty($cliente) && is_array($cliente)) {
                    foreach ($cliente as $cliente) {
                      echo '<option value="'.$cliente['id'].'">'.$cliente['nombre'].'</option>';
                    }
                  } else {
                    echo '<option value="">No hay cliente disponibles</option>';
                  }
                  ?>
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

  <script>
    $(document).ready(function () {
      $('#cliente_id').select2();
      loadClientes();
      loadOportunidades();

      $('#btn-nueva-oportunidad').on('click', function () {
        $('#modal-nueva-oportunidad').modal('show');
      });

      $('#form-nueva-oportunidad').on('submit', function (e) {
        e.preventDefault();
        crearOportunidad();
      });
    });
  </script>
</body>
</html>