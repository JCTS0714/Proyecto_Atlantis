<?php



require_once "conexion.php";



class ModeloCategoria{



  /**============================

  * MÉTODO PARA MOSTRAR USUARIOS

  * ============================

  */

 static public function MdlMostrarCategoria($tabla,$item,$valor){



  if($item != null)

  {

   $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");



   $stmt->bindParam(":".$item,$valor,PDO::PARAM_STR);



   $stmt->execute();



   return $stmt->fetch(); /**Nos retorna solo una fila */

  }

  else

  {

   $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla");



   $stmt->execute();



   return $stmt->fetchAll(); /**Nos retorna toda las filas de la tabla */

  }

  

  $stmt -> close();



  $stmt=null;



 }

 /*MÉTODO PARA REGISTRAR USUARIOS*/

 static public function mdlRegistrarCategoria($tabla,$datos){

  $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(categoria) VALUES(:categoria)");

  $stmt->bindParam(":categoria",$datos["categoria"],PDO::PARAM_STR);

  if($stmt->execute()){

   return "ok";

  }

  else{

   return "error";

  }

  $stmt->close();

  $stmt = null;

 }

 static public function mdlEliminarCategoria($tabla, $id){

 $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id = :id");

 $stmt->bindParam(":id", $id, PDO::PARAM_INT);



 if ($stmt->execute()) {

  return "ok";

 } else {

  return "error";

 }

 $stmt->close();

  $stmt = null;

}
static public function mdlEditarCategoria($tabla, $datos){
    $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET categoria = :categoria WHERE id = :id");
    $stmt->bindParam(":categoria", $datos["categoria"], PDO::PARAM_STR);
    $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);

    if($stmt->execute()){
        
        return "ok";
    } else {
       
        return "error";
    }
     $stmt->close();

    $stmt = null;
  }
}