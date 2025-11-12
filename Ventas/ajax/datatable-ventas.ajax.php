<?php
require_once "../controladores/productos.controlador.php";
require_once "../modelos/productos.modelo.php";

class TablaProductosVentas{
    //**=========================================
     //* MOSTRAR LA TABLA DE PRODUCTOS 
    //**=========================================
        
    public function mostrarTablaProductosVentas()
    {
        $item = null;
        $valor = null;
        $productos = ControladorProductos::ctrMostrarProductos($item, $valor);
        
        $datosJson = '{
        "data": [';

        for($i = 0; $i < count($productos); $i++ ){
            //**TRAEMOS LAS IMAGENES */
            $imagen ="<img src='" . $productos[$i]["imagen"] . "' width='40px'>";
            //**TRAEMOS EL STOCK (CANTIDAD)*/
            //**APLICAMOS CONDICIONES SEGUN LA CANTIDAD DEL STOCK*/
            if ($productos[$i]["stock"] <= 10) {
            $stock = "<button class='btn btn-danger'>". $productos[$i]["stock"] . "</button>";
            } else if ($productos[$i]["stock"] > 11 && $productos[$i]["stock"] <= 20) {

            $stock = "<button class='btn btn-warning'>". $productos[$i]["stock"] . "</button>";

            } else {

            $stock = "<button class='btn btn-success'>". $productos[$i]["stock"] . "</button>";

            }

            //**TRAEMOS LAS ACCIONES */
            $botones ="<div class='btn-group'><button class='btn btn-primary btnAgregarProductos' idProducto='".$productos[$i]["id"]."'>Agregar</button></div>";

            $datosJson .= '[
                "'.($i + 1).'",
                "'.$imagen.'",
                "'.$productos[$i]["codigo"].'",
                "'.$productos[$i]["descripcion"].'",
                "'.$stock.'",
                "'.$botones.'"
            ],';//ESTA COMA
        }

        $datosJson = substr($datosJson,0,-1); // Elimina la última coma
        $datosJson .= ']
        }';
        echo $datosJson; // Devuelve el JSON generado
    }
}
//**ACTIVAR LA TABLA PRODUCTOS */
$activarProductosVentas = new TablaProductosVentas();
$activarProductosVentas->mostrarTablaProductosVentas();

