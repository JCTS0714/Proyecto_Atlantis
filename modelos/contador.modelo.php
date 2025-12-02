<?php

require_once "conexion.php";

class ModeloContador {

  static public function mdlMostrarContador($tabla, $item, $valor) {
    if ($item != null) {
      $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC");
      $stmt->bindParam(":".$item, $valor, PDO::PARAM_STR);
      try {
        $stmt->execute();
        return $stmt->fetchAll();
      } catch (PDOException $e) {
        error_log("mdlMostrarContador ERROR: " . $e->getMessage());
        return [];
      }
    } else {
      $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");
      try {
        $stmt->execute();
        return $stmt->fetchAll();
      } catch (PDOException $e) {
        error_log("mdlMostrarContador(ALL) ERROR: " . $e->getMessage());
        return [];
      }
    }
    $stmt = null;
  }

  static public function mdlRegistrarContador($tabla, $datos) {
    // Calcular nro automáticamente si no fue provisto
    if (empty($datos['nro'])) {
      try {
        $maxStmt = Conexion::conectar()->prepare("SELECT MAX(CAST(nro AS UNSIGNED)) AS maxn FROM $tabla");
        $maxStmt->execute();
        $row = $maxStmt->fetch(PDO::FETCH_ASSOC);
        $next = ($row && $row['maxn']) ? intval($row['maxn']) + 1 : 1;
        $datos['nro'] = (string)$next;
      } catch (PDOException $e) {
        error_log('mdlRegistrarContador (calc nro) EXCEPTION: ' . $e->getMessage());
        $datos['nro'] = '1';
      }
    }

    $sql = "INSERT INTO $tabla(nro, comercio, nom_contador, titular_tlf, telefono, telefono_actu, link, usuario, contrasena) VALUES (:nro, :comercio, :nom_contador, :titular_tlf, :telefono, :telefono_actu, :link, :usuario, :contrasena)";
    $stmt = Conexion::conectar()->prepare($sql);
    $stmt->bindParam(':nro', $datos['nro'], PDO::PARAM_STR);
    $stmt->bindParam(':comercio', $datos['comercio'], PDO::PARAM_STR);
    $stmt->bindParam(':nom_contador', $datos['nom_contador'], PDO::PARAM_STR);
    $stmt->bindParam(':titular_tlf', $datos['titular_tlf'], PDO::PARAM_STR);
    $stmt->bindParam(':telefono', $datos['telefono'], PDO::PARAM_STR);
    $stmt->bindParam(':telefono_actu', $datos['telefono_actu'], PDO::PARAM_STR);
    $stmt->bindParam(':link', $datos['link'], PDO::PARAM_STR);
    $stmt->bindParam(':usuario', $datos['usuario'], PDO::PARAM_STR);
    $stmt->bindParam(':contrasena', $datos['contrasena'], PDO::PARAM_STR);

    try {
      if ($stmt->execute()) {
        return 'ok';
      } else {
        $err = $stmt->errorInfo();
        error_log('mdlRegistrarContador ERROR: ' . json_encode($err));
        return 'error';
      }
    } catch (PDOException $e) {
      error_log('mdlRegistrarContador EXCEPTION: ' . $e->getMessage());
      return 'error';
    }
  }

  static public function mdlEditarContador($tabla, $datos) {
    // No se permite modificar el campo 'nro' en la edición; conservar valor existente
    $sql = "UPDATE $tabla SET comercio = :comercio, nom_contador = :nom_contador, titular_tlf = :titular_tlf, telefono = :telefono, telefono_actu = :telefono_actu, link = :link, usuario = :usuario, contrasena = :contrasena WHERE id = :id";
    $stmt = Conexion::conectar()->prepare($sql);
    $stmt->bindParam(':comercio', $datos['comercio'], PDO::PARAM_STR);
    $stmt->bindParam(':nom_contador', $datos['nom_contador'], PDO::PARAM_STR);
    $stmt->bindParam(':titular_tlf', $datos['titular_tlf'], PDO::PARAM_STR);
    $stmt->bindParam(':telefono', $datos['telefono'], PDO::PARAM_STR);
    $stmt->bindParam(':telefono_actu', $datos['telefono_actu'], PDO::PARAM_STR);
    $stmt->bindParam(':link', $datos['link'], PDO::PARAM_STR);
    $stmt->bindParam(':usuario', $datos['usuario'], PDO::PARAM_STR);
    $stmt->bindParam(':contrasena', $datos['contrasena'], PDO::PARAM_STR);
    $stmt->bindParam(':id', $datos['id'], PDO::PARAM_INT);

    try {
      if ($stmt->execute()) {
        return 'ok';
      } else {
        $err = $stmt->errorInfo();
        error_log('mdlEditarContador ERROR: ' . json_encode($err));
        return 'error';
      }
    } catch (PDOException $e) {
      error_log('mdlEditarContador EXCEPTION: ' . $e->getMessage());
      return 'error';
    }
  }

  static public function mdlEliminarContador($tabla, $id) {
    // Primero eliminar relaciones en tabla pivote
    try {
      $stmtPivot = Conexion::conectar()->prepare("DELETE FROM contadores_clientes WHERE contador_id = :id");
      $stmtPivot->bindParam(':id', $id, PDO::PARAM_INT);
      $stmtPivot->execute();
    } catch (PDOException $e) {
      error_log('mdlEliminarContador (pivote) WARNING: ' . $e->getMessage());
      // Continuar aunque falle la tabla pivote (puede no existir aún)
    }

    $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    try {
      if ($stmt->execute()) {
        return 'ok';
      } else {
        $err = $stmt->errorInfo();
        error_log('mdlEliminarContador ERROR: ' . json_encode($err));
        return 'error';
      }
    } catch (PDOException $e) {
      error_log('mdlEliminarContador EXCEPTION: ' . $e->getMessage());
      return 'error';
    }
  }

  /**
   * Asignar clientes a un contador (tabla pivote)
   * @param int $contadorId
   * @param array $clienteIds - Array de IDs de clientes
   */
  static public function mdlAsignarClientes($contadorId, $clienteIds) {
    $pdo = Conexion::conectar();
    
    try {
      // Eliminar asignaciones anteriores
      $stmtDelete = $pdo->prepare("DELETE FROM contadores_clientes WHERE contador_id = :contador_id");
      $stmtDelete->bindParam(':contador_id', $contadorId, PDO::PARAM_INT);
      $stmtDelete->execute();
      
      // Insertar nuevas asignaciones
      if (!empty($clienteIds)) {
        $stmtInsert = $pdo->prepare("INSERT INTO contadores_clientes (contador_id, cliente_id) VALUES (:contador_id, :cliente_id)");
        foreach ($clienteIds as $clienteId) {
          if (!empty($clienteId) && is_numeric($clienteId)) {
            $stmtInsert->bindParam(':contador_id', $contadorId, PDO::PARAM_INT);
            $stmtInsert->bindValue(':cliente_id', intval($clienteId), PDO::PARAM_INT);
            $stmtInsert->execute();
          }
        }
      }
      
      return 'ok';
    } catch (PDOException $e) {
      error_log('mdlAsignarClientes EXCEPTION: ' . $e->getMessage());
      return 'error';
    }
  }

  /**
   * Obtener clientes asignados a un contador
   * @param int $contadorId
   * @return array - Array de clientes con id y empresa
   */
  static public function mdlObtenerClientesContador($contadorId) {
    try {
      $stmt = Conexion::conectar()->prepare(
        "SELECT c.id, c.empresa, c.nombre 
         FROM clientes c 
         INNER JOIN contadores_clientes cc ON c.id = cc.cliente_id 
         WHERE cc.contador_id = :contador_id 
         ORDER BY c.empresa ASC"
      );
      $stmt->bindParam(':contador_id', $contadorId, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log('mdlObtenerClientesContador EXCEPTION: ' . $e->getMessage());
      return [];
    }
  }

  /**
   * Buscar clientes por empresa (para Select2)
   * @param string $termino - Término de búsqueda
   * @return array
   */
  static public function mdlBuscarClientesPorEmpresa($termino = '') {
    try {
      if (!empty($termino)) {
        $stmt = Conexion::conectar()->prepare(
          "SELECT id, empresa, nombre 
           FROM clientes 
           WHERE empresa LIKE :termino AND empresa IS NOT NULL AND empresa != ''
           ORDER BY empresa ASC 
           LIMIT 20"
        );
        $like = '%' . $termino . '%';
        $stmt->bindParam(':termino', $like, PDO::PARAM_STR);
      } else {
        $stmt = Conexion::conectar()->prepare(
          "SELECT id, empresa, nombre 
           FROM clientes 
           WHERE empresa IS NOT NULL AND empresa != ''
           ORDER BY empresa ASC 
           LIMIT 50"
        );
      }
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log('mdlBuscarClientesPorEmpresa EXCEPTION: ' . $e->getMessage());
      return [];
    }
  }

  /**
   * Mostrar contadores con sus comercios relacionados
   */
  static public function mdlMostrarContadoresConComercios() {
    try {
      $stmt = Conexion::conectar()->prepare(
        "SELECT ct.*, 
                GROUP_CONCAT(c.empresa SEPARATOR ', ') as comercios_lista,
                GROUP_CONCAT(c.id SEPARATOR ',') as comercios_ids
         FROM contadores ct
         LEFT JOIN contadores_clientes cc ON ct.id = cc.contador_id
         LEFT JOIN clientes c ON cc.cliente_id = c.id
         GROUP BY ct.id
         ORDER BY ct.id DESC"
      );
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log('mdlMostrarContadoresConComercios EXCEPTION: ' . $e->getMessage());
      return [];
    }
  }

}

?>
