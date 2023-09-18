<?php
require_once 'app/cors.php';
require_once 'app/request.php';
require_once 'app/error.php';
require_once 'models/configuracionesModel.php';

class ConfiguracionesController
{
    private $cors;

    public function __construct()
    {
        $this->cors = new Cors();
    }

    public function listarConfiguracionesxId($params)
    {
        $this->cors->corsJson();
        $id = intval($params['id']);
        $configuracion = Configuraciones::find($id);
        $response = [];
    
        if ($configuracion) {
            $response = [
                'status' => true,
                'mensaje' => 'hay datos',
                'configuracion' => $configuracion,
            ];
        } else {
            $response = [
                'status' => false,
                'mensaje' => 'no hay datos',
                'configuracion' => null,
            ];
        }
        echo json_encode($response);
    }
}