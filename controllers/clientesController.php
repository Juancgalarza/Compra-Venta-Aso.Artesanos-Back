<?php
require_once 'app/cors.php';
require_once 'app/request.php';
require_once 'app/error.php';
require_once 'models/clientesModel.php';
require_once 'models/usuariosModel.php';
require_once 'controllers/personasController.php';

class ClientesController
{
    private $cors;
    private $personaCntr;

    public function __construct()
    {
        $this->cors = new Cors();
        $this->personaCntr = new PersonasController();
    }

    public function listarTable()
    {
        $this->cors->corsJson();
        $clientes = Clientes::where('estado', 'A')->orderBy('id', 'Desc')->get();
        $data = [];  $i = 1;

        foreach ($clientes as $cl) {
            $botones = '<div class="btn-group">
                            <button class="btn bg-purple btn-sm" onclick="editarCliente(' . $cl->id . ')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-dark btn-sm" onclick="eliminarCliente(' . $cl->id . ')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>';
            $data[] = [
                0 => $i,
                1 => $cl->personas->cedula,
                2 => $cl->personas->nombre,
                3 => $cl->personas->apellido,
                4 => $cl->personas->celular,
                5 => $cl->personas->direccion,
                6 => $botones,
            ];
            $i++;
        }
        $result = [
            'sEcho' => 1,
            'iTotalRecords' => count($data),
            'iTotalDisplayRecords' => count($data),
            'aaData' => $data,
        ];

        echo json_encode($result);
    }

    public function guardar(Request $request)
    {
        $this->cors->corsJson();
        $usuarioRequest = $request->input('usuario');
        $dataPersona = $this->personaCntr->guardarPersona($request);
        $objectPersona = (object) $dataPersona;
        $response = [];

        if ($objectPersona->status) {
            $nuevoCliente = new Clientes();
            $nuevoCliente->personas_id = $objectPersona->persona->id;
            $nuevoCliente->estado = 'A';

            $encriptar = hash('sha256', $objectPersona->persona->cedula);

            $nuevoUsuario = new Usuarios();
            $nuevoUsuario->roles_id = 3;
            $nuevoUsuario->personas_id = $objectPersona->persona->id;
            $nuevoUsuario->usuario = $usuarioRequest->usuario;
            $nuevoUsuario->correo = $usuarioRequest->correo;
            $nuevoUsuario->clave = $encriptar;
            $nuevoUsuario->conf_clave = $encriptar;
            $nuevoUsuario->imagen = $usuarioRequest->imagen;
            $nuevoUsuario->estado = 'A';

            if ($nuevoCliente->save()) {
                $nuevoUsuario->save();

                $response = [
                    'status' => true,
                    'mensaje' => 'El cliente se guardo correctamente',
                    'cliente' => $nuevoCliente,
                ];
            } else {
                $response = [
                    'status' => false,
                    'mensaje' => 'No se puede guardar el cliente',
                    'cliente' => null,
                ];
            }
        } else {
            $response = [
                'status' => false,
                'mensaje' => $objectPersona->mensaje,
                'cliente' => null,
            ];
        }
        echo json_encode($response);
    } 

    public function subirFoto($file)
    {
        $this->cors->corsJson();
        $img = $file['fichero'];
        $path = 'resources/clientes/';
        $response = Helper::save_file($img, $path);
        echo json_encode($response);
    }

    public function listar()
    {
        $this->cors->corsJson();
        $clientes = Clientes::where('estado','A')->get();
        $response = [];
        foreach($clientes as $cli){
            $response[] = [
                'cliente' =>$cli,
                'persona_id' =>$cli->personas->id,
            ];
        }
        echo json_encode($response);
    }

    public function listarClienteDataTableVenta()
    {
        $this->cors->corsJson();
        $clientes = Clientes::where('estado', 'A')->orderBy('id', 'Desc')->get();
        $data = [];  $i = 1;

        foreach ($clientes as $cl) {
            $botones = '<div>  
                            <button class="btn btn-dark btn-sm" onclick="seleccionarCliente(' . $cl->id . ')">
                                <i class="fas fa-check"></i>
                            </button>
                        </div>';

            $data[] = [
                0 => $i,
                1 => $cl->personas->cedula,
                2 => $cl->personas->nombre,
                3 => $cl->personas->apellido,
                4 => $cl->personas->celular,
                5 => $cl->personas->direccion,
                6 => $botones,
            ];
            $i++;
        }
        $result = [
            'sEcho' => 1,
            'iTotalRecords' => count($data),
            'iTotalDisplayRecords' => count($data),
            'aaData' => $data,
        ];
        echo json_encode($result);
    }

    public function listarxId($params)
    {
        $this->cors->corsJson();
        $id = intval($params['id']);
        $clientes = Clientes::find($id);
        $response = [];

        if($clientes){
            $clientes->personas;
            
            $response = [
                'status' => true,
                'mensaje' => 'Existen datos',
                'cliente' => $clientes
            ];
        }else{
            $response = [
                'status' => false,
                'mensaje' => 'No existen datos',
                'cliente' => $clientes
            ];
        }
        echo json_encode($response);
    }

    public function eliminarCliente(Request $request)
    {
        $this->cors->corsJson();
        $clienteRequest = $request->input('cliente');
        $id = $clienteRequest->id;
        $dataCliente = Clientes::find($id);

        if ($clienteRequest) {
            if ($dataCliente) {
                $dataCliente->estado = 'I';

                $dataPersona = Personas::find($dataCliente->personas_id);
                $dataPersona->estado = 'I';
                $dataPersona->save();
                $dataCliente->save();

                $response = [
                    'status' => true,
                    'mensaje' => "Se ha eliminado el cliente",
                ];
            } else {
                $response = [
                    'status' => false,
                    'mensaje' => "No se puede eliminar el cliente",
                ];
            }
        } else {
            $response = [
                'status' => false,
                'mensaje' => 'No hay datos',
            ];
        }
        echo json_encode($response);
    }

    public function editarCliente(Request $request)
    {
        $this->cors->corsJson();
        $clienteRequest = $request->input('cliente');
        $id = intval($clienteRequest->id);
        $persona_id = intval($clienteRequest->personas_id); 
        
        $dataCliente = Clientes::find($id); 
        $response = [];

        if($clienteRequest){
            if($dataCliente){ 
                $dataCliente->personas_id = $persona_id; 

                $dataPersona = Personas::find($dataCliente->personas_id);
                $dataPersona->nombre = ucfirst($clienteRequest->nombre);
                $dataPersona->apellido = ucfirst($clienteRequest->apellido);
                $dataPersona->celular = $clienteRequest->celular;
                $dataPersona->direccion = ucfirst($clienteRequest->direccion);
                $dataPersona->save();
                $dataCliente->save();
                
                $response = [
                    'status' => true,
                    'mensaje' => 'El cliente se ha actualizado correctamente',
                    'cliente' => $dataCliente,
                ];
            }else {
                $response = [
                    'status' => false,
                    'mensaje' => 'No se puede actualizar el cliente',
                ];
            }
        }else{
            $response = [
                'status' => false,
                'mensaje' => 'No hay datos ',
            ];
        }
        echo json_encode($response);
    }

    public function contar()
    {
        $this->cors->corsJson();
        $dataCliente = Clientes::where('estado', 'A')->get();
        $response = [];

        if ($dataCliente) {
            $response = [
                'status' => true,
                'mensaje' => 'existe datos',
                'modelo' => 'Clientes',
                'cantidad' => $dataCliente->count(),
            ];
        } else {
            $response = [
                'status' => false,
                'mensaje' => 'no existe datos',
                'modelo' => 'Usuario',
                'cantidad' => 0,
            ];
        }
        echo json_encode($response);

    }
}