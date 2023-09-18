<?php
require_once 'app/cors.php';
require_once 'app/request.php';
require_once 'models/personasModel.php';

class PersonasController
{
    private $cors;

    public function __construct()
    {
        $this->cors = new Cors();
    }

    public function guardarPersona(Request $request)
    {
        $this->cors->corsJson();
        $data = $request->input('persona');
        $response = [];

        if (!isset($data) || $data == null) {
            $response = [
                'status' => false,
                'mensaje' => 'No hay datos para procesar',
                'persona' => null,
            ];
        } else {
            $response = $this->guardandoDatos($data);
        }
        return $response;
    }

    private function guardandoDatos($data)
    {
        //validar la cedula que no se repita
        $existePersona = Personas::where('cedula', $data->cedula)->get()->first();
        $response = [];

        if ($existePersona == null) {
            $persona = new Personas();
            //seteando campos o rellenando el modelo
            $persona->cedula = $data->cedula;
            $persona->nombre = ucfirst($data->nombre);
            $persona->apellido = ucfirst($data->apellido);
            $persona->celular = $data->celular;
            $persona->direccion = ucfirst($data->direccion);
            $persona->estado = 'A';

            if ($persona->save()) {
                $response = [
                    'status' => true,
                    'mensaje' => 'Se ha guardado la persona',
                    'persona' => $persona,
                ];
            } else {
                $response = [
                    'status' => false,
                    'mensaje' => 'No se pudo guardar la persona',
                    'persona' => null,
                ];
            }
        } else {
            $response = [
                'status' => false,
                'mensaje' => 'La persona ya se encuentra registrado',
                'persona' => $existePersona,
            ];
        }
        return $response;
    }
}
