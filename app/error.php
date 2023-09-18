<?php

require_once 'app/cors.php';

class ErrorClass
{

    private $default = "404";
    private $mensaje = "No existe el recurso";

    public function __construct()
    {
    }

    public static function e($codigo, $mensaje)
    {
        $cors = new Cors();
        $cors->corsJson();

        $array = [
            'codigo' => $codigo,
            'mensaje' => $mensaje,
        ];

        echo json_encode($array);
    }
}
