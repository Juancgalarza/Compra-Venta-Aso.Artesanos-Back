<?php
require_once 'app/cors.php';
require_once 'app/request.php';
require_once 'app/error.php';
require_once 'models/rolesModel.php';

class RolesController
{
    private $cors;

    public function __construct()
    {
        $this->cors = new Cors();
    }

    public function selectRol()
    {
        $this->cors->corsJson();
        $dataRol = Roles::where('estado', 'A')->get();
        $response = [];

        if (count($dataRol) > 0) {
            $response = [
                'status' => true,
                'message' => 'existen datos',
                'rol' => $dataRol,
            ];
        } else {
            $response = [
                'status' => false,
                'message' => 'no existen datos',
                'rol' => null,
            ];
        }
        echo json_encode($response);
    }

    public function selectRolSinCliente()
    {
        $this->cors->corsJson();
        $rolCliente = 3;
        $dataRol = Roles::where('id', '<>', $rolCliente)->where('estado', 'A')->get();
        $response = [];

        if (count($dataRol) > 0) {
            $response = [
                'status' => true,
                'message' => 'existen datos',
                'rol' => $dataRol,
            ];
        } else {
            $response = [
                'status' => false,
                'message' => 'no existen datos',
                'rol' => null,
            ];
        }
        echo json_encode($response);
    }
}