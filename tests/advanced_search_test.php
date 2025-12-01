<?php
// Simple test harness for Advanced Search and DataTable integration
// Access via: http://localhost/Proyecto_Atlantis/produccion_worktree/tests/advanced_search_test.php
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Advanced Search Test</title>
  <link rel="stylesheet" href="/Proyecto_Atlantis/produccion_worktree/vistas/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="/Proyecto_Atlantis/produccion_worktree/vistas/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <style>body{padding:20px}</style>
</head>
<body>
  <h3>Advanced Search Test</h3>

  <?php include __DIR__ . '/../vistas/modulos/advanced_search.php'; ?>

  <div style="margin-top:20px;">
    <table id="tablaClientes" class="table table-bordered dt-responsive tabla" data-ajax="/Proyecto_Atlantis/produccion_worktree/ajax/datatable-clientes.ajax.php">
      <thead>
        <tr>
          <th>#</th>
          <th>Nombre</th>
          <th>Tipo</th>
          <th>Documento</th>
          <th>Teléfono</th>
          <th>Observacion</th>
          <th>Ciudad</th>
          <th>Fecha Creacion</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>

  <script src="/Proyecto_Atlantis/produccion_worktree/vistas/bower_components/jquery/dist/jquery.min.js"></script>
  <script src="/Proyecto_Atlantis/produccion_worktree/vistas/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
  <script src="/Proyecto_Atlantis/produccion_worktree/vistas/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
  <script src="/Proyecto_Atlantis/produccion_worktree/vistas/js/plantilla.js"></script>
  <script src="/Proyecto_Atlantis/produccion_worktree/vistas/js/advanced_search.js"></script>

  <script>
    // enable debug mode to view debugReplaceTableWithRaw helpers
    window.PLANTILLA_DEV = true;
    // small helper to log when advancedSearch:apply fires
    window.addEventListener('advancedSearch:apply', function(e){
      console.log('Test page: advancedSearch:apply received', e.detail);
    });
  </script>
</body>
</html>
