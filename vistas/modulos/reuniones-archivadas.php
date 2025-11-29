<div class="content-wrapper">
  <section class="content-header">
    <h1>Historial de reuniones</h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Historial de reuniones</li>
    </ol>
  </section>

  <section class="content">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">Listado - Historial de reuniones</h3>
      </div>
      <div class="box-body">
        <table id="tablaArchivadas" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>ID</th>
              <th>Fecha</th>
              <th>Hora</th>
              <th>Título</th>
              <th>Cliente</th>
              <th>Usuario</th>
              <th>Estado</th>
              <th>Archivado en</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<script>
$(function(){
  function cargarArchivadas(){
    $.post('ajax/calendario.ajax.php', { accion: 'obtener_archivadas' }, function(resp){
      try{
        var j = typeof resp === 'string' ? JSON.parse(resp) : resp;
        var rows = j.eventos || [];
        var tbody = $('#tablaArchivadas tbody');
        tbody.empty();
        rows.forEach(function(r){
          var hora = (r.hora_inicio || '') + ' - ' + (r.hora_fin || '');
          tbody.append('<tr>'+
            '<td>'+r.id+'</td>'+
            '<td>'+r.fecha+'</td>'+
            '<td>'+hora+'</td>'+
            '<td>'+r.titulo+'</td>'+
            '<td>'+ (r.nombre_cliente || r.cliente_id) +'</td>'+
            '<td>'+ (r.usuario_id || '') +'</td>'+
            '<td>'+(r.estado||'')+'</td>'+
            '<td>'+(r.archivado_en||'')+'</td>'+
            '</tr>');
        });
        $('#tablaArchivadas').DataTable();
      }catch(e){ console.error(e); }
    });
  }
  cargarArchivadas();
});
</script>
