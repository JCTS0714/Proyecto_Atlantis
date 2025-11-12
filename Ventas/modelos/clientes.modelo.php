<?php

require_once "conexion.php";

class ModeloCliente{

  /**============================
   * MÉTODO PARA MOSTRAR CLIENTES
   * ============================
   */
  static public function MdlMostrarCliente($tabla,$item,$valor){

    if($item != null)
    {
      if ($item == "nombre") {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item LIKE :$item ORDER BY fecha_creacion DESC");
        $valor = "%".$valor."%";
      } else {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY fecha_creacion DESC");
      }

      $stmt->bindParam(":".$item,$valor,PDO::PARAM_STR);

      $stmt->execute();

      return $stmt->fetchAll(); /**Nos retorna todas las filas que coinciden */
    }
    else
    {
      $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY fecha_creacion DESC");

      $stmt->execute();

      return $stmt->fetchAll(); /**Nos retorna toda las filas de la tabla */
    }
    
    $stmt -> close();

    $stmt=null;

  }

  /** ============================
   * MÉTODO PARA ACTUALIZAR CLIENTE
   * ============================
   */
  static public function mdlActualizarCliente($tabla, $item1, $valor1, $item2, $valor2) {

    $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET $item1 = :$item1 WHERE $item2 = :$item2");
    $stmt->bindParam(":".$item1, $valor1, PDO::PARAM_STR);
    $stmt->bindParam(":".$item2, $valor2, PDO::PARAM_STR);

    if ($stmt->execute()) {
      return "ok";
    } else {
      return "error";
    }
  }

  /* Nuevo método para obtener clientes para oportunidades */
  static public function mdlMostrarClientesParaOportunidad($searchTerm = null) {
    if ($searchTerm) {
      $stmt = Conexion::conectar()->prepare("SELECT id, nombre FROM clientes WHERE nombre LIKE :nombre ORDER BY nombre ASC");
      $likeTerm = "%".$searchTerm."%";
      $stmt->bindParam(":nombre", $likeTerm, PDO::PARAM_STR);
    } else {
      $stmt = Conexion::conectar()->prepare("SELECT id, nombre FROM clientes ORDER BY nombre ASC");
    }
    $stmt->execute();
    return $stmt->fetchAll();
    $stmt->closeCursor();
    $stmt = null;
  }

  /* MÉTODO PARA REGISTRAR CLIENTE */
  static public function mdlRegistrarCliente($tabla,$datos){
    $stmt = Conexion::conectar()->prepare("INSERT INTO clientes(nombre, tipo, documento, telefono, correo, ciudad, migracion, referencia, fecha_contacto, empresa, estado) VALUES(:nombre, :tipo, :documento, :telefono, :correo, :ciudad, :migracion, :referencia, :fecha_contacto, :empresa, :estado)");

    $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
    $stmt->bindParam(":tipo", $datos["tipo"], PDO::PARAM_STR);
    $stmt->bindParam(":documento", $datos["documento"], PDO::PARAM_STR);
    $stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
    $stmt->bindParam(":correo", $datos["correo"], PDO::PARAM_STR);
    $stmt->bindParam(":ciudad", $datos["ciudad"], PDO::PARAM_STR);
    $stmt->bindParam(":migracion", $datos["migracion"], PDO::PARAM_STR);
    $stmt->bindParam(":referencia", $datos["referencia"], PDO::PARAM_STR);
    $stmt->bindParam(":fecha_contacto", $datos["fecha_contacto"], PDO::PARAM_STR);
    $stmt->bindParam(":empresa", $datos["empresa"], PDO::PARAM_STR);
    $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);

    if($stmt->execute()){
      return "ok";
    }
    else{
      return "error";
    }
    $stmt->closeCursor();
    $stmt = null;
  }

  /* MÉTODO PARA EDITAR CLIENTE */
  static public function mdlEditarCliente($tabla,$datos){
    $stmt = Conexion::conectar()->prepare("UPDATE clientes SET nombre = :nombre, tipo = :tipo, documento = :documento, telefono = :telefono, correo = :correo, ciudad = :ciudad, migracion = :migracion, referencia = :referencia, fecha_contacto = :fecha_contacto, empresa = :empresa, fecha_creacion = :fecha_creacion WHERE id = :id");

    $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
    $stmt->bindParam(":tipo", $datos["tipo"], PDO::PARAM_STR);
    $stmt->bindParam(":documento", $datos["documento"], PDO::PARAM_STR);
    $stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
    $stmt->bindParam(":correo", $datos["correo"], PDO::PARAM_STR);
    $stmt->bindParam(":ciudad", $datos["ciudad"], PDO::PARAM_STR);
    $stmt->bindParam(":migracion", $datos["migracion"], PDO::PARAM_STR);
    $stmt->bindParam(":referencia", $datos["referencia"], PDO::PARAM_STR);
    $stmt->bindParam(":fecha_contacto", $datos["fecha_contacto"], PDO::PARAM_STR);
    $stmt->bindParam(":empresa", $datos["empresa"], PDO::PARAM_STR);
    $stmt->bindParam(":fecha_creacion", $datos["fecha_creacion"], PDO::PARAM_STR);
    $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);

    if($stmt->execute()){
      return "ok";
    }
    else{
      return "error";
    }
    $stmt->closeCursor();
    $stmt = null;
  }

  /* MÉTODO PARA ELIMINAR CLIENTE */
  static public function mdlEliminarCliente($tabla, $id){
    $stmt = Conexion::conectar()->prepare("DELETE FROM clientes WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
      return "ok";
    } else {
      return "error";
    }
    $stmt->closeCursor();
    $stmt = null;
  }

  /* MÉTODO PARA VERIFICAR SI UN CLIENTE TIENE OPORTUNIDADES */
  static public function mdlVerificarOportunidades($idCliente) {
    $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as total FROM oportunidades WHERE cliente_id = :cliente_id");
    $stmt->bindParam(":cliente_id", $idCliente, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'] > 0;
  }

  /* MÉTODO PARA MOSTRAR CLIENTES FILTRADOS */
  static public function mdlMostrarClientesFiltrados($tabla, $filtros = []) {
    $where = [];
    $params = [];
    $joins = [];

    // Construir condiciones WHERE basadas en filtros
    if (!empty($filtros['nombre'])) {
      $where[] = "c.nombre LIKE :nombre";
      $params[':nombre'] = '%' . $filtros['nombre'] . '%';
    }

    if (!empty($filtros['tipo'])) {
      $where[] = "c.tipo = :tipo";
      $params[':tipo'] = $filtros['tipo'];
    }

    if (!empty($filtros['documento'])) {
      $where[] = "c.documento LIKE :documento";
      $params[':documento'] = '%' . $filtros['documento'] . '%';
    }

    if (!empty($filtros['telefono'])) {
      $where[] = "c.telefono LIKE :telefono";
      $params[':telefono'] = '%' . $filtros['telefono'] . '%';
    }

    if (!empty($filtros['correo'])) {
      $where[] = "c.correo LIKE :correo";
      $params[':correo'] = '%' . $filtros['correo'] . '%';
    }

    if (!empty($filtros['ciudad'])) {
      $where[] = "c.ciudad LIKE :ciudad";
      $params[':ciudad'] = '%' . $filtros['ciudad'] . '%';
    }

    if (!empty($filtros['migracion'])) {
      $where[] = "c.migracion LIKE :migracion";
      $params[':migracion'] = '%' . $filtros['migracion'] . '%';
    }

    if (!empty($filtros['referencia'])) {
      $where[] = "c.referencia LIKE :referencia";
      $params[':referencia'] = '%' . $filtros['referencia'] . '%';
    }

    if (!empty($filtros['empresa'])) {
      $where[] = "c.empresa LIKE :empresa";
      $params[':empresa'] = '%' . $filtros['empresa'] . '%';
    }

    // Filtros de fecha
    if (!empty($filtros['fechaContactoDesde']) && !empty($filtros['fechaContactoHasta'])) {
      $where[] = "c.fecha_contacto BETWEEN :fechaContactoDesde AND :fechaContactoHasta";
      $params[':fechaContactoDesde'] = $filtros['fechaContactoDesde'];
      $params[':fechaContactoHasta'] = $filtros['fechaContactoHasta'];
    } elseif (!empty($filtros['fechaContactoDesde'])) {
      $where[] = "c.fecha_contacto >= :fechaContactoDesde";
      $params[':fechaContactoDesde'] = $filtros['fechaContactoDesde'];
    } elseif (!empty($filtros['fechaContactoHasta'])) {
      $where[] = "c.fecha_contacto <= :fechaContactoHasta";
      $params[':fechaContactoHasta'] = $filtros['fechaContactoHasta'];
    }

    if (!empty($filtros['estado'])) {
      $where[] = "c.estado = :estado";
      $params[':estado'] = $filtros['estado'];
    }

    // Filtros de otras tablas
    if (!empty($filtros['tieneOportunidades'])) {
      if ($filtros['tieneOportunidades'] == 'si') {
        $where[] = "o.id IS NOT NULL";
      } elseif ($filtros['tieneOportunidades'] == 'no') {
        $where[] = "o.id IS NULL";
      }
      $joins[] = "LEFT JOIN oportunidades o ON c.id = o.cliente_id";
    }

    if (!empty($filtros['tieneIncidencias'])) {
      if ($filtros['tieneIncidencias'] == 'si') {
        $where[] = "i.id IS NOT NULL";
      } elseif ($filtros['tieneIncidencias'] == 'no') {
        $where[] = "i.id IS NULL";
      }
      $joins[] = "LEFT JOIN incidencias i ON c.id = i.cliente_id";
    }

    if (!empty($filtros['estadoOportunidad'])) {
      $where[] = "o.estado = :estadoOportunidad";
      $params[':estadoOportunidad'] = $filtros['estadoOportunidad'];
      $joins[] = "LEFT JOIN oportunidades o ON c.id = o.cliente_id";
    }

    // Construir consulta SQL con JOINs
    $sql = "SELECT DISTINCT c.* FROM $tabla c";
    if (!empty($joins)) {
      $sql .= " " . implode(" ", array_unique($joins));
    }
    if (!empty($where)) {
      $sql .= " WHERE " . implode(" AND ", $where);
    }
    $sql .= " ORDER BY c.fecha_creacion DESC";

    $stmt = Conexion::conectar()->prepare($sql);

    // Bind parameters
    foreach ($params as $key => $value) {
      $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
