<!-- Certificados Module (initial inline modal builder removed — dynamic loader and handlers below provide required behavior) -->

<div class="content-wrapper">
  <section class="content-header">
    <h1>Certificados</h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo BASE_URL; ?>/inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Certificados</li>
    </ol>
  </section>

  <section class="content">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">Administrar Certificados</h3>
        <!-- Removed data-toggle/data-target to avoid duplicate bootstrap handlers; JS controls the modal -->
        <button id="btnAgregarCertificado" class="btn btn-success pull-right" style="margin-left:8px;"><i class="fa fa-plus"></i> Agregar</button>
        <button id="btnExportarCertificados" class="btn btn-primary pull-right"><i class="fa fa-download"></i> Exportar CSV</button>
      </div>
      <div class="box-body">
        <table class="table table-bordered table-striped dt-responsive tabla" id="tablaCertificados">
          <thead>
            <tr>
              <th>N°</th>
              <th>Nombre</th>
              <th>RUC</th>
              <th>Usuario</th>
              <th>Clave</th>
              <th>F. Creación</th>
              <th>F. Vencimiento</th>
              <th>Estado</th>
              <th>Observación</th>
              <th>Imagen</th>
              <th>Tipo</th>
              <th class="no-export">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $certificados = ControladorCertificados::ctrMostrarCertificados();
            if (!empty($certificados)) {
              foreach ($certificados as $k => $c) {
                echo '<tr>';
                echo '<td>'.($k+1).'</td>';
                echo '<td>'.htmlspecialchars($c['nombre']).'</td>';
                echo '<td>'.htmlspecialchars($c['ruc']).'</td>';
                echo '<td>'.htmlspecialchars($c['usuario']).'</td>';
                echo '<td>'.htmlspecialchars($c['clave']).'</td>';
                echo '<td>'.(!empty($c['fecha_creacion']) ? date('d/m/Y', strtotime($c['fecha_creacion'])) : '-').'</td>';
                echo '<td>'.(!empty($c['fecha_vencimiento']) ? date('d/m/Y', strtotime($c['fecha_vencimiento'])) : '-').'</td>';
                echo '<td>'.htmlspecialchars($c['estado']).'</td>';
                echo '<td>'.nl2br(htmlspecialchars($c['observacion'])).'</td>';
                // Imagen thumbnail
                if (!empty($c['imagen'])) {
                  $imgUrl = rtrim(BASE_URL,'/') . '/uploads/certificados/' . $c['imagen'];
                  echo '<td data-column="col-imagen"><img src="'.$imgUrl.'" alt="thumb" class="certificado-thumb" style="max-width:48px;max-height:48px;cursor:pointer;" data-full="'.$imgUrl.'"></td>';
                } else {
                  echo '<td data-column="col-imagen">-</td>';
                }
                echo '<td>'.(isset($c['tipo']) ? htmlspecialchars($c['tipo']) : '-').'</td>';
                echo '<td class="no-export">'
                   .'<button class="btn btn-warning btn-sm btnEditarCertificado" data-id="'.$c['id'].'"><i class="fa fa-pencil"></i></button> '
                   .'<button class="btn btn-danger btn-sm btnEliminarCertificado" data-id="'.$c['id'].'"><i class="fa fa-trash"></i></button>'
                  .'</td>';
                echo '</tr>';
              }
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
    <!-- Botones de exportación: se insertan vía DataTables Buttons -->
  </section>
</div>

<!-- Modal Agregar -->
<!-- MODULO_CERTIFICADOS_PRESENTE: confirma inclusión del archivo 'modulos/certificados.php' en el HTML servido -->
<div class="modal fade" id="modalAgregarCertificado" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form id="formAgregarCertificado" method="post" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Agregar Certificado</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" id="usuario_id" name="usuario_id" value="<?php echo $_SESSION['id']; ?>">
          <div class="form-group">
            <label>Nombre</label>
            <input type="text" class="form-control" name="nombre" required>
          </div>
          <div class="form-group">
            <label>RUC</label>
            <input type="text" class="form-control" name="ruc">
          </div>
          <div class="form-group">
            <label>Usuario</label>
            <input type="text" class="form-control" name="usuario">
          </div>
          <div class="form-group">
            <label>Clave</label>
            <input type="text" class="form-control" name="clave">
          </div>
          <div class="form-group">
            <label>F. Creación</label>
            <input type="date" class="form-control" name="fecha_creacion">
          </div>
          <div class="form-group">
            <label>F. Vencimiento</label>
            <input type="date" class="form-control" name="fecha_vencimiento" required>
          </div>
          <div class="form-group">
            <label>Estado</label>
            <select class="form-control" name="estado">
              <option value="activo">activo</option>
              <option value="inactivo">inactivo</option>
            </select>
          </div>
          <div class="form-group">
            <label>Observación</label>
            <textarea class="form-control" name="observacion"></textarea>
          </div>
          <div class="form-group">
            <label>Tipo</label>
            <select class="form-control" name="tipo">
              <option value="OSE">OSE</option>
              <option value="PSE">PSE</option>
            </select>
          </div>
          <div class="form-group">
            <label>Imagen (opcional)</label>
            <input type="file" class="form-control" name="imagen" accept="image/*">
            <p class="help-block">Puede subir una imagen opcional para este certificado (jpg, png, gif).</p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Guardar</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal Editar (se completará via JS) -->
<div class="modal fade" id="modalEditarCertificado" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form id="formEditarCertificado" method="post" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Editar Certificado</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="editar_id">
          <div class="form-group"><label>Nombre</label><input type="text" class="form-control" id="editar_nombre" name="nombre" required></div>
          <div class="form-group"><label>RUC</label><input type="text" class="form-control" id="editar_ruc" name="ruc"></div>
          <div class="form-group"><label>Usuario</label><input type="text" class="form-control" id="editar_usuario" name="usuario"></div>
          <div class="form-group"><label>Clave</label><input type="text" class="form-control" id="editar_clave" name="clave"></div>
          <div class="form-group"><label>F. Creación</label><input type="date" class="form-control" id="editar_fecha_creacion" name="fecha_creacion"></div>
          <div class="form-group"><label>F. Vencimiento</label><input type="date" class="form-control" id="editar_fecha_vencimiento" name="fecha_vencimiento" required></div>
          <div class="form-group"><label>Estado</label><select class="form-control" id="editar_estado" name="estado"><option value="activo">activo</option><option value="inactivo">inactivo</option></select></div>
          <div class="form-group"><label>Observación</label><textarea class="form-control" id="editar_observacion" name="observacion"></textarea></div>
          <div class="form-group"><label>Tipo</label><select class="form-control" id="editar_tipo" name="tipo"><option value="OSE">OSE</option><option value="PSE">PSE</option></select></div>
          <div class="form-group"><label>Imagen (opcional)</label><input type="file" class="form-control" id="editar_imagen" name="imagen" accept="image/*"><p class="help-block">Sube una nueva imagen para reemplazar la existente (opcional).</p></div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Inline fallback JS: asegura que 'Agregar' funcione aunque certificados.js no se cargue -->
<script>
document.addEventListener('DOMContentLoaded', function(){
  try{
    var btn = document.getElementById('btnAgregarCertificado');
    if(btn){ btn.addEventListener('click', function(e){ e.preventDefault(); var m = document.getElementById('modalAgregarCertificado'); if(m){ $(m).modal('show'); } else { console.error('Modal agregar no encontrado'); } }); }

    var form = document.getElementById('formAgregarCertificado');
    if(form){
      // Avoid attaching fallback submit handler if main script already registered a handler
      if (!window._certificados_has_main_handler) {
        form.addEventListener('submit', function(e){
          e.preventDefault();
          try{
            var fd = new FormData(form);
            fd.append('accion','crear');
            $.ajax({ url: 'ajax/certificados.ajax.php', method: 'POST', data: fd, processData: false, contentType: false, dataType: 'json' })
            .done(function(resp){ if(resp == 'ok' || (resp && resp.success !== false)){ location.reload(); } else { alert('Error: ' + (resp && resp.error ? resp.error : 'No se pudo crear')); } })
            .fail(function(){ alert('Error de conexión al crear certificado'); });
          }catch(err){
            // If FormData is not available, fallback to serialize (no files)
            var formData = $(form).serialize() + '&accion=crear';
            $.ajax({ url: 'ajax/certificados.ajax.php', method: 'POST', data: formData, dataType: 'json' })
            .done(function(resp){ if(resp == 'ok' || (resp && resp.success !== false)){ location.reload(); } else { alert('Error: ' + (resp && resp.error ? resp.error : 'No se pudo crear')); } })
            .fail(function(){ alert('Error de conexión al crear certificado'); });
          }
        });
      } else {
        console.log('certificados: inline fallback did not attach submit handler (main handler present)');
      }
    }
  } catch(e){ console.error('Fallback certificados inline error', e); }
});
</script>

<!-- Dynamic loader: if certificados.js not present inject it and wait for jQuery/Bootstrap -->
<script>
(function(){
  function scriptExists(srcEnding){
    var scripts = document.getElementsByTagName('script');
    for(var i=0;i<scripts.length;i++){ if(scripts[i].src && scripts[i].src.indexOf(srcEnding) !== -1) return true; }
    return false;
  }

  if (!scriptExists('certificados.js')) {
    // inject the script at the end of body
    var s = document.createElement('script');
    s.src = window.BASE_URL + '/vistas/js/certificados.js';
    s.onload = function(){ console.log('certificados.js dinámicamente cargado'); };
    s.onerror = function(){ console.error('No se pudo cargar certificados.js dinámicamente'); };
    document.body.appendChild(s);
  }

  // Ensure jQuery and bootstrap modal plugin are available before attempting to open modals from fallback
  var retries = 0;
  function ensureReady(cb){
    if (typeof jQuery !== 'undefined' && typeof jQuery.fn !== 'undefined' && typeof jQuery.fn.modal === 'function') return cb();
    retries++;
    if (retries > 50) { console.warn('Timeout esperando jQuery/Bootstrap para certificados'); return; }
    setTimeout(function(){ ensureReady(cb); }, 200);
  }

  ensureReady(function(){
    try{
      console.log('certificados: jQuery y Bootstrap modal plugin disponibles');
      var btn = document.getElementById('btnAgregarCertificado');

      // Move modal elements to body to avoid z-index/stacking context issues
      function moveModalToBody(id){
        var m = document.getElementById(id);
        if(m && m.parentNode !== document.body){
          console.log('certificados: moviendo', id, 'a document.body');
          document.body.appendChild(m);
        }
      }
      moveModalToBody('modalAgregarCertificado');
      moveModalToBody('modalEditarCertificado');

      if (btn) {
        btn.addEventListener('click', function(e){
          e.preventDefault();
          console.log('certificados: add button clicked (ensureReady handler)');
          // Use Bootstrap modal show via jQuery
          try{ $('#modalAgregarCertificado').modal('show'); console.log('certificados: invoked bootstrap modal show'); }
          catch(err){ console.error('certificados: error invoking bootstrap modal', err); }
        });
      }

      // Attach modal lifecycle logging when jQuery + bootstrap present
      try{
        var $m = $('#modalAgregarCertificado');
        if ($m && $m.length){
          $m.on('show.bs.modal', function(){ console.log('certificados: show.bs.modal fired'); });
          $m.on('shown.bs.modal', function(){ console.log('certificados: shown.bs.modal fired'); });
          $m.on('hide.bs.modal', function(e){ console.log('certificados: hide.bs.modal fired'); console.trace(); });
          $m.on('hidden.bs.modal', function(){ console.log('certificados: hidden.bs.modal fired'); });
        }
      }catch(e){ console.warn('certificados: could not bind bootstrap modal events', e); }

      // After moving modals, log computed styles to help diagnose visibility/z-index
      setTimeout(function(){
        var m = document.getElementById('modalAgregarCertificado');
        if(m){
          var cs = window.getComputedStyle(m);
          console.log('certificados: modalAgregarCertificado computed display=', cs.display, 'visibility=', cs.visibility, 'zIndex=', cs.zIndex);
        } else { console.warn('certificados: modalAgregarCertificado not found during post-move check'); }
      }, 50);
    }catch(e){ console.error('certificados: ensureReady callback error', e); }
  });
})();
</script>
