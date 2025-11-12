<?php
require_once "conexion.php";
class ModeloVentas
{
    /**============================
     * MÉTODO PARA MOSTRAR VENTAS
     * ============================
     */
    static public function mdlMostrarVentas($tabla, $item, $valor)
    {
        if ($item != null) {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY fecha DESC");
            // Prepara la consulta SQL para buscar un registro específico
            $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(); // Retorna una sola fila
        } else {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY fecha DESC");
            $stmt->execute();
            return $stmt->fetchAll(); // Retorna todas las filas de la tabla
        }
        $stmt->close();
        $stmt = null;
    }
}
