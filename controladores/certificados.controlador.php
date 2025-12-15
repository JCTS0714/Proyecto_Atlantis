<?php
require_once __DIR__ . '/../modelos/certificados.modelo.php';

class ControladorCertificados{

  static public function ctrMostrarCertificados($item = null, $valor = null){
    $tabla = 'certificados';
    $respuesta = ModeloCertificados::mdlMostrarCertificados($tabla, $item, $valor);
    return $respuesta;
  }

  static public function ctrCrearCertificado(){
    if(isset($_POST['nombre'])){
      $datos = [
        'nombre'=>$_POST['nombre'],
        'ruc'=>$_POST['ruc'] ?? '',
        'usuario'=>$_POST['usuario'] ?? '',
        'clave'=>$_POST['clave'] ?? '',
        'fecha_creacion'=>$_POST['fecha_creacion'] ?: date('Y-m-d'),
        'fecha_vencimiento'=>$_POST['fecha_vencimiento'] ?? null,
        'estado'=>$_POST['estado'] ?? 'activo',
        'observacion'=>$_POST['observacion'] ?? '',
        'creado_por'=> $_SESSION['id'] ?? 0
      ];
      $tabla = 'certificados';
      return ModeloCertificados::mdlCrearCertificado($tabla, $datos);
    }
  }

  static public function ctrEditarCertificado(){
    if(isset($_POST['id'])){
      $tabla = 'certificados';
      return ModeloCertificados::mdlEditarCertificado($tabla, $_POST);
    }
  }

  static public function ctrEliminarCertificado(){
    if(isset($_POST['id'])){
      $tabla = 'certificados';
      return ModeloCertificados::mdlEliminarCertificado($tabla, intval($_POST['id']));
    }
  }

}
