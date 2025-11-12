<?php

require_once "conexion.php";

class ModeloProveedores {

    /**============================
    * MÉTODO PARA MOSTRAR PROVEEDORES
    * ============================
    */
    static public function mdlMostrarProveedores($tabla, $item, $valor) {
        if ($item != null) {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
            $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(); // Nos retorna solo una fila
        } else {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla");
            $stmt->execute();
            return $stmt->fetchAll(); // Nos retorna todas las filas de la tabla
        }
         $stmt = null;
    }

    /**============================
    * MÉTODO PARA REGISTRAR PROVEEDORES
    * ============================
    */
    static public function mdlIngresarProveedor($tabla, $datos) {
        try {
            $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(id_usuarios, razon_social, ruc, direccion, telefono, tipo_proveedor) 
            VALUES(:id_usuarios, :razon_social, :ruc, :direccion, :telefono, :tipo_proveedor)");


            $stmt->bindParam(":razon_social", $datos["razon_social"], PDO::PARAM_STR);
            $stmt->bindParam(":ruc", $datos["ruc"], PDO::PARAM_STR);
            $stmt->bindParam(":direccion", $datos["direccion"], PDO::PARAM_STR);
            $stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
            $stmt->bindParam(":tipo_proveedor", $datos["tipo_proveedor"], PDO::PARAM_STR);
            $stmt->bindParam(":id_usuarios", $datos["id_usuarios"], PDO::PARAM_STR);

            if ($stmt->execute()) {
                $stmt->closeCursor();
                $stmt = null;
                return "ok";
            } else {
                print_r($stmt->errorInfo()); // DEBUG: Muestra el error de SQL si la consulta falla
                $stmt->closeCursor();
                $stmt = null;
                return "error";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return "error";
        }
    }
    static public function mdlEditarProveedor($tabla, $datos) {
    $stmt = Conexion::conectar()->prepare(
        "UPDATE $tabla SET razon_social = :razon_social, ruc = :ruc, direccion = :direccion, telefono = :telefono, tipo_proveedor = :tipo_proveedor WHERE id = :id"
    );
    $stmt->bindParam(":razon_social", $datos["razon_social"], PDO::PARAM_STR);
    $stmt->bindParam(":ruc", $datos["ruc"], PDO::PARAM_STR);
    $stmt->bindParam(":direccion", $datos["direccion"], PDO::PARAM_STR);
    $stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
    $stmt->bindParam(":tipo_proveedor", $datos["tipo_proveedor"], PDO::PARAM_STR);
    $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);

    if ($stmt->execute()) {
        $stmt = null;
        return "ok";
    } else {
        $stmt = null;
        return "error";
    }
}

static public function mdlEliminarProveedor($tabla, $id) {
    $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        $stmt = null;
        return "ok";
    } else {
        $stmt = null;
        return "error";
    }
 }
}
