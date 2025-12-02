<?php

class ControladorContador {

  static public function ctrMostrarContadores($item, $valor) {
    $tabla = "contador";
    $respuesta = ModeloContador::mdlMostrarContador($tabla, $item, $valor);
    return $respuesta;
  }

  public function ctrCrearContador() {
    // Detectar envío - ahora usa comercioIds en lugar de nuevoComercio
    if (isset($_POST['nuevoNomContador']) || isset($_POST['comercioIds'])) {
      
      // Obtener comercio del primer cliente seleccionado (para compatibilidad con campo legacy)
      $comercioLegacy = '';
      $comercioIds = isset($_POST['comercioIds']) ? $_POST['comercioIds'] : '';
      
      if (!empty($comercioIds)) {
        $idsArray = array_filter(explode(',', $comercioIds));
        if (!empty($idsArray)) {
          // Obtener nombre de empresa del primer cliente para el campo comercio legacy
          try {
            $stmt = Conexion::conectar()->prepare("SELECT empresa FROM clientes WHERE id = :id LIMIT 1");
            $stmt->bindValue(':id', intval($idsArray[0]), PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
              $comercioLegacy = $row['empresa'];
            }
          } catch (Exception $e) {
            error_log('ctrCrearContador: Error obteniendo empresa - ' . $e->getMessage());
          }
        }
      }
      
      $datos = [
        // nro será calculado automáticamente en el modelo
        'nro' => '',
        'comercio' => $comercioLegacy,
        'nom_contador' => $_POST['nuevoNomContador'] ?? '',
        'titular_tlf' => $_POST['nuevoTitularTlf'] ?? '',
        'telefono' => $_POST['nuevoTelefono'] ?? '',
        'telefono_actu' => $_POST['nuevoTelefonoActu'] ?? '',
        'link' => $_POST['nuevoLink'] ?? '',
        'usuario' => $_POST['nuevoUsuario'] ?? '',
        'contrasena' => $_POST['nuevoContrasena'] ?? ''
      ];

      $tabla = 'contador';
      $respuesta = ModeloContador::mdlRegistrarContador($tabla, $datos);

      if ($respuesta == 'ok') {
        // Obtener el ID del contador recién creado
        try {
          $stmt = Conexion::conectar()->prepare("SELECT MAX(id) as ultimo_id FROM contador");
          $stmt->execute();
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          $nuevoContadorId = $row ? intval($row['ultimo_id']) : 0;
          
          // Asignar comercios a la tabla pivote
          if ($nuevoContadorId > 0 && !empty($comercioIds)) {
            $idsArray = array_filter(explode(',', $comercioIds));
            ModeloContador::mdlAsignarClientes($nuevoContadorId, $idsArray);
          }
        } catch (Exception $e) {
          error_log('ctrCrearContador: Error asignando comercios - ' . $e->getMessage());
        }
        
        echo '<script>Swal.fire({icon:"success", title:"Contador creado", showConfirmButton:true}).then(()=>{window.location="contadores";});</script>';
      } else {
        echo '<script>Swal.fire({icon:"error", title:"Error al crear", showConfirmButton:true});</script>';
      }
    }
  }

  public function ctrEditarContador() {
    if (isset($_POST['idContador']) && isset($_POST['editarNomContador'])) {
      $contadorId = intval($_POST['idContador']);
      
      // Obtener comercio del primer cliente seleccionado (para compatibilidad con campo legacy)
      $comercioLegacy = '';
      $comercioIds = isset($_POST['comercioIdsEditar']) ? $_POST['comercioIdsEditar'] : '';
      
      if (!empty($comercioIds)) {
        $idsArray = array_filter(explode(',', $comercioIds));
        if (!empty($idsArray)) {
          try {
            $stmt = Conexion::conectar()->prepare("SELECT empresa FROM clientes WHERE id = :id LIMIT 1");
            $stmt->bindValue(':id', intval($idsArray[0]), PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
              $comercioLegacy = $row['empresa'];
            }
          } catch (Exception $e) {
            error_log('ctrEditarContador: Error obteniendo empresa - ' . $e->getMessage());
          }
        }
      }
      
      $datos = [
        'id' => $contadorId,
        'comercio' => $comercioLegacy,
        'nom_contador' => $_POST['editarNomContador'] ?? '',
        'titular_tlf' => $_POST['editarTitularTlf'] ?? '',
        'telefono' => $_POST['editarTelefono'] ?? '',
        'telefono_actu' => $_POST['editarTelefonoActu'] ?? '',
        'link' => $_POST['editarLink'] ?? '',
        'usuario' => $_POST['editarUsuario'] ?? '',
        'contrasena' => $_POST['editarContrasena'] ?? ''
      ];

      $tabla = 'contador';
      $respuesta = ModeloContador::mdlEditarContador($tabla, $datos);
      
      if ($respuesta == 'ok') {
        // Actualizar comercios en la tabla pivote
        if (!empty($comercioIds)) {
          $idsArray = array_filter(explode(',', $comercioIds));
          ModeloContador::mdlAsignarClientes($contadorId, $idsArray);
        } else {
          // Si no hay comercios, limpiar asignaciones
          ModeloContador::mdlAsignarClientes($contadorId, []);
        }
        
        echo '<script>Swal.fire({icon:"success", title:"Contador actualizado", showConfirmButton:true}).then(()=>{window.location="contadores";});</script>';
      } else {
        echo '<script>Swal.fire({icon:"error", title:"Error al actualizar", showConfirmButton:true});</script>';
      }
    }
  }

  public function ctrEliminarContador() {
    if (isset($_GET['idContadorEliminar'])) {
      $id = intval($_GET['idContadorEliminar']);
      $tabla = 'contador';
      $respuesta = ModeloContador::mdlEliminarContador($tabla, $id);
      if ($respuesta == 'ok') {
        echo '<script>Swal.fire({icon:"success", title:"Contador eliminado", showConfirmButton:true}).then(()=>{window.location="contadores";});</script>';
      } else {
        echo '<script>Swal.fire({icon:"error", title:"Error al eliminar", showConfirmButton:true});</script>';
      }
    }
  }

}
