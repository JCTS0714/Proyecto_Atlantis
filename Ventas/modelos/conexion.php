<?php
class Conexion{
    static public function conectar(){
        $link = new PDO("mysql:host=localhost;dbname=atlantisbd;charset=utf8mb4",
                         "root",
                         "");
        $link->exec("set names utf8mb4");

        return $link;
    }
}